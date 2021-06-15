<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['user_id', 'post_id', 'body'];

    /**
     * Relations
     */
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
    
    // Post relationship
    public function post(){
        return $this->belongsTo(Post::class, 'post_id');
    }
     
}
