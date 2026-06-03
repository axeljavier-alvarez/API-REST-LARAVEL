<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
class PostController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('auth:api', except: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::getOrPaginate();
        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data =$request->validate([
            'title' => 'required',
            'slug' => 'required',
            'excerpt' => 'required',
            'body' => 'required',
            'image' => 'nullable|image',
            'is_published' => 'nullable|boolean',
            'category_id' => 'required|exists:categories,id',
        ]);
        $data['user_id'] = auth('api')->id();
        // return $data;
        $post = Post::create($data);
        return PostResource::make($post);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return PostResource::make($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => 'required',
            'slug' => 'required|unique:posts,slug,' . $post->id,
            'excerpt' => 'required',
            'body' => 'required',
            'image' => 'nullable|image',
            'is_published' => 'nullable|boolean',
            'category_id' => 'required|exists:categories,id',
        ]);

        // 1. Guarda los cambios reales en tu base de datos
        $post->update($data);

        // 2. Devuelve la respuesta formateada con tu Resource transformado
        return PostResource::make($post);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();

      
        return response()->json([
            'message' => 'Post eliminado correctamente'
        ]);

    }
}
