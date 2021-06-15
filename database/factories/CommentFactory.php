<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Comment;
use Faker\Generator as Faker;

$factory->define(Comment::class, function (Faker $faker) {
    $users = App\User::pluck('id')->toArray();
    $posts = App\Models\Post::pluck('id')->toArray();

    return [
        'user_id' => $faker->randomElement($users),
        'post_id' => $faker->randomElement($posts),
        'body' => $faker->sentence($nbWords = 10, $variableNbWords = true),
    ];
});
