<?php

namespace App\Http\Controllers;

use App\Events\PostCreated;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Post::query();

        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }
        if ($request->has('body')) {
            $query->where('body', 'like', '%' . $request->body . '%');
        }
        if ($request->has('author_id')) {
            $query->where('author_id', $request->author_id);
        }
        if ($request->has('created_at')) {
            $query->whereDate('created_at', $request->created_at);
        }
        if ($request->has('updated_at')) {
            $query->whereDate('updated_at', $request->updated_at);
        }

        if ($request->has('sort_by')) {
            $sortDirection = $request->get('sort_direction', 'asc');
            $query->orderBy($request->sort_by, $sortDirection);
        }

        $posts = $query->with('author')->paginate($request->get('per_page', 5));

        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $post = Post::create($request->validated());

        event(new PostCreated($post));
        return new PostResource($post);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Post::with('author')->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, string $id)
    {
        $post = Post::findOrFail($id);
        $post->update($request->validated());
        return response()->json($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::findOrFail($id);
        $post->delete();
        return response()->json(['message' => 'Пост успешно удален']);
    }
}
