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
            'name' => 'Dropped',
            'description' => 'Student has dropped from the assessment',
        ]);

        CompetencyType::create([
            'name' => 'Absent',
            'description' => 'Student was absent during assessment',
        ]);

        // Create Assessment Centers
        AssessmentCenter::create([
            'name' => 'Main Assessment Center',
            'address' => '123 Main Street, City, Province',
        ]);

        AssessmentCenter::create([
            'name' => 'Secondary Assessment Center',
            'address' => '456 Secondary Street, City, Province',
        ]);

        // Create Assessors
        Assessor::create([
            'name' => 'John Doe',
        ]);

        Assessor::create([
            'name' => 'Jane Smith',
        ]);

        Assessor::create([
            'name' => 'Michael Johnson',
        ]);

        // Associate assessors with assessment centers
        $assessors = Assessor::all();
        $centers = AssessmentCenter::all();

        foreach ($assessors as $assessor) {
            $assessor->assessmentCenters()->attach($centers->random(rand(1, 2)));
        }
    }
}
