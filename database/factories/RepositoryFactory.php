<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Repository;

class RepositoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Repository::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->sentence(4)    ,
            'repoUrl' => $this->faker->randomElement(['git@github.com:Binary-Hype/backup.git', 'git@github.com:Binary-Hype/binary-hype.git']),
        ];
    }
}
