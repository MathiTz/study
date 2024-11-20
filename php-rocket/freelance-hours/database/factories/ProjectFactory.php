<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Random\RandomException;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws RandomException
     */

    public function definition(): array
    {
//        dd(fake()->randomElements(['react', 'php', 'laravel', 'vue', 'tailwindcss', 'javascript', 'nextjs', 'python'], random_int(1, 5)));
        return [
            "title" => implode(' ', fake()->words(5)),
            "description" => 'oi',
            "ends_at" => fake()->dateTimeBetween('now', '+ 3 days'),
            "status" => fake()->randomElement(['open', 'closed']),
            "tech_stack" => fake()->randomElements(['nodejs', 'react', 'javascript', 'vite', 'nextjs'], random_int(1, 5)),
            "created_by" => User::factory()
        ];
    }
}
