<?php

namespace App\Livewire\Modals\Admin;

use App\Models\User;
use App\Models\Campus;
use Illuminate\Support\Facades\Hash;
use LivewireUI\Modal\ModalComponent;
use Livewire\Attributes\Rule;

class AddNewRegistrar extends ModalComponent
{
    #[Rule('required|string|max:255')]
    public $first_name = '';

    #[Rule('nullable|string|max:255')]
    public $middle_name = '';

    #[Rule('required|string|max:255')]
    public $last_name = '';

    #[Rule('required|email|unique:users,email')]
    public $email = '';

    #[Rule('required|exists:campuses,id')]
    public $campus_id = '';

    

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validate();

        try {
            User::create([
                'first_name' => ucfirst(strtolower($this->first_name)),
                'middle_name' => $this->middle_name ? ucfirst(strtolower($this->middle_name)) : null,
                'last_name' => ucfirst(strtolower($this->last_name)),
                'email' => strtolower($this->email),
                'campus_id' => $this->campus_id ?: null,
                'role_id' => 2, // Registrar role
                'status' => 'active', // Default status
                'password' => Hash::make('password'), // Generate a random password
            ]);

            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Registrar created successfully!',
            ]);

            $this->dispatch('registrar-created');
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
        return view('livewire.modals.admin.add-new-registrar', [
            'campuses' => Campus::orderBy('name', 'asc')->get()
        ]);
    }

    public static function modalMaxWidth(): string
    {
        return '2xl';
    }
}