<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Goal;
use App\Models\Category;
use App\Models\Achievement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', '30');
        $reportType = $request->get('type', 'overview');
        $categoryFilter = $request->get('category');
        $priorityFilter = $request->get('priority');
        $userId = Auth::id();

        // Date range
        $endDate = Carbon::now();
        $startDate = $this->getStartDate($period);

        // Previous period for comparison
        $previousStartDate = $startDate->copy()->subDays($period);
        $previousEndDate = $startDate->copy();

        // Base query with filters
        $tasksQuery = Task::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($categoryFilter) {
            $tasksQuery->where('category_id', $categoryFilter);
        }
        if ($priorityFilter) {
            $tasksQuery->where('priority', $priorityFilter);
        }

        $tasks = $tasksQuery->get();

        // === CURRENT PERIOD DATA ===
        $completedTasks = $tasks->where('status', 'Completed')->count();
        $pendingTasks = $tasks->where('status', 'Pending')->count();
        $ongoingTasks = $tasks->where('status', 'Ongoing')->count();
        $totalTasks = $tasks->count();
        $overdueTasks = $tasks->where('status', '!=', 'Completed')
            ->filter(function ($task) {
                return $task->due_date && Carbon::parse($task->due_date)->isPast();
            })->count();

        // === PREVIOUS PERIOD DATA ===
        $previousTasks = Task::where('user_id', $userId)
            ->whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->get();

        $previousCompleted = $previousTasks->where('status', 'Completed')->count();
        $previousTotal = $previousTasks->count();

        // === CALCULATIONS ===
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;
        $previousCompletionRate = $previousTotal > 0 ? round(($previousCompleted / $previousTotal) * 100, 1) : 0;

        // Percentage changes
        $tasksChange = $previousTotal > 0 ? round((($totalTasks - $previousTotal) / $previousTotal) * 100, 1) : 0;
        $completionChange = $completionRate - $previousCompletionRate;

        // === AVERAGE COMPLETION TIME ===
        $avgCompletionTime = $this->calculateAvgCompletionTime($tasks->where('status', 'Completed'));

        // === DAILY TREND DATA ===
        $dailyData = $this->getDailyTrendData($userId, $startDate, $endDate);

        // === PEAK PRODUCTIVITY HOURS ===
        $peakHours = $this->getPeakProductivityHours($userId, $startDate, $endDate);

        // === CATEGORY BREAKDOWN ===
        $categoryData = $this->getCategoryBreakdown($userId, $startDate, $endDate);

        // === PRIORITY BREAKDOWN ===
        $priorityData = [
            'High' => $tasks->where('priority', 'High')->count(),
            'Medium' => $tasks->where('priority', 'Medium')->count(),
            'Low' => $tasks->where('priority', 'Low')->count(),
        ];

        // === GOALS DATA ===
        $goals = Goal::where('user_id', $userId)->get();
        $goalsCompleted = $goals->where('status', 'Completed')->count();
        $goalsOngoing = $goals->where('status', 'Ongoing')->count();
        $goalsPending = $goals->where('status', 'Pending')->count();
        $avgGoalProgress = $goals->count() > 0 ? round($goals->avg('progress'), 1) : 0;

        // Goals on track vs behind
        $goalsOnTrack = $goals->filter(function ($goal) {
            if ($goal->status === 'Completed')
                return true;
            $expectedProgress = $this->calculateExpectedProgress($goal);
            return $goal->progress >= $expectedProgress - 10;
        })->count();

        $goalsBehind = $goals->count() - $goalsOnTrack;

        // === ACHIEVEMENTS & STREAKS ===
        $achievements = Achievement::where('user_id', $userId)->count();
        $streakData = $this->getStreakData($userId);

        // === PRODUCTIVITY SCORE ===
        $productivityScore = $this->calculateProductivityScore(
            $completionRate,
            $totalTasks,
            $goalsCompleted,
            $achievements
        );

        $previousProductivityScore = $this->calculateProductivityScore(
            $previousCompletionRate,
            $previousTotal,
            0, // Simplified for comparison
            0
        );

        // === WEEKLY COMPARISON ===
        $weeklyComparison = $this->getWeeklyComparison($userId, $startDate, $endDate);

        // === ACTIVITY HEATMAP DATA ===
        $heatmapData = $this->getHeatmapData($userId, $startDate, $endDate);

        // === UPCOMING DEADLINES ===
        $upcomingDeadlines = Task::where('user_id', $userId)
            ->where('status', '!=', 'Completed')
            ->whereNotNull('due_date')
            ->where('due_date', '>=', Carbon::now())
            ->orderBy('due_date')
            ->take(5)
            ->get();

        // === CATEGORIES FOR FILTER ===
        $categories = Category::where('user_id', $userId)->get();

        return view('reports.index', compact(
            'period',
            'reportType',
            'totalTasks',
            'completedTasks',
            'pendingTasks',
            'ongoingTasks',
            'overdueTasks',
            'completionRate',
            'tasksChange',
            'completionChange',
            'avgCompletionTime',
            'dailyData',
            'peakHours',
            'categoryData',
            'priorityData',
            'goals',
            'goalsCompleted',
            'goalsOngoing',
            'goalsPending',
            'avgGoalProgress',
            'goalsOnTrack',
            'goalsBehind',
            'achievements',
            'streakData',
            'productivityScore',
            'previousProductivityScore',
            'weeklyComparison',
            'heatmapData',
            'upcomingDeadlines',
            'categories',
            'categoryFilter',
            'priorityFilter',
            'startDate',
            'endDate'
        ));
    }

    private function getStartDate($period)
    {
        if ($period === 'custom') {
            return Carbon::parse(request('start_date', Carbon::now()->subDays(30)));
        }
        return Carbon::now()->subDays((int) $period);
    }

    private function calculateAvgCompletionTime($completedTasks)
    {
        $times = [];
        foreach ($completedTasks as $task) {
            if ($task->completed_at && $task->created_at) {
                $times[] = Carbon::parse($task->created_at)
                    ->diffInHours(Carbon::parse($task->completed_at));
            }
        }

        return count($times) > 0 ? round(array_sum($times) / count($times), 1) : 0;
    }

    private function getDailyTrendData($userId, $startDate, $endDate)
    {
        $dailyData = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dayTasks = Task::where('user_id', $userId)
                ->whereDate('created_at', $currentDate)
                ->get();

            $dailyData[] = [
                'date' => $currentDate->format('M d'),
                'fullDate' => $currentDate->format('Y-m-d'),
                'completed' => $dayTasks->where('status', 'Completed')->count(),
                'created' => $dayTasks->count(),
                'pending' => $dayTasks->where('status', 'Pending')->count(),
            ];

            $currentDate->addDay();
        }

        return $dailyData;
    }

    private function getPeakProductivityHours($userId, $startDate, $endDate)
    {
        $hourlyData = [];

        $completedTasks = Task::where('user_id', $userId)
            ->where('status', 'Completed')
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->get();

        for ($hour = 0; $hour < 24; $hour++) {
            $count = $completedTasks->filter(function ($task) use ($hour) {
                return Carbon::parse($task->completed_at)->hour === $hour;
            })->count();

            $hourlyData[] = [
                'hour' => $hour,
                'label' => sprintf('%02d:00', $hour),
                'count' => $count
            ];
        }

        return $hourlyData;
    }

    private function getCategoryBreakdown($userId, $startDate, $endDate)
    {
        $categories = Category::where('user_id', $userId)
            ->with([
                'tasks' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            ])
            ->get();

        return $categories->map(function ($cat) {
            $tasks = $cat->tasks;
            $completed = $tasks->where('status', 'Completed')->count();
            $total = $tasks->count();

            return [
                'name' => $cat->name,
                'count' => $total,
                'completed' => $completed,
                'completionRate' => $total > 0 ? round(($completed / $total) * 100, 1) : 0,
                'color' => $cat->color_code ?? '#94a3b8'
            ];
        })->sortByDesc('count')->values();
    }

    private function getStreakData($userId)
    {
        $tasks = Task::where('user_id', $userId)
            ->where('status', 'Completed')
            ->orderBy('completed_at', 'desc')
            ->get();

        $currentStreak = 0;
        $longestStreak = 0;
        $tempStreak = 0;
        $lastDate = null;

        foreach ($tasks as $task) {
            if (!$task->completed_at)
                continue;

            $completedDate = Carbon::parse($task->completed_at)->startOfDay();

            if ($lastDate === null) {
                $tempStreak = 1;
                $currentStreak = 1;
            } elseif ($completedDate->diffInDays($lastDate) === 1) {
                $tempStreak++;
            } else {
                $longestStreak = max($longestStreak, $tempStreak);
                $tempStreak = 1;
            }

            $lastDate = $completedDate;
        }

        $longestStreak = max($longestStreak, $tempStreak);

        return [
            'current' => $currentStreak,
            'longest' => $longestStreak,
            'consistency' => $this->calculateConsistencyScore($userId)
        ];
    }

    private function calculateConsistencyScore($userId)
    {
        $days = 30;
        $activeDays = Task::where('user_id', $userId)
            ->where('status', 'Completed')
            ->where('completed_at', '>=', Carbon::now()->subDays($days))
            ->distinct()
            ->count(DB::raw('DATE(completed_at)'));

        return round(($activeDays / $days) * 100, 1);
    }

    private function getWeeklyComparison($userId, $startDate, $endDate)
    {
        $weeks = [];
        $currentWeek = $startDate->copy()->startOfWeek();

        while ($currentWeek <= $endDate) {
            $weekEnd = $currentWeek->copy()->endOfWeek();

            $weekTasks = Task::where('user_id', $userId)
                ->whereBetween('created_at', [$currentWeek, $weekEnd])
                ->get();

            $weeks[] = [
                'week' => $currentWeek->format('M d'),
                'completed' => $weekTasks->where('status', 'Completed')->count(),
                'total' => $weekTasks->count(),
            ];

            $currentWeek->addWeek();
        }

        return $weeks;
    }

    private function getHeatmapData($userId, $startDate, $endDate)
    {
        $heatmap = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dayTasks = Task::where('user_id', $userId)
                ->whereDate('created_at', $currentDate)
                ->where('status', 'Completed')
                ->count();

            $heatmap[] = [
                'date' => $currentDate->format('Y-m-d'),
                'day' => $currentDate->format('D'),
                'count' => $dayTasks,
                'intensity' => min(4, floor($dayTasks / 2))
            ];

            $currentDate->addDay();
        }

        return $heatmap;
    }

    private function calculateExpectedProgress($goal)
    {
        if (!$goal->target_date)
            return 0;

        $start = Carbon::parse($goal->created_at);
        $end = Carbon::parse($goal->target_date);
        $now = Carbon::now();

        if ($now >= $end)
            return 100;
        if ($now <= $start)
            return 0;

        $totalDays = $start->diffInDays($end);
        $daysPassed = $start->diffInDays($now);

        return round(($daysPassed / $totalDays) * 100, 1);
    }

    private function calculateProductivityScore($completionRate, $totalTasks, $goalsCompleted, $achievements)
    {
        $score = 0;

        // Completion rate (40%)
        $score += $completionRate * 0.4;

        // Task volume (30%)
        $score += min(30, $totalTasks * 2);

        // Goals completed (20%)
        $score += min(20, $goalsCompleted * 5);

        // Achievements (10%)
        $score += min(10, $achievements * 2);

        return min(100, round($score));
    }

    public function exportPdf(Request $request)
    {
        $period = $request->get('period', '30');
        $userId = Auth::id();

        $endDate = Carbon::now();
        $startDate = $this->getStartDate($period);

        $tasks = Task::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $completedTasks = $tasks->where('status', 'Completed')->count();
        $totalTasks = $tasks->count();
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;

        $categoryData = $this->getCategoryBreakdown($userId, $startDate, $endDate);
        $goals = Goal::where('user_id', $userId)->get();
        $achievements = Achievement::where('user_id', $userId)->count();

        $productivityScore = $this->calculateProductivityScore(
            $completionRate,
            $totalTasks,
            $goals->where('status', 'Completed')->count(),
            $achievements
        );

        $pdf = Pdf::loadView('reports.pdf', compact(
            'period',
            'startDate',
            'endDate',
            'totalTasks',
            'completedTasks',
            'completionRate',
            'categoryData',
            'goals',
            'achievements',
            'productivityScore'
        ));

        return $pdf->download('productivity-report-' . date('Y-m-d') . '.pdf');
    }
}