<?php

namespace App\Livewire\Pages\TesdaFocal;

use App\Models\Assessment;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

class ViewAssessmentResults extends Component
{
    public $assessment;
    public $stats = [];

    #[Layout('layouts.app')]
    #[Title('Assessment Results Details')]

    public function mount($assessment)
    {
        $this->assessment = Assessment::with([
            'course', 
            'campus', 
            'academicYear', 
            'qualificationType', 
            'examType', 
            'schedules.assessmentCenter', 
            'schedules.assessor',
            'schedules.results.student.user',
            'schedules.results.competencyType'
        ])->findOrFail($assessment);

        $this->calculateStats();
    }

    private function calculateStats()
    {
        $total = 0;
        $completed = 0;
        $competent = 0;
        $notYetCompetent = 0;
        $absent = 0;
        
        // Collect results from all schedules
        foreach ($this->assessment->schedules as $schedule) {
            $total += $schedule->results->count();
            $completed += $schedule->results->whereNotNull('competency_type_id')->count();
            $competent += $schedule->results->filter(function($result) {
                return $result->competencyType?->name === 'Competent';
            })->count();
            $notYetCompetent += $schedule->results->filter(function($result) {
                return $result->competencyType?->name === 'Not Yet Competent';
            })->count();
            $absent += $schedule->results->filter(function($result) {
                return $result->competencyType?->name === 'Absent';
            })->count();
        }
        
        $pending = $total - $completed;

        $this->stats = [
            'total' => $total,
            'completed' => $completed,
            'competent' => $competent,
            'not_yet_competent' => $notYetCompetent,
            'absent' => $absent,
            'pending' => $pending,
            'completion_percentage' => $total > 0 ? round(($completed / $total) * 100, 1) : 0,
            'passing_percentage' => ($competent + $notYetCompetent) > 0 ? round(($competent / ($competent + $notYetCompetent)) * 100, 1) : 0
        ];
    }

    public function render()
    {
        return view('livewire.pages.tesda-focal.view-assessment-results');
    }
}
