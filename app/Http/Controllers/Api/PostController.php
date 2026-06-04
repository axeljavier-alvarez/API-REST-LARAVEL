<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use App\Models\Tag;
use Illuminate\Support\Facades\Gate;
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

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('images', 'public');
        }
        $data['user_id'] = auth('api')->id();
        // return $data;
        $post = Post::create($data);
        return PostResource::make($post);
    }
    public function syncTags(Request $request, Post $post)
    {
        Gate::authorize('author', $post);
        $request->validate([
            'tags' => 'required|array|min:1'
        ]);

        $tags =[];

        foreach($request->tags as $tag){
            $tags[] = Tag::firstOrCreate([
                'name' => $tag,
            ])->id;
        }

        $post->tags()->sync($tags);
        // cargue las relaciones
        $post->load('tags');
        return PostResource::make($post);

        // return $tags;
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
        Gate::authorize('author', $post);
        // return "Paso validación";

        $data = $request->validate([
            'title' => 'required',
            'slug' => 'required|unique:posts,slug,' . $post->id,
            'excerpt' => 'required',
            'body' => 'required',
            'image' => 'nullable|image',
            'is_published' => 'nullable|boolean',
            'category_id' => 'required|exists:categories,id',
        ]);

        if($request->hasFile('image')){
            // Eliminar la imagen anterior si existe
            if($post->image_path){
                Storage::delete($post->image_path);
            }
            // $data['image_path'] = Storage::put('images', $request->file('image'));
            $data['image_path'] = $request->file('image')->store('images', 'public');
        }
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

        Gate::authorize('author', $post);
        $post->delete();

      
        return response()->json([
            'message' => 'Post eliminado correctamente'
        ]);

    }
}
