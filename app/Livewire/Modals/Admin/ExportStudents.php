<?php

namespace App\Livewire\Modals\Admin;

use LivewireUI\Modal\ModalComponent;
use App\Models\Campus;
use App\Models\Course;
use App\Models\Academic;
use App\Exports\AdminStudentsExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportStudents extends ModalComponent
{
    public $includeAssessments = false;
    public $campusFilter = '';
    public $courseFilter = '';
    public $academicYearFilter = '';
    public $statusFilter = '';
    public $exportFormat = 'xlsx';

    public function mount()
    {
        // Initialize with current filters from the main page if available
    }

    public function exportStudents()
    {
        try {
            $filters = [
                'campus' => $this->campusFilter,
                'course' => $this->courseFilter,
                'academic_year' => $this->academicYearFilter,
                'status' => $this->statusFilter,
            ];

            // Remove empty filters
            $filters = array_filter($filters);

            $filename = 'students-export-' . now()->format('Y-m-d-H-i-s') . '.' . $this->exportFormat;
            
            $this->dispatch('swal:success', [
                'title' => 'Export Started!',
                'text' => 'Student data export has been initiated. Download will start shortly.',
            ]);

            $this->closeModal();

            if ($this->exportFormat === 'csv') {
                return Excel::download(new AdminStudentsExport($filters, $this->includeAssessments), $filename, \Maatwebsite\Excel\Excel::CSV);
            } else {
                return Excel::download(new AdminStudentsExport($filters, $this->includeAssessments), $filename);
            }
            
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Export Failed!',
                'text' => 'Failed to export student data: ' . $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        $campuses = Campus::orderBy('name', 'asc')->get();
        $courses = Course::orderBy('code', 'asc')->get();
        $academicYears = Academic::orderBy('start_year', 'desc')->get();
        
        return view('livewire.modals.admin.export-students', [
            'campuses' => $campuses,
            'courses' => $courses,
            'academicYears' => $academicYears,
        ]);
    }

    public static function modalMaxWidth(): string
    {
        return 'lg';
    }

    public static function closeModalOnEscape(): bool
    {
        return true;
    }

    public static function closeModalOnClickAway(): bool
    {
        return true;
    }
}
