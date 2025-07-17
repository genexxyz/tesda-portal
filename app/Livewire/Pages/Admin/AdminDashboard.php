<?php

namespace App\Livewire\Pages\Admin;

use App\Models\Campus;
use App\Models\Course;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\User;

class AdminDashboard extends Component
{
    #[Layout('layouts.app')]
    #[Title('Dashboard')]

    public $activeTab = 'campuses';

    public function mount()
    {
        // Set default tab if none specified
        if (!in_array($this->activeTab, ['campuses', 'courses', 'qualifications',])) {
            $this->activeTab = 'campuses';
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }
    public function render()
    {
        $totalUsers = User::count();
        $totalStudents = User::where('role_id', 5)->count();
        $totalRegistrars = User::where('role_id', 2)->count();
        $totalProgramHeads = User::where('role_id', 3)->count();
$totalCampuses = Campus::count();
$campuses = Campus::all();
$courses = Course::all();

$date = now()->format('l, F j, Y'); // Format date as "Day, Month Date, Year"
$time = now()->format('h:i A'); // Format time as 12-hour with AM/PM
        return view('livewire.pages.admin.admin-dashboard', [
            'totalUsers' => $totalUsers,
            'totalStudents' => $totalStudents,
            'totalRegistrars' => $totalRegistrars,
            'totalProgramHeads' => $totalProgramHeads,
            'totalCampuses' => $totalCampuses,
            'campuses' => $campuses,
            'courses' => $courses,
            'date' => $date,
            'time' => $time,
        ]);
    }
}