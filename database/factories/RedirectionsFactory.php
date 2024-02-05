<?php

namespace Pardalsalcap\HailoRedirections\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Pardalsalcap\HailoRedirections\Models\Redirection;


class RedirectionsFactory extends Factory
{
    protected $model = Redirection::class;

    public function definition()
    {
        $url = $this->faker->url;
        return [
            'hash' => Hash::make($url),
            'url' => $url,
            'fix' => $this->faker->url,
            'hits' => $this->faker->randomNumber(),
            'http_status' => $this->faker->randomElement(['301', '302', '404', '500']),
        ];
    }
}
