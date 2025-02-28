<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index() // Method names should typically be lowercase
    {
        $categories = Category::all();
        return inertia('Admin/Categories/Index', ['categories' => $categories]);
    }

    public function create()
    {
        return inertia('Admin/Categories/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:categories,name', // Specify the column for uniqueness
        ]);

        Category::create(['name' => $request->name]);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.'); // Use route name
    }

    public function edit(Category $category)
    {
        return inertia('Admin/Categories/Edit', ['category' => $category]);
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|unique:categories,name,' . $category->id, // Correct uniqueness validation
        ]);

        $category->update(['name' => $request->name]);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.'); // Use route name
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.'); // Use route name
    }
}
