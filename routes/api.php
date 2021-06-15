<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


/**
 * Api Auth routes
 */
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
});

// Posts and comments
Route::group(['prefix' => 'posts', 'middleware' => 'auth:api'], function(){
    $controller = 'PostController@';
    Route::get('/', $controller.'index'); // Get user posts
    Route::get('/{id}', $controller.'show'); // Get a post
    Route::post('/', $controller.'store'); // Make new post
    Route::post('/make-comment', $controller.'storeComment'); //  make post comment
});