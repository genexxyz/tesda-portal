<?php

namespace App\Livewire\Modals\Admin;

use App\Models\User;
use App\Models\Campus;
use LivewireUI\Modal\ModalComponent;
use Livewire\Attributes\Rule;

class EditRegistrar extends ModalComponent
{
    public $registrarId;
    public $registrar;

    #[Rule('required|string|max:255')]
    public $first_name = '';

    #[Rule('nullable|string|max:255')]
    public $middle_name = '';

    #[Rule('required|string|max:255')]
    public $last_name = '';

    #[Rule('required|email')]
    public $email = '';

    #[Rule('required|exists:campuses,id')]
    public $campus_id = '';

    #[Rule('required|in:active,inactive')]
    public $status = 'active';

    public function mount($registrarId)
    {
        $this->registrarId = $registrarId;
        $this->registrar = User::with('campus')->findOrFail($registrarId);
        
        // Load registrar data
        $this->first_name = $this->registrar->first_name;
        $this->middle_name = $this->registrar->middle_name;
        $this->last_name = $this->registrar->last_name;
        $this->email = $this->registrar->email;
        $this->campus_id = $this->registrar->campus_id;
        $this->status = $this->registrar->status;
    }

    public function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->registrarId,
            'campus_id' => 'required|exists:campuses,id',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validate();

        try {
            $this->registrar->update([
                'first_name' => ucfirst(strtolower($this->first_name)),
                'middle_name' => $this->middle_name ? ucfirst(strtolower($this->middle_name)) : null,
                'last_name' => ucfirst(strtolower($this->last_name)),
                'email' => strtolower($this->email),
                'campus_id' => $this->campus_id,
                'status' => $this->status,
            ]);

            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Registrar updated successfully!',
            ]);

            $this->dispatch('registrar-updated');
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
        return view('livewire.modals.admin.edit-registrar', [
            'campuses' => Campus::orderBy('name', 'asc')->get()
        ]);
    }

    public static function modalMaxWidth(): string
    {
        return 'md';
    }
}
