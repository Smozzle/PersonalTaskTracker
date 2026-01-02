<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class GoalController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $goals = Goal::where('user_id', Auth::id())->get();
        return view('goals.index', compact('goals'));
    }

    public function create()
    {
        return view('goals.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'progress' => 'nullable|integer|min:0|max:100',
            'target_date' => 'nullable|date',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'Ongoing';

        Goal::create($validated);

        return redirect()->route('goals.index')
            ->with('success', 'Goal added successfully!');
    }

    public function edit(Goal $goal)
    {
        // Security check - make sure the goal belongs to the authenticated user
        if ($goal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('goals.edit', compact('goal'));
    }

    public function update(Request $request, Goal $goal)
    {
        if ($goal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->has('title')) {
            // Full update
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'target_date' => 'nullable|date',
                'progress' => 'required|integer|min:0|max:100',
                'status' => 'required|in:Ongoing,Completed',
            ]);
        } else {
            // Quick update
            $validated = $request->validate([
                'progress' => 'required|integer|min:0|max:100',
                'status' => 'required|in:Ongoing,Completed',
            ]);
        }

        // 🔥 Auto-set progress to 100 when completed
        if ($validated['status'] === 'Completed') {
            $validated['progress'] = 100;
        }

        $goal->update($validated);

        return redirect()->route('goals.index')->with('success', 'Goal updated successfully!');
    }

    public function destroy(Goal $goal)
    {
        // Security check
        if ($goal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $goal->delete();
        return back()->with('success', 'Goal deleted successfully.');
    }

    public function ajaxUpdate(Request $request, Goal $goal)
    {
        if ($goal->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'progress' => 'required|integer|min:0|max:100',
            'status' => 'required|in:Ongoing,Completed',
        ]);

        $goal->update($validated);

        return response()->json([
            'success' => true,
            'progress' => $goal->progress,
            'status' => $goal->status,
        ]);
    }
}