<?php

namespace App\Livewire\Modals\Admin;

use App\Models\QualificationType;
use App\Models\Course;
use LivewireUI\Modal\ModalComponent;
use Livewire\Attributes\Rule;

class EditQualificationType extends ModalComponent
{
    public $qualificationTypeId;
    public $qualificationType;

    public $code = '';
    public $name = '';
    public $level = 'NC II';
    public $description = '';
    public $selectedCourses = [];

    public function mount($qualificationTypeId)  // Changed from $id to $qualificationTypeId
    {
        $this->qualificationTypeId = $qualificationTypeId;
        $this->qualificationType = QualificationType::with('courses')->findOrFail($qualificationTypeId);
        
        $this->code = $this->qualificationType->code;
        $this->name = $this->qualificationType->name;
        $this->level = $this->qualificationType->level;
        $this->description = $this->qualificationType->description;
        $this->selectedCourses = $this->qualificationType->courses->pluck('id')->toArray();
    }

    public function updated($propertyName)
    {
        // Use the rules() method for validation to include the unique constraints
        $this->validateOnly($propertyName, $this->rules(), $this->messages());
    }

    public function rules()
    {
        return [
            'code' => [
                'required',
                'string',
                'max:20',
                \Illuminate\Validation\Rule::unique('qualification_types', 'code')
                    ->where('level', $this->level)
                    ->ignore($this->qualificationTypeId)
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('qualification_types', 'name')
                    ->where('level', $this->level)
                    ->ignore($this->qualificationTypeId)
            ],
            'level' => 'required|in:NC I,NC II,NC III,NC IV',
            'description' => 'nullable|string|max:1000',
            'selectedCourses' => 'nullable|array'
        ];
    }

    public function messages()
    {
        return [
            'code.unique' => 'This code already exists for the selected level (' . $this->level . ').',
            'name.unique' => 'This name already exists for the selected level (' . $this->level . ').',
            'code.required' => 'The qualification code is required.',
            'name.required' => 'The qualification name is required.',
            'level.required' => 'The level is required.',
            'code.max' => 'The code may not be greater than 20 characters.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'description.max' => 'The description may not be greater than 1000 characters.',
        ];
    }

    public function save()
    {
        $this->validate();

        try {
            $this->qualificationType->update([
                'code' => strtoupper($this->code),
                'name' => strtoupper($this->name),
                'level' => $this->level,
                'description' => $this->description,
            ]);

            // Sync selected courses
            $this->qualificationType->courses()->sync($this->selectedCourses);

            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Qualification type updated successfully!',
            ]);

            $this->dispatch('qualification-type-updated');
            $this->closeModal();

        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Something went wrong. Please try again.',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.modals.admin.edit-qualification-type', [
            'courses' => Course::orderBy('code', 'asc')->get()
        ]);
    }

    public static function modalMaxWidth(): string
    {
        return 'lg';
    }
}