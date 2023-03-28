<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\WorkReport::class, function (Faker $faker, $factory) {
    return [
        'uuid' => \Ramsey\Uuid\Uuid::getFactory()->uuid4()
    ];
});
