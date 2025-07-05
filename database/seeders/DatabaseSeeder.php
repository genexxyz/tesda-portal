<?php

namespace Database\Seeders;

use App\Models\Campus;
use App\Models\Course;
use App\Models\User;
use App\Models\Role;
use App\Models\School;
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

        School::create([
            'name' => 'Bulacan Polytechnic College',
            'address' => 'Bulihan, City Of Malolos, Bulacan 3000',
            'contact_number' => '09123456789',
            'email' => 'communications@bpc.edu.ph',
            'website' => 'bpc.edu.ph',
            'logo' => 'default.png',
            'tag_line' => 'Your Partner to Reach the World',
        ]);

        Campus::create([
            'code' => 'MA',
            'name' => 'MALOLOS',
            'number' => 1,
            'color' => '#0000FF', // Blue
        ]);

        Course::create([
            'code' => 'HRS',
            'name' => 'Hotel and Restaurant Services',
            'campus_id' => Campus::where('code', 'MA')->value('id'),
        ]);


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
