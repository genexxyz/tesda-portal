<?php

namespace App\Livewire\Pages\Registrar;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class RegistrarDashboard extends Component
{

    #[Layout('layouts.app')]
    #[Title('Dashboard')]
    public function render()
{
    $campusId = Auth::user()->campus_id;

    $totalStudents = \App\Models\Student::whereHas('user', function($q) use ($campusId) {
        $q->where('campus_id', $campusId)
          ->where('role_id', 5);
    })->count();

    $totalCourses = \App\Models\Course::whereHas('campuses', function($q) use ($campusId) {
        $q->where('campus_id', $campusId);
    })->count();

    $activeStudents = \App\Models\User::where('campus_id', $campusId)
        ->where('role_id', 5)
        ->where('status', 'active')
        ->whereHas('student')
        ->count();

    $droppedStudents = \App\Models\User::where('campus_id', $campusId)
        ->where('role_id', 5)
        ->where('status', 'dropped')
        ->whereHas('student')
        ->count();

    // Per-course stats
    $courses = \App\Models\Course::whereHas('campuses', function($q) use ($campusId) {
        $q->where('campus_id', $campusId);
    })->get();


$courseStats = $courses->map(function($course) use ($campusId) {
    $studentsQuery = $course->students()->whereHas('user', function($q) use ($campusId) {
        $q->where('campus_id', $campusId)
          ->where('role_id', 5);
    });

    $total = $studentsQuery->count();

    // Clone the query for active and dropped to avoid stacking conditions
    $active = (clone $studentsQuery)->whereHas('user', function($q) {
        $q->where('status', 'active');
    })->count();

    $dropped = (clone $studentsQuery)->whereHas('user', function($q) {
        $q->where('status', 'dropped');
    })->count();

    return [
        'code' => $course->code,
        'name' => $course->name,
        'total' => $total,
        'active' => $active,
        'dropped' => $dropped,
    ];
});

    return view('livewire.pages.registrar.registrar-dashboard', compact(
        'totalStudents', 'totalCourses', 'activeStudents', 'droppedStudents', 'courseStats'
    ));
}
}
