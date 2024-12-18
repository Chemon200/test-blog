<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';

    protected $fillable = [
        'category_id', 'title', 'content', 'image'
    ];

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function category() {
        return $this->belongsTo('App\Models\Category', 'category_id');
    }
}
