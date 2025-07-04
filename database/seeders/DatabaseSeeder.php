<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $roles = ['admin', 'registrar', 'program-head', 'assessor', 'student'];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }

        

        User::create([
            'last_name' => 'Admin',
            'first_name' => 'User',
            'middle_name' => 'A',
            'email' => 'admin@bpc.edu.ph',
            'password' => bcrypt('password'),
            'campus_id' => null,
            'role_id' => Role::where('name', 'admin')->value('id'),
        ]);
    }
}
