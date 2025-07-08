<?php

namespace App\Livewire\Modals\Admin;

use App\Models\QualificationType;
use App\Models\Course;
use LivewireUI\Modal\ModalComponent;
use Livewire\Attributes\Rule;

class AddNewQualificationType extends ModalComponent
{
    public $code = '';
    public $name = '';
    public $level = 'NC II';
    public $description = '';
    public $selectedCourses = [];

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
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('qualification_types', 'name')
                    ->where('level', $this->level)
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
            $qualificationType = QualificationType::create([
                'code' => strtoupper($this->code),
                'name' => strtoupper($this->name),
                'level' => $this->level,
                'description' => $this->description,
            ]);

            // Attach selected courses if any
            if (!empty($this->selectedCourses)) {
                $qualificationType->courses()->attach($this->selectedCourses);
            }

            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Qualification type created successfully!',
            ]);

            $this->dispatch('qualification-type-created');
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
        return view('livewire.modals.admin.add-new-qualification-type', [
            'courses' => Course::orderBy('code', 'asc')->get()
        ]);
    }

    public static function modalMaxWidth(): string
    {
        return 'lg';
    }
}