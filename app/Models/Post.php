<?php

namespace App\Models;

use Framework\Database\Model;

class Post extends Model {
    protected $table = 'posts';
    protected $fillable = ['title', 'content', 'user_id', 'published_at'];
    protected $timestamps = true;
    protected $softDelete = true;

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function isPublished() {
        return $this->getAttribute('published_at') !== null;
    }
}
