<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Post;
use Faker\Generator as Faker;

$factory->define(Post::class, function (Faker $faker) {
    // Get users
    $allUsers = App\User::pluck('id')->toArray();

    return [
        'user_id' => $faker->randomElement($allUsers),
        'caption' => $faker->sentence($nbWords = 6, $variableNbWords = true),
        'image' => $faker->imageUrl($width = 640, $height = 480),
    ];
});
