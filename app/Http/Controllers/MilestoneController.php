<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MilestoneController extends Controller
{
    /**
     * Store a new milestone
     */
    public function store(Request $request, Goal $goal)
    {
        // Check authorization
        if ($goal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_percentage' => 'required|integer|min:1|max:100',
        ]);

        // Get the next order number
        $lastOrder = $goal->milestones()->max('order');
        $validated['order'] = ($lastOrder ?? 0) + 1;
        $validated['goal_id'] = $goal->id;

        $milestone = Milestone::create($validated);

        // Update goal progress
        $goal->updateProgressFromMilestones();

        return response()->json([
            'success' => true,
            'milestone' => $milestone,
            'message' => 'Milestone created successfully!'
        ]);
    }

    /**
     * Toggle milestone completion
     */
    public function toggle(Milestone $milestone)
    {
        $goal = $milestone->goal;

        // Check authorization
        if ($goal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Toggle completion
        $milestone->is_completed = !$milestone->is_completed;
        $milestone->completed_at = $milestone->is_completed ? now() : null;
        $milestone->save();

        // Update goal progress
        $goal->updateProgressFromMilestones();

        return response()->json([
            'success' => true,
            'is_completed' => $milestone->is_completed,
            'completed_at' => $milestone->completed_at?->format('M d, Y'),
            'goal_progress' => $goal->progress,
            'goal_status' => $goal->status,
            'message' => $milestone->is_completed ? 'Milestone completed! 🎉' : 'Milestone marked as incomplete'
        ]);
    }

    /**
     * Update milestone
     */
    public function update(Request $request, Milestone $milestone)
    {
        $goal = $milestone->goal;

        // Check authorization
        if ($goal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_percentage' => 'required|integer|min:1|max:100',
        ]);

        $milestone->update($validated);

        // Update goal progress
        $goal->updateProgressFromMilestones();

        return response()->json([
            'success' => true,
            'milestone' => $milestone,
            'goal_progress' => $goal->progress,
            'message' => 'Milestone updated successfully!'
        ]);
    }

    /**
     * Delete milestone
     */
    public function destroy(Milestone $milestone)
    {
        $goal = $milestone->goal;

        // Check authorization
        if ($goal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $milestone->delete();

        // Update goal progress
        $goal->updateProgressFromMilestones();

        return response()->json([
            'success' => true,
            'goal_progress' => $goal->progress,
            'message' => 'Milestone deleted successfully!'
        ]);
    }

    /**
     * Reorder milestones
     */
    public function reorder(Request $request, Goal $goal)
    {
        // Check authorization
        if ($goal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'milestones' => 'required|array',
            'milestones.*.id' => 'required|exists:milestones,id',
            'milestones.*.order' => 'required|integer|min:0',
        ]);

        foreach ($validated['milestones'] as $milestoneData) {
            Milestone::where('id', $milestoneData['id'])
                ->update(['order' => $milestoneData['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Milestones reordered successfully!'
        ]);
    }
}