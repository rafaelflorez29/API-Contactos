<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Administrador TaskFlow',
            'email' => 'admin@taskflow.test',
        ]);

        User::factory()->count(10)->create();
    }
}
