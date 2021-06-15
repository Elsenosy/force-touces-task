<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\GeneralController;
use App\Http\Requests\Api\StoreCommentRequest;
use App\Http\Requests\Api\StorePostRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Notifications\NewCommentAdded;

class PostController extends GeneralController
{
    private $imagePath = 'posts';

    // Initialize the constructor
    public function __construct(Post $model) {
        $this->model = $model;
    }

    // Return user posts
    public function index(){
        $user = $this->userApi();
        $posts = $user->posts;
        
        // Check if there is posts found 
        if($posts->isEmpty()){
            return $this->returnError('No posts found!');
        }

        return $this->returnSuccess('Posts', PostResource::collection($user->posts));
    }

    public function show($id){
        $post = $this->userApi()->posts()->find($id);

        // Check if exists
        if(!$post){
            return $this->returnError('Post not found!');
        }

        return $this->returnSuccess('Post retrieved', new PostResource($post));
    }

    /**
     * Store Post
     * param: App\Http\Requests\Api\StorePostRequest
     * response: App\Http\Resources\PostResource $post created
     */
    public function store(StorePostRequest $request){

        // Get the validated data
        $data = $request->validated();

        // Get user 
        $user = $this->userApi();

        // Upload the image
        if($request->hasFile('image')){
            $data['image'] = $this->uploadImage($request->file('image'), $this->imagePath);
        }

        // Save the data 
        $post = $user->posts()->create($data);

        return $this->returnSuccess('Saved successfully', new PostResource($post));
    }

    
    /**
     * Store Post comment
     * param: App\Http\Requests\Api\StoreCommentRequest
     * response: App\Http\Resources\CommentResource $comment created
     */
    public function storeComment(StoreCommentRequest $request){

        // Get the validated data
        $data = $request->validated();

        // Get user 
        $user = $this->userApi();
        $data['user_id'] = $user->id;

        // Get post 
        $post = Post::find($data['post_id']);

        // Save the data 
        $comment = $post->comments()->create($data);

        // Send notification to the owner
        $post->owner->notify(new NewCommentAdded($user->name));

        return $this->returnSuccess('Saved successfully', new CommentResource($comment));
    }


}
