<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Metko\Galera\GlrMessage;
use Faker\Generator as Faker;

$factory->define(GlrMessage::class, function (Faker $faker) {
    return [
        'message' => $faker->text,
    ];
});
