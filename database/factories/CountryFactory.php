<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class CountryFactory extends Factory
{
    protected $model = Country::class;

    public function definition()
    {
        return [
            'code' => $this->faker->unique()->countryCode,
            'name' => $this->faker->country,
        ];
    }
}
