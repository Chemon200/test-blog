<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Post;

class pruebaController extends Controller
{
    public function index($name='pep')
    {
        $params = [
            'title' => 'Listado de animales by '.$name,
            'animals' => ['Perro', 'Gato', 'Hamster']
        ];

        return view('prueba/animals', $params);
    }

    public function testOrm() {
        $categories = Category::all();

        foreach ($categories as $category) {
            echo '<h2>'.$category->name.'</h2>';

            foreach ($category->posts as $post) {
                echo '<h2>'.$post->title.'</h2>';
                echo '<p>User: '.$post->user->name.' - Category:'.$post->category->name.'</p>';
            }

            echo '<hr/>';
        }

        die();
    }
}
