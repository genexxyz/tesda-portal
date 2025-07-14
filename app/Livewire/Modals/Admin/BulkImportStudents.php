<?php

namespace App\Livewire\Modals\Admin;

use App\Imports\StudentImport;
use App\Models\Campus;
use App\Models\Course;
use App\Models\Academic;
use LivewireUI\Modal\ModalComponent;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;
use Maatwebsite\Excel\Facades\Excel;

class BulkImportStudents extends ModalComponent
{
    use WithFileUploads;

    #[Rule('required|file|mimes:xlsx,xls,csv|max:10240')]
    public $file;

    #[Rule('required|exists:campuses,id')]
    public $default_campus_id = '';

    #[Rule('required|exists:academics,id')]
    public $default_academic_year_id = '';

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function import()
    {
        $this->validate();

        try {
            $import = new StudentImport($this->default_campus_id, $this->default_academic_year_id);
            
            Excel::import($import, $this->file->getRealPath());

            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Students imported successfully!',
            ]);

            $this->dispatch('student-created');
            $this->closeModal();

        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Import Failed!',
                'text' => 'Error importing students: ' . $e->getMessage(),
            ]);
        }
    }

    public function downloadTemplate()
    {
        return response()->download(
            public_path('templates/student-import-template.xlsx'),
            'student-import-template.xlsx'
        );
    }

    public function render()
    {
        return view('livewire.modals.admin.bulk-import-students', [
            'campuses' => Campus::orderBy('name', 'asc')->get(),
            'academicYears' => Academic::orderBy('start_year', 'desc')->get()
        ]);
    }

    public static function modalMaxWidth(): string
    {
        return 'lg';
    }
}
