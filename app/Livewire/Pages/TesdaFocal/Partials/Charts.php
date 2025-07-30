<?php
namespace App\Livewire\Pages\TesdaFocal\Partials;

use Livewire\Component;
use App\Models\Academic;
use App\Models\Campus;
use App\Models\Assessment;
use Illuminate\Support\Facades\Log;

class Charts extends Component
{
    /**
     * Build the base query for assessments with all necessary relationships
     * (Similar to your ViewResults pattern)
     */
    private function buildAssessmentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        // Get active academic year (matching your ViewResults logic)
        $activeAcademicYear = Academic::where('is_active', true)->first();
        
        $query = Assessment::with([
            'course', 
            'examType', 
            'qualificationType',
            'campus',
            'academicYear',
            'schedules' => function ($q) {
                $q->where('assessment_date', '<', now());
            },
            'schedules.results.competencyType'
        ])
        ->whereHas('schedules', function($q) {
            $q->where('assessment_date', '<', now());
        });

        // Apply academic year filter (matching your pattern)
        if ($activeAcademicYear) {
            $query->where('academic_year_id', $activeAcademicYear->id);
        }

        return $query;
    }

    /**
     * Process assessment results (adapted from your ViewResults logic)
     */
    private function processAssessmentResults(Assessment $assessment): array
    {
        $competentCount = 0;
        $notYetCompetentCount = 0;
        $totalAssessed = 0;

        // Process results from all schedules for this assessment
        foreach ($assessment->schedules as $schedule) {
            foreach ($schedule->results as $result) {
                if ($result->competency_type_id && $result->competencyType) {
                    switch ($result->competencyType->name) {
                        case 'Competent':
                            $competentCount++;
                            $totalAssessed++;
                            break;
                        case 'Not Yet Competent':
                            $notYetCompetentCount++;
                            $totalAssessed++;
                            break;
                        // Don't count 'Absent' or 'Dropped' in pass rate calculations
                    }
                }
            }
        }

        return [
            'competent' => $competentCount,
            'not_yet_competent' => $notYetCompetentCount,
            'total_assessed' => $totalAssessed
        ];
    }

    /**
     * Get chart data for ISA assessments
     */
    private function getIsaChartData(): array
    {
        $query = $this->buildAssessmentQuery();
        
        // Filter for ISA assessments
        $isaAssessments = $query->whereHas('examType', function ($q) {
            $q->where('type', 'ISA');
        })->get();

        $campusData = [];
        $campusColors = [];

        // Get all campuses to ensure consistent ordering
        $allCampuses = Campus::orderBy('name')->get();

        foreach ($allCampuses as $campus) {
            $campusAssessments = $isaAssessments->where('campus_id', $campus->id);
            
            $totalCompetent = 0;
            $totalAssessed = 0;

            foreach ($campusAssessments as $assessment) {
                $results = $this->processAssessmentResults($assessment);
                $totalCompetent += $results['competent'];
                $totalAssessed += $results['total_assessed'];
            }

            $passRate = $totalAssessed > 0 ? round(($totalCompetent / $totalAssessed) * 100, 2) : 0;
            $campusData[$campus->name] = $passRate;
            $campusColors[] = $campus->color ?? '#3b82f6';
        }

        return [
            'data' => $campusData,
            'colors' => $campusColors
        ];
    }

    /**
     * Get chart data for Mandatory assessments
     */
    private function getMandatoryChartData(): array
    {
        $query = $this->buildAssessmentQuery();
        
        // Filter for Mandatory assessments
        $mandatoryAssessments = $query->whereHas('examType', function ($q) {
            $q->where('type', 'MANDATORY');
        })->get();

        $campusData = [];
        $campusColors = [];

        // Get all campuses to ensure consistent ordering
        $allCampuses = Campus::orderBy('name')->get();

        foreach ($allCampuses as $campus) {
            $campusAssessments = $mandatoryAssessments->where('campus_id', $campus->id);
            
            $totalCompetent = 0;
            $totalAssessed = 0;

            foreach ($campusAssessments as $assessment) {
                $results = $this->processAssessmentResults($assessment);
                $totalCompetent += $results['competent'];
                $totalAssessed += $results['total_assessed'];
            }

            $passRate = $totalAssessed > 0 ? round(($totalCompetent / $totalAssessed) * 100, 2) : 0;
            $campusData[$campus->name] = $passRate;
            $campusColors[] = $campus->color ?? '#6366f1';
        }

        return [
            'data' => $campusData,
            'colors' => $campusColors
        ];
    }

    public function render()
    {
        try {
            // Check if active academic year exists
            $activeAcademicYear = Academic::where('is_active', true)->first();
            
            if (!$activeAcademicYear) {
                Log::warning('Charts: No active academic year found');
                return view('livewire.pages.tesda-focal.partials.charts', [
                    'isaCampuses' => [],
                    'mandatoryCampuses' => [],
                    'isaColors' => [],
                    'mandatoryColors' => [],
                    'debug_message' => 'No active academic year found'
                ]);
            }

            // Check if campuses exist
            $campusCount = Campus::count();
            if ($campusCount === 0) {
                Log::warning('Charts: No campuses found');
                return view('livewire.pages.tesda-focal.partials.charts', [
                    'isaCampuses' => [],
                    'mandatoryCampuses' => [],
                    'isaColors' => [],
                    'mandatoryColors' => [],
                    'debug_message' => 'No campuses found'
                ]);
            }

            // Get chart data using the same pattern as ViewResults
            $isaData = $this->getIsaChartData();
            $mandatoryData = $this->getMandatoryChartData();

            // Debug logging
            Log::info('Charts data retrieved successfully', [
                'active_academic_year' => $activeAcademicYear->id,
                'campus_count' => $campusCount,
                'isa_data_count' => count($isaData['data']),
                'mandatory_data_count' => count($mandatoryData['data'])
            ]);

            return view('livewire.pages.tesda-focal.partials.charts', [
                'isaCampuses' => $isaData['data'],
                'mandatoryCampuses' => $mandatoryData['data'],
                'isaColors' => $isaData['colors'],
                'mandatoryColors' => $mandatoryData['colors'],
            ]);

        } catch (\Exception $e) {
            Log::error('Charts component error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return view('livewire.pages.tesda-focal.partials.charts', [
                'isaCampuses' => [],
                'mandatoryCampuses' => [],
                'isaColors' => [],
                'mandatoryColors' => [],
                'debug_message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }





    // public function render()
    // {
    //     // Test campuses with colors
    //     $campuses = collect([
    //         (object)['name' => 'MALOLOS', 'color' => '#1E90FF'],
    //         (object)['name' => 'ANGAT', 'color' => '#FF0000'],
    //         (object)['name' => 'BOCAUE', 'color' => '#FFA500'],
    //         (object)['name' => 'OBANDO', 'color' => '#FFFF00'],
    //         (object)['name' => 'PANDI', 'color' => '#0000FF'],
    //         (object)['name' => 'SAN MIGUEL', 'color' => '#4B0082'],
    //         (object)['name' => 'SAN RAFAEL', 'color' => '#9400D3'],
    //         (object)['name' => 'SAN JOSE DEL MONTE', 'color' => '#808080'],
    //     ]);

    //     $isaCampuses = [];
    //     $mandatoryCampuses = [];
    //     $isaColors = [];
    //     $mandatoryColors = [];

    //     foreach ($campuses as $campus) {
    //         $isaPassRate = rand(60, 100);
    //         $mandatoryPassRate = rand(40, 90);

    //         $isaCampuses[$campus->name] = $isaPassRate;
    //         $isaColors[] = $campus->color;

    //         $mandatoryCampuses[$campus->name] = $mandatoryPassRate;
    //         $mandatoryColors[] = $campus->color;
    //     }

    //     return view('livewire.pages.tesda-focal.partials.charts', [
    //         'isaCampuses' => $isaCampuses,
    //         'mandatoryCampuses' => $mandatoryCampuses,
    //         'isaColors' => $isaColors,
    //         'mandatoryColors' => $mandatoryColors,
    //     ]);
    // }
}
