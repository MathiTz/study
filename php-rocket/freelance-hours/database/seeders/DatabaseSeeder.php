<?php

namespace Database\Seeders;

use App\Actions\ArrangePositions;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->count(200)->create();

        User::query()->inRandomOrder()->limit(10)->get()
            ->each(
                function (User $u) {
                    $projects = Project::factory()->create(['created_by' => $u->id]);

                    Proposal::factory()->count(random_int(4, 45))->create([
                        'project_id' => $projects->id,
                    ]);

                    ArrangePositions::run($projects->id);
                }
            );


    }
}