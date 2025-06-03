<?php

namespace Kamansoft\LaravelBlame\Tests\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Kamansoft\LaravelBlame\Tests\Models\TestModel;

class TestModelFactory extends Factory
{
    protected $model = TestModel::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
