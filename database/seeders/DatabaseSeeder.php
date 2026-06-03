<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task;
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user =User::factory()->create([
            'name' => 'Victor Arana',
            'email' => 'victor@codersfree.com',
            'password' => bcrypt('12345678')
        ]);

        Task::factory(100)->create([
            'user_id' => $user->id
        ]);
    }
}
