<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a list of categories and filtered tasks.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        // Fetch all categories belonging to the user
        $categories = Category::where('user_id', $userId)->get();

        // Get selected filters from query params
        $selectedCategory = $request->input('category_id');
        $selectedPriority = $request->input('priority');

        // Build the task query
        $tasksQuery = Task::where('user_id', $userId);

        if ($selectedCategory) {
            $tasksQuery->where('category_id', $selectedCategory);
        }

        if ($selectedPriority) {
            $tasksQuery->where('priority', $selectedPriority);
        }

        $tasks = $tasksQuery->get();

        // Return combined data to the view
        return view('categories.index', compact('categories', 'tasks', 'selectedCategory', 'selectedPriority'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color_code' => 'nullable|string|max:20',
        ]);

        // ✅ Normalize color format
        $color = $validated['color_code'] ?? null;
        if ($color) {
            $color = strtoupper(trim($color));
            if (strpos($color, '#') !== 0) {
                $color = '#' . $color;
            }
        } else {
            $color = '#6B7280'; // Default gray
        }

        $validated['color_code'] = $color;
        $validated['user_id'] = Auth::id();

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Category added successfully!');
    }

    /**
     * Show the form for editing a category.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update a category in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color_code' => 'nullable|string|max:20',
        ]);

        $color = $validated['color_code'] ?? null;

        if ($color) {
            $color = strtoupper(trim($color));
            if (strpos($color, '#') !== 0) {
                $color = '#' . $color;
            }
        } else {
            $color = '#6B7280';
        }

        $validated['color_code'] = $color;

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully!');
    }

    /**
     * Delete a category.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully!');
    }

    /**
     * Show all tasks under a specific category.
     */
    public function showTasks(Category $category)
    {
        // Ensure user owns this category
        if ($category->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        // Fetch related tasks
        $tasks = $category->tasks()->where('user_id', auth()->id())->get();

        return view('categories.tasks', compact('category', 'tasks'));
    }
}
