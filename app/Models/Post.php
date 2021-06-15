<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['user_id', 'caption', 'image'];

    /**
     * Relationships
     */
    
    // Post comments
    public function comments(){
        return $this->hasMany(Comment::class, 'post_id');
    }

    // post owner
    public function owner(){
        return $this->belongsTo(User::class, 'user_id');
    }
    
}
