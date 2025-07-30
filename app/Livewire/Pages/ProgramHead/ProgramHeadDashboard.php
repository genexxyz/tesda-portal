<?php

namespace App\Livewire\Pages\ProgramHead;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\ProgramHead;
use Illuminate\Support\Facades\Auth;

class ProgramHeadDashboard extends Component
{
    #[Layout('layouts.app')]
    #[Title('Dashboard')]

public function getCoursesProperty()
{
    // Get all courses assigned to the current program head
    return ProgramHead::where('user_id', Auth::id())
        ->with('course')
        ->get()
        ->pluck('course')
        ->unique('id')
        ->filter();
}
  public function getTotalStudentsProperty()
{
    $campusId = Auth::user()->campus_id;

    return $this->courses->flatMap(function ($course) use ($campusId) {
        // Filter students by their user's campus_id
        return $course->students->filter(function ($student) use ($campusId) {
            return $student->user && $student->user->campus_id == $campusId;
        });
    })->unique('id')->count();
}
    public function getUpcomingAssessmentsProperty()
{
    $managedCourseIds = $this->courses->pluck('id');
    $campusId = Auth::user()->campus_id;
    $currentDate = now();

    $assessments = \App\Models\Assessment::whereIn('course_id', $managedCourseIds)
        ->whereHas('academicYear', function($q) {
            $q->where('is_active', true);
        })
        ->whereHas('schedules', function($q) use ($currentDate) {
            $q->where('assessment_date', '>', $currentDate);
        })
        ->with([
            'course',
            'qualificationType',
            'examType',
            'schedules' => function($q) use ($currentDate) {
                $q->where('assessment_date', '>', $currentDate);
            }
        ])
        ->get();

    // Only include assessments for students in the current user's campus
    return $assessments->flatMap(function($assessment) use ($campusId) {
        return $assessment->schedules->map(function($schedule) use ($assessment, $campusId) {
            // Optionally, you can check if the assessment/course is relevant to the campus here
            return [
                'type' => $assessment->examType?->type,
                'course' => $assessment->course?->name,
                'qualification' => $assessment->qualificationType?->code . ' - ' . $assessment->qualificationType?->level,
                'date' => $schedule->assessment_date?->format('Y-m-d'),
            ];
        });
    })->sortBy('date')->values();
}
public function getTotalAssessmentsProperty()
{
    $managedCourseIds = $this->courses->pluck('id');
    $campusId = Auth::user()->campus_id;

    return \App\Models\Assessment::whereIn('course_id', $managedCourseIds)
        ->where('campus_id', $campusId)
        ->whereHas('academicYear', function($q) {
            $q->where('is_active', true);
        })
        ->count();
}
    public function render()
    {
        $totalStudents = $this->getTotalStudentsProperty();
        $totalCourses = $this->courses->count();
        $totalAssessments = $this->getTotalAssessmentsProperty();
        $campus = Auth::user()->campus->name ?? 'NO CAMPUS';
        $upcomingAssessments = $this->getUpcomingAssessmentsProperty();

        return view('livewire.pages.program-head.program-head-dashboard', [
            'totalStudents' => $totalStudents,
            'totalCourses' => $totalCourses,
            'totalAssessments' => $totalAssessments,
            'campus' => $campus,
            'upcomingAssessments' => $upcomingAssessments,
        ]);
    }
}
