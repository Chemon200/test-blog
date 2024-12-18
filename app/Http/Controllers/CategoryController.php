<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use App\Http\Middleware\apiAuthMiddleware;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Helper\ResponseApi;
use Illuminate\Support\Facades\Validator;

class CategoryController extends BaseController
{
    public function __construct()
    {
        $this->middleware(apiAuthMiddleware::class, ['except' => ['index', 'show']]);
    }

    public function index()
    {
        $categories = Category::all();

        return ResponseApi::jsonSuccess(200, ['categories' => $categories]);
    }

    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return ResponseApi::jsonError(404, ['errors' => ['Category not found']]);
        }

        return ResponseApi::jsonSuccess(200, ['category' => $category]);
    }

    public function store(Request $request)
    {
        $categoryData = json_decode($request->input('category_data', null), true);

        if (empty($categoryData)) {
            return ResponseApi::jsonError(404, ['errors' => ['Category data format error']]);
        }

        $rules = [
            'name' => 'required'
        ];
        $validate = Validator::make($categoryData, $rules); 

        if ($validate->fails()) { //dump($validate->errors()->all()); die;
            return ResponseApi::jsonError(400, $validate->errors()->all());
        }

        $category = new Category();
        $category->name = $categoryData['name'];
        $category->save();

        return ResponseApi::jsonSuccess(200, ['category' => $category]);
    }

    public function update(int $id, Request $request)
    {
        $categoryData = json_decode($request->input('category_data', null), true);

        if (empty($categoryData)) {
            return ResponseApi::jsonError(404, ['errors' => ['Category data format error']]);
        }

        $rules = [
            'name' => 'required'
        ];
        $validate = Validator::make($categoryData, $rules); 

        if ($validate->fails()) { //dump($validate->errors()->all()); die;
            return ResponseApi::jsonError(400, $validate->errors()->all());
        }

        $categoryUpdate = Category::where('id', $id)->update(['name' => $categoryData['name']]);
        $category = Category::find($id);

        return ResponseApi::jsonSuccess(200, ['category' => $category]);
    }
}
