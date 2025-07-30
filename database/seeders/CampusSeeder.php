<?php

namespace Database\Seeders;

use App\Models\Campus;
use Illuminate\Database\Seeder;

class CampusSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        

        //MALOLOS, ANGAT, BOCAUE, OBANDO, PANDI, SAN MIGUEL, SAN RAFAEL, SAN JOSE DEL MONTE
        $campuses = [
            ['code' => 'AN', 'name' => 'ANGAT', 'number'=> 2,'color'=> '#FF0000'], // Red
            ['code' => 'BO', 'name' => 'BOCAUE', 'number'=> 3,'color'=> '#FFA500'], // Orange
            ['code' => 'OB', 'name' => 'OBANDO', 'number'=> 4,'color'=> '#FFFF00'], // Yellow
            ['code' => 'PA', 'name' => 'PANDI', 'number'=> 5,'color'=> '#0000FF'], // Blue
            ['code' => 'SM', 'name' => 'SAN MIGUEL', 'number'=> 6,'color'=> '#4B0082'], // Indigo
            ['code' => 'SR', 'name' => 'SAN RAFAEL', 'number'=> 7,'color'=> '#9400D3'], // Violet
            ['code' => 'SJ', 'name' => 'SAN JOSE DEL MONTE', 'number'=> 8,'color'=> '#808080'], // Grey
        ];

        foreach ($campuses as $campus) {
            Campus::create($campus);
        }

        
    }
}
