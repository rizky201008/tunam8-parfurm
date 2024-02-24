<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Logics\Tags;
use App\Models\Product;
use Illuminate\Http\Request;

class TagsController extends Controller
{

    protected $tag;
    protected $tagsLogic;

    public function __construct(Tag $tag = new Tag(), Tags $tagsLogic = new Tags())
    {
        $this->tag = $tag;
        $this->tagsLogic = $tagsLogic;
    }

    public function allTags()
    {
        return response()->json(
            $this->tag->paginate(10)
        );
    }

    public function createTag(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $tag = $this->tag;
        $tag->name = $request->name;
        $tag->slug = $this->tagsLogic->createSlug($request->name);
        $tag->save();

        return response()->json([
            'message' => 'Tag created successfully',
            'tag' => $tag
        ], 201);
    }

    public function updateTag(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'id' => 'required|integer|exists:tags,id',
        ]);
        $tag = $this->tag->find($request->id);
        $tag->name = $request->name;
        $tag->slug =  $this->tagsLogic->createSlug($request->name);
        $tag->save();

        return response()->json(
            [
                'message' => 'Tag updated successfully',
                'tag' => $tag
            ],
            200
        );
    }

    public function deleteTag(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:tags,id',
        ]);
        $tag = $this->tag->find($request->id);
        $tag->delete();

        return response()->json(['message' => 'Tag deleted successfully'], 200);
    }

    public function showTagProducts($slug)
    {
        $tag = $this->tag->where('slug', $slug)->first();
        return response()->json($tag->products->paginate(10));
    }

    public function getProductsByTag($tag)
    {
        $products = Product::with(['category', 'images'])->where('tags', 'LIKE',"%$tag%")->paginate(25);

        return response()->json($products);
    }
}
