<?php

namespace App\Http\Controllers;

use App\Helper\JwtAuth;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Middleware\apiAuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Post;
use App\Helper\ResponseApi;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends BaseController
{
    public function __construct()
    {
        $this->middleware(apiAuthMiddleware::class, [
            'except' => [
                'index', 
                'show', 
                'getImage', 
                'getPostsByCategory',
                'getPostsByUser'
            ]
        ]);
    }

    public function index()
    {
        $posts = Post::all()->load('category', 'user');

        return ResponseApi::jsonSuccess(200, ['posts' => $posts]);
    }

    public function show($id)
    {
        $post = Post::find($id)->load('category', 'user');

        if (!$post) {
            return ResponseApi::jsonError(404, ['errors' => ['Post not found']]);
        }

        return ResponseApi::jsonSuccess(200, ['post' => $post]);
    }

    public function store(Request $request)
    {
        $postData = json_decode($request->input('post_data', null), true);

        if (empty($postData)) {
            return ResponseApi::jsonError(404, ['errors' => ['Post data format error']]);
        }

        $rules = [
            'title' => 'required',
            'content' => 'required',
            'category_id' => 'required',
            'image' => 'required'
        ];
        $validate = Validator::make($postData, $rules); 

        if ($validate->fails()) { 
            return ResponseApi::jsonError(400, $validate->errors()->all());
        }

        $jwtAuth = new JwtAuth();
        $userData = $jwtAuth->getUserInformation($request->header('Authorization', null));
        
        $post = new Post();

        $post->user_id = $userData->sub;
        $post->title = $postData['title'];
        $post->content = $postData['content'];
        $post->category_id = $postData['category_id'];
        $post->image = $postData['image'];
        $post->save();

        return ResponseApi::jsonSuccess(200, ['post' => $post]);
    }

    public function update(int $id, Request $request)
    {
        $postData = json_decode($request->input('post_data', null), true);

        if (empty($postData)) {
            return ResponseApi::jsonError(404, ['errors' => ['Post data format error']]);
        }

        $rules = [
            'title' => 'required',
            'content' => 'required',
            'category_id' => 'required',
            'image' => 'required'
        ];
        $validate = Validator::make($postData, $rules); 

        if ($validate->fails()) {
            return ResponseApi::jsonError(400, $validate->errors()->all());
        }

        $jwtAuth = new JwtAuth();
        $userData = $jwtAuth->getUserInformation($request->header('Authorization', null));
        
        $post = Post::where(['id' => $id, 'user_id' => $userData->sub])->first();

        if (empty($post)) {
            return ResponseApi::jsonError(400, ['errors' => ['Post not found'], 'post' => $post]);
        }

        $postUpdateData = [
            'title' => $postData['title'],
            'content' => $postData['content'],
            'category_id' => $postData['category_id'],
            'image' => $postData['image']
        ];

        $post->update($postUpdateData);

        return ResponseApi::jsonSuccess(200, ['category' => $post]);
    }

    public function destroy($id, Request $request)
    {
        $jwtAuth = new JwtAuth();
        $userData = $jwtAuth->getUserInformation($request->header('Authorization', null));
        
        $post = Post::where(['id' => $id, 'user_id' => $userData->sub])->first();

        if (empty($post)) {
            return ResponseApi::jsonError(400, ['errors' => ['Post not found']]);
        }

        $post->delete();

        return ResponseApi::jsonSuccess(200, ['post' => $post]);
    }

    public function upload(Request $request)
    {
        $image = $request->file('file0');

        if (!$image) {
            return ResponseApi::jsonError(400, ['errors' => ['Image not found']]);
        }
        $rules = [
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
        ];

        $validate = Validator::make($request->all(), $rules);

        if ($validate->fails()) {
            return ResponseApi::jsonError(400, $validate->errors()->all());
        }

        $imageName = time().$image->getClientOriginalName();
        Storage::disk('images')->put($imageName, File::get($image));

        return ResponseApi::jsonSuccess(200, ['imageName' => $imageName]);
    }

    public function getImage(string $filename = null)
    {
        if (!$filename) {
            return ResponseApi::jsonError(400, ['errors' => ['Image name is required.']]);
        }

        $isset = Storage::disk('images')->exists($filename);

        if (!$isset) {
            return ResponseApi::jsonError(400, ['errors' => ['Image not found.']]);
        }

        $file = Storage::disk('images')->get($filename);

        return new Response($file, 200);
    }

    public function getPostsByCategory(int $id)
    {
        $posts = Post::where('category_id', $id)->get();

        return ResponseApi::jsonSuccess(200, ['posts' => $posts]);
    }

    public function getPostsByUser(int $id)
    {
        $posts = Post::where('user_id', $id)->get();

        return ResponseApi::jsonSuccess(200, ['posts' => $posts]);
    }
}
