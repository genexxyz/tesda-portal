<?php

namespace App\Livewire\Modals\Registrar;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Course;
use App\Models\Academic;
use App\Models\CampusCourse;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RegistrarStudentsExport;
use LivewireUI\Modal\ModalComponent;

class ExportStudents extends ModalComponent
{
    public $isOpen = false;
    public $selectedAcademicYear = '';
    public $selectedCourses = [];
    public $includeResults = false;
    public $isExporting = false;

    public function mount()
    {
        // Set default to current active academic year
        $activeAcademicYear = Academic::where('is_active', true)->first();
        if ($activeAcademicYear) {
            $this->selectedAcademicYear = $activeAcademicYear->id;
        }
    }

    #[On('openModal')]
    public function openModal($component)
    {
        if ($component === 'modals.registrar.export-students') {
            $this->isOpen = true;
        }
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->reset(['selectedCourses', 'includeResults', 'isExporting']);
        parent::closeModal();
    }

    public function toggleCourse($courseId)
    {
        if (in_array($courseId, $this->selectedCourses)) {
            $this->selectedCourses = array_filter($this->selectedCourses, fn($id) => $id != $courseId);
        } else {
            $this->selectedCourses[] = $courseId;
        }
    }

    public function selectAllCourses()
    {
        $this->selectedCourses = $this->availableCourses->pluck('id')->toArray();
    }

    public function deselectAllCourses()
    {
        $this->selectedCourses = [];
    }

    public function exportStudents()
    {
        $this->validate([
            'selectedAcademicYear' => 'required|exists:academics,id',
            'selectedCourses' => 'required|array|min:1',
            'selectedCourses.*' => 'exists:courses,id'
        ], [
            'selectedAcademicYear.required' => 'Please select an academic year.',
            'selectedCourses.required' => 'Please select at least one course.',
            'selectedCourses.min' => 'Please select at least one course.'
        ]);

        try {
            $this->isExporting = true;

            $academicYear = Academic::find($this->selectedAcademicYear);
            $fileName = 'Students_' . str_replace(['/', ' '], ['_', '_'], $academicYear->formatted_description) . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

            return Excel::download(
                new RegistrarStudentsExport(
                    $this->selectedAcademicYear,
                    $this->selectedCourses,
                    $this->includeResults,
                    Auth::user()->campus_id
                ),
                $fileName
            );

        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Export Failed',
                'text' => 'An error occurred while exporting: ' . $e->getMessage()
            ]);
        } finally {
            $this->isExporting = false;
            $this->closeModal();
        }
    }

    public function getAvailableAcademicYearsProperty()
    {
        return Academic::orderBy('start_year', 'desc')->get();
    }

    public function getAvailableCoursesProperty()
    {
        // Get course IDs that are associated with the registrar's campus
        $courseIds = CampusCourse::where('campus_id', Auth::user()->campus_id)
                                ->pluck('course_id');

        // Get the actual courses using the IDs
        return Course::whereIn('id', $courseIds)
                    ->orderBy('name')
                    ->get();
    }

    public function getSelectedCoursesNamesProperty()
    {
        return Course::whereIn('id', $this->selectedCourses)
                    ->pluck('code')
                    ->join(', ');
    }

    public function render()
    {
        return view('livewire.modals.registrar.export-students', [
            'academicYears' => $this->availableAcademicYears,
            'courses' => $this->availableCourses,
            'selectedCoursesNames' => $this->selectedCoursesNames
        ]);
    }

    public static function modalMaxWidth(): string
    {
        return '4xl';
    }
}
