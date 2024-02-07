<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function allCategories()
    {
        return response()->json(Category::all());
    }

    public function createCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);

        $category = new Category;
        $category->name = $request->name;
        $category->slug = Str::slug($request->name, '-');
        $category->save();
        return response()->json(
            ['message' => 'Category created', 'category' => $category],
            201
        );
    }

    public function updateCategory(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'name' => 'required|max:255',
        ]);

        $category = Category::find($request->id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->name, '-');
        $category->save();

        return response()->json(['message' => 'Category updated', 'category' => $category], 200);
    }

    public function deleteCategory(Request $request)
    {
        $category = Category::find($request->id);
        $category->delete();
        return response()->json(['message' => 'Category deleted']);
    }

    public function getCategoryBySlug($slug)
    {
        $category = Category::where('slug', $slug)->first();
        return response()->json($category);
    }
}
