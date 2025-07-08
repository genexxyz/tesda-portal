<?php

namespace Database\Seeders;

use App\Models\QualificationType;
use Illuminate\Database\Seeder;

class QualificationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $qualificationTypes = [
            // Bread and Pastry Production - Multiple Levels
            [
                'code' => 'BKP',
                'name' => 'BOOKKEEPING',
                'level' => 'NC II',
                'description' => 'This qualification consists of competencies that a person must achieve to prepare, bake and present bread and pastry products at entry level.',
            ],
            [
                'code' => 'BKP',
                'name' => 'BOOKKEEPING',
                'level' => 'NC III',
                'description' => 'This qualification consists of competencies that a person must achieve to prepare, bake and present bread and pastry products at entry level.',
            ],
            [
                'code'=> 'COOKERY',
                'name' => 'COOKERY',
                'level' => 'NC II',
                'description' => 'This qualification consists of competencies that a person must achieve to prepare, cook and present food at entry level.',
            ],
            
            [
                'code' => 'SMAW',
                'name' => 'SHIELDED METAL ARC WELDING',
                'level' => 'NC II',
                'description' => 'This qualification consists of competencies that a person must achieve to perform advanced welding tasks using shielded metal arc welding process.',
            ],
            
            // Food and Beverage Services
            [
                'code' => 'FBS',
                'name' => 'FOOD AND BEVERAGE SERVICES',
                'level' => 'NC II',
                'description' => 'This qualification consists of competencies that a person must achieve to provide food and beverage services.',
            ],
            
            // Housekeeping
            [
                'code' => 'HKP',
                'name' => 'HOUSEKEEPING',
                'level' => 'NC II',
                'description' => 'This qualification consists of competencies that a person must achieve to provide housekeeping services.',
            ],
            
            // Electrical Installation and Maintenance
            [
                'code' => 'EIM',
                'name' => 'ELECTRICAL INSTALLATION AND MAINTENANCE',
                'level' => 'NC II',
                'description' => 'This qualification consists of competencies that a person must achieve to install and maintain electrical systems.',
            ],
            
        ];

        foreach ($qualificationTypes as $qualificationType) {
            QualificationType::create($qualificationType);
        }
    }
}
