<?php

namespace App\Livewire\Modals\Registrar;

use App\Imports\StudentImport;
use App\Models\Academic;
use LivewireUI\Modal\ModalComponent;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Computed;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentImportTemplate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BulkImportStudents extends ModalComponent
{
    use WithFileUploads;

    #[Rule('required|file|mimes:xlsx,xls,csv|max:10240')]
    public $file;

    #[Rule('required|exists:academics,id')]
    public $academic_year_id;

    public $importKey;
    public $isImporting = false;
    public $importCompleted = false;

    public function mount()
    {
        // Set default academic year to active one
        $activeAcademicYear = Academic::where('is_active', true)->first();
        $this->academic_year_id = $activeAcademicYear?->id;
        
        $this->importKey = 'student_import_' . Str::random(10);
    }

    public function startImport()
    {
        $this->validate();

        try {
            $this->isImporting = true;
            
            // Clear previous cache data
            $this->clearCacheData();
            
            // Dispatch event to start auto-refresh
            $this->dispatch('import-started');
            
            // Process the import
            Excel::import(new StudentImport($this->importKey, $this->academic_year_id), $this->file);
            
            $this->importCompleted = true;
            $this->isImporting = false;
            
            $this->dispatch('swal:success', [
                'title' => 'Import Completed!',
                'text' => 'Student import process has been completed. Check the results below.',
            ]);
$this->dispatch('student-created');
            

        } catch (\Exception $e) {
            $this->isImporting = false;
            
            $this->dispatch('swal:error', [
                'title' => 'Import Failed!',
                'text' => 'An error occurred during import: ' . $e->getMessage(),
            ]);
        }
    }

    #[Computed]
    public function importData()
    {
        if (!$this->importKey) {
            return [
                'errors' => [],
                'success' => [],
                'progress' => null
            ];
        }

        return [
            'errors' => Cache::get($this->importKey . '_errors', []),
            'success' => Cache::get($this->importKey . '_success', []),
            'progress' => Cache::get($this->importKey . '_overall_progress', null)
        ];
    }

    public function resetImport()
    {
        $this->clearCacheData();
        $this->reset(['file', 'isImporting', 'importCompleted']);
        $this->importKey = 'student_import_' . Str::random(10);
    }

    private function clearCacheData()
    {
        if ($this->importKey) {
            Cache::forget($this->importKey . '_errors');
            Cache::forget($this->importKey . '_success');
            Cache::forget($this->importKey . '_overall_progress');
            Cache::forget($this->importKey . '_progress');
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(
            new StudentImportTemplate(Auth::user()->campus_id), 
            'Student_Import_Template_' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function render()
    {
        return view('livewire.modals.registrar.bulk-import-students', [
            'academicYears' => Academic::orderBy('start_year', 'desc')->get()
        ]);
    }

    public static function modalMaxWidth(): string
    {
        return '4xl';
    }
}