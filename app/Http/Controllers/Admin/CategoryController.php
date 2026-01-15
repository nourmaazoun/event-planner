<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('events')->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        // Vérifier si c'est une requête AJAX
        if (!$request->ajax() && !$request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request type'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $category = Category::create([
            'name' => $request->name
        ]);

        // Charger le count des événements
        $category->loadCount('events');

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully!',
            'category' => $category
        ], 201);
    }

    public function update(Request $request, Category $category)
    {
        // Vérifier si c'est une requête AJAX
        if (!$request->ajax() && !$request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request type'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $category->update([
            'name' => $request->name
        ]);

        // Recharger le count
        $category->loadCount('events');

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully!',
            'category' => $category
        ]);
    }

    public function destroy(Category $category)
    {
        // Vérifier si la catégorie a des événements
        if ($category->events()->count() > 0) {
            return back()->with('error', 'Cannot delete category with events.');
        }

        $category->delete();
        return back()->with('success', 'Category deleted successfully!');
    }
}