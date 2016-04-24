<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(\Dan\Tagging\Testing\Integration\Setup\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->define(\Dan\Tagging\Testing\Integration\Setup\Post::class, function (Faker\Generator $faker) {
    $title = $faker->sentence;
    $body = $faker->paragraph;
    return compact('title', 'body');
});

$factory->define(\Dan\Tagging\Models\Tag::class, function (Faker\Generator $faker) {
    $name = $faker->word;
    $slug = \Illuminate\Support\Str::slug($name);
    return compact('name', 'slug');
});

$factory->define(\Dan\Tagging\Models\Tagged::class, function (Faker\Generator $faker) {
    $tag_name = $faker->word;
    $tag_slug = \Illuminate\Support\Str::slug($tag_name);
    return compact('tag_name', 'tag_slug');
});

$factory->define(\Dan\Tagging\Models\TaggedUser::class, function (Faker\Generator $faker) {
    // Provide real data with create()
    return [];
});