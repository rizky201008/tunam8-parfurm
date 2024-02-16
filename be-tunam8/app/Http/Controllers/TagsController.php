<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    public function allTags()
    {
        return response()->json(Tag::all());
    }

    public function createTag(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $tag = new Tag;
        $tag->name = $request->name;
        $tag->slug = Str::slug($request->name, '-');
        $tag->save();

        return response()->json($tag);
    }

    public function updateTag(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'id' => 'required|integer|exists:tags,id',
        ]);
        $tag = Tag::find($request->id);
        $tag->name = $request->name;
        $tag->slug = Str::slug($request->name, '-');
        $tag->save();

        return response()->json($tag);
    }

    public function deleteTag(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:tags,id',
        ]);
        $tag = Tag::find($request->id);
        $tag->delete();

        return response()->json($tag);
    }

    public function showTag(Request $request)
    {
        $tag = Tag::find($request->id);
        return response()->json($tag);
    }

    public function showTagBySlug(Request $request)
    {
        $tag = Tag::where('slug', $request->slug)->first();
        return response()->json($tag);
    }

    public function showTagProducts(Request $request)
    {
        $tag = Tag::find($request->id);
        return response()->json($tag->products);
    }
}
