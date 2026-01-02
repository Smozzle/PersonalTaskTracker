<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Productivity Report - {{ date('Y-m-d') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #1e293b;
            line-height: 1.6;
        }

        .container {
            padding: 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #667eea;
        }

        .header h1 {
            font-size: 28px;
            color: #667eea;
            margin-bottom: 10px;
        }

        .header p {
            color: #64748b;
            font-size: 14px;
        }

        .report-period {
            text-align: center;
            background: #f1f5f9;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
            font-weight: 600;
            color: #475569;
        }

        .section {
            margin-bottom: 40px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }

        .metric-box {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #e2e8f0;
        }

        .metric-value {
            font-size: 32px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 5px;
        }

        .metric-label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
        }

        .metric-detail {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table thead {
            background: #f8fafc;
        }

        table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
            font-size: 11px;
        }

        table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }

        table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .category-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 10px;
            color: white;
        }

        .progress-bar-container {
            width: 100%;
            height: 20px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            background: #10b981;
            text-align: center;
            color: white;
            font-size: 10px;
            line-height: 20px;
            font-weight: 600;
        }

        .summary-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .summary-item:last-child {
            border-bottom: none;
        }

        .summary-label {
            font-weight: 600;
            color: #475569;
        }

        .summary-value {
            color: #1e293b;
            font-weight: 700;
        }

        .highlight-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 30px;
        }

        .highlight-value {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .highlight-label {
            font-size: 16px;
            opacity: 0.9;
        }

        .goal-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .goal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .goal-title {
            font-weight: 600;
            color: #1e293b;
            font-size: 13px;
        }

        .goal-status {
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .goal-status.completed {
            background: #d1fae5;
            color: #065f46;
        }

        .goal-status.ongoing {
            background: #dbeafe;
            color: #1e40af;
        }

        .goal-status.pending {
            background: #fef3c7;
            color: #92400e;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            color: #94a3b8;
            font-size: 10px;
        }

        .page-break {
            page-break-after: always;
        }

        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>📊 Productivity Report</h1>
            <p>Comprehensive analysis of your task management performance</p>
        </div>

        <!-- Report Period -->
        <div class="report-period">
            Report Period: {{ $startDate->format('F d, Y') }} - {{ $endDate->format('F d, Y') }}
            ({{ $period }} days)
        </div>

        <!-- Productivity Score Highlight -->
        <div class="highlight-box">
            <div class="highlight-value">{{ $productivityScore }}</div>
            <div class="highlight-label">Overall Productivity Score</div>
        </div>

        <!-- Key Metrics -->
        <div class="section">
            <h2 class="section-title">📈 Key Performance Metrics</h2>
            <div class="metrics-grid">
                <div class="metric-box">
                    <div class="metric-value">{{ $totalTasks }}</div>
                    <div class="metric-label">Total Tasks</div>
                </div>
                <div class="metric-box">
                    <div class="metric-value">{{ $completedTasks }}</div>
                    <div class="metric-label">Completed</div>
                </div>
                <div class="metric-box">
                    <div class="metric-value">{{ $completionRate }}%</div>
                    <div class="metric-label">Completion Rate</div>
                </div>
                <div class="metric-box">
                    <div class="metric-value">{{ $achievements }}</div>
                    <div class="metric-label">Achievements</div>
                </div>
            </div>
        </div>

        <!-- Summary Box -->
        <div class="section">
            <h2 class="section-title">📋 Executive Summary</h2>
            <div class="summary-box">
                <div class="summary-item">
                    <span class="summary-label">Report Period</span>
                    <span class="summary-value">{{ $period }} Days</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Total Tasks Created</span>
                    <span class="summary-value">{{ $totalTasks }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Tasks Completed</span>
                    <span class="summary-value">{{ $completedTasks }} ({{ $completionRate }}%)</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Active Goals</span>
                    <span class="summary-value">{{ $goals->count() }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Goals Completed</span>
                    <span class="summary-value">{{ $goals->where('status', 'Completed')->count() }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Total Achievements</span>
                    <span class="summary-value">{{ $achievements }}</span>
                </div>
            </div>
        </div>

        <!-- Category Performance -->
        <div class="section">
            <h2 class="section-title">📁 Category Performance Analysis</h2>
            <table>
                <thead>
                    <tr>
                        <th>Category</th>
                        <th style="text-align: center;">Total Tasks</th>
                        <th style="text-align: center;">Completed</th>
                        <th style="text-align: center;">Completion Rate</th>
                        <th>Progress</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categoryData as $cat)
                    <tr>
                        <td>
                            <span class="category-badge" style="background: {{ $cat['color'] }};">
                                {{ $cat['name'] }}
                            </span>
                        </td>
                        <td style="text-align: center;">{{ $cat['count'] }}</td>
                        <td style="text-align: center;">{{ $cat['completed'] }}</td>
                        <td style="text-align: center;"><strong>{{ $cat['completionRate'] }}%</strong></td>
                        <td>
                            <div class="progress-bar-container">
                                <div class="progress-bar-fill" style="width: {{ $cat['completionRate'] }}%; background: {{ $cat['color'] }};">
                                    {{ $cat['completionRate'] }}%
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Page Break -->
        <div class="page-break"></div>

        <!-- Goals Overview -->
        <div class="section">
            <h2 class="section-title">🎯 Goals Overview</h2>
            
            <div class="two-column">
                <div class="summary-box">
                    <div class="summary-item">
                        <span class="summary-label">Total Goals</span>
                        <span class="summary-value">{{ $goals->count() }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Completed</span>
                        <span class="summary-value" style="color: #10b981;">
                            {{ $goals->where('status', 'Completed')->count() }}
                        </span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">In Progress</span>
                        <span class="summary-value" style="color: #3b82f6;">
                            {{ $goals->where('status', 'Ongoing')->count() }}
                        </span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Pending</span>
                        <span class="summary-value" style="color: #f59e0b;">
                            {{ $goals->where('status', 'Pending')->count() }}
                        </span>
                    </div>
                </div>

                <div class="summary-box">
                    <div style="text-align: center; padding: 20px;">
                        <div style="font-size: 48px; font-weight: 700; color: #667eea; margin-bottom: 10px;">
                            {{ $goals->count() > 0 ? round($goals->avg('progress'), 1) : 0 }}%
                        </div>
                        <div style="color: #64748b; font-weight: 600;">Average Goal Progress</div>
                    </div>
                </div>
            </div>

            <h3 style="margin-top: 30px; margin-bottom: 15px; font-size: 14px; color: #475569;">Active Goals</h3>
            @foreach($goals->take(10) as $goal)
            <div class="goal-item">
                <div class="goal-header">
                    <span class="goal-title">{{ $goal->title }}</span>
                    <span class="goal-status {{ strtolower($goal->status) }}">{{ $goal->status }}</span>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar-fill" style="width: {{ $goal->progress }}%;">
                        {{ $goal->progress }}%
                    </div>
                </div>
                @if($goal->target_date)
                <div style="margin-top: 8px; font-size: 10px; color: #64748b;">
                    Target Date: {{ \Carbon\Carbon::parse($goal->target_date)->format('M d, Y') }}
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Performance Insights -->
        <div class="section">
            <h2 class="section-title">💡 Performance Insights</h2>
            <div class="summary-box">
                <div style="padding: 15px;">
                    <h3 style="color: #667eea; margin-bottom: 15px; font-size: 14px;">Key Highlights:</h3>
                    <ul style="margin-left: 20px; color: #475569;">
                        <li style="margin-bottom: 10px;">
                            <strong>Completion Rate:</strong> You completed {{ $completionRate }}% of your tasks during this period
                            @if($completionRate >= 80)
                                - Excellent performance! 🎉
                            @elseif($completionRate >= 60)
                                - Good progress! Keep it up! 👍
                            @else
                                - Room for improvement. Focus on task completion! 💪
                            @endif
                        </li>
                        <li style="margin-bottom: 10px;">
                            <strong>Most Active Category:</strong> 
                            @if($categoryData->count() > 0)
                                {{ $categoryData->first()['name'] }} ({{ $categoryData->first()['count'] }} tasks)
                            @else
                                No category data available
                            @endif
                        </li>
                        <li style="margin-bottom: 10px;">
                            <strong>Goal Achievement:</strong> 
                            @if($goals->count() > 0)
                                {{ round(($goals->where('status', 'Completed')->count() / $goals->count()) * 100, 1) }}% of your goals have been completed
                            @else
                                No goals set yet
                            @endif
                        </li>
                        <li style="margin-bottom: 10px;">
                            <strong>Productivity Score:</strong> {{ $productivityScore }}/100
                            @if($productivityScore >= 80)
                                - Outstanding! 🏆
                            @elseif($productivityScore >= 60)
                                - Very Good! 🌟
                            @elseif($productivityScore >= 40)
                                - Good effort! 💫
                            @else
                                - Let's improve together! 💪
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Recommendations -->
        <div class="section">
            <h2 class="section-title">🎯 Recommendations</h2>
            <div class="summary-box">
                <div style="padding: 15px;">
                    <ul style="margin-left: 20px; color: #475569;">
                        @if($completionRate < 70)
                        <li style="margin-bottom: 10px;">
                            Focus on completing pending tasks to improve your completion rate
                        </li>
                        @endif
                        
                        @if($goals->where('status', 'Pending')->count() > 0)
                        <li style="margin-bottom: 10px;">
                            Start working on {{ $goals->where('status', 'Pending')->count() }} pending goal(s) to maintain momentum
                        </li>
                        @endif
                        
                        @if($categoryData->count() > 0 && $categoryData->where('completionRate', '<', 50)->count() > 0)
                        <li style="margin-bottom: 10px;">
                            Some categories need attention - allocate more time to lower-performing areas
                        </li>
                        @endif
                        
                        <li style="margin-bottom: 10px;">
                            Set realistic daily targets to maintain consistent progress
                        </li>
                        
                        <li style="margin-bottom: 10px;">
                            Review and update your goals regularly to stay aligned with priorities
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Generated on {{ date('F d, Y') }} at {{ date('h:i A') }}</p>
            <p>This is an automated report from your Task Management System</p>
        </div>
    </div>
</body>
</html>