<?php

namespace Database\Seeders;

use App\Models\ExamType;
use App\Models\CompetencyType;
use App\Models\AssessmentCenter;
use App\Models\Assessor;
use Illuminate\Database\Seeder;

class AssessmentSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create Exam Types
        ExamType::create([
            'type' => 'ISA',
            'description' => 'Initial Skills Assessment',
        ]);

        ExamType::create([
            'type' => 'MANDATORY',
            'description' => 'Mandatory Assessment',
        ]);

        // Create Competency Types
        CompetencyType::create([
            'name' => 'Competent',
            'description' => 'Student has demonstrated competency',
        ]);

        CompetencyType::create([
            'name' => 'Not Yet Competent',
            'description' => 'Student has not yet demonstrated competency',
        ]);

        CompetencyType::create([
            'name' => 'Absent',
            'description' => 'Student was absent during assessment',
        ]);

        CompetencyType::create([
            'name' => 'Dropped',
            'description' => 'Student has dropped from the assessment',
        ]);

        

        
    }
}
