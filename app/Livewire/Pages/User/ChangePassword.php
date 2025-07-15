<?php

namespace App\Livewire\Pages\User;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ChangePassword extends Component
{
    #[Validate('required|string')]
    public $current_password = '';

    #[Validate('required|string|min:8|confirmed')]
    public $new_password = '';

    #[Validate('required|string')]
    public $new_password_confirmation = '';

    protected function rules()
    {
        return [
            'current_password' => 'required|string|current_password',
            'new_password' => ['required', 'string', Password::min(8)->letters()->numbers()->symbols(), 'confirmed'],
            'new_password_confirmation' => 'required|string',
        ];
    }

    protected function messages()
    {
        return [
            'current_password.current_password' => 'The current password is incorrect.',
            'new_password.confirmed' => 'The new password confirmation does not match.',
            'new_password.min' => 'The new password must be at least 8 characters.',
        ];
    }

    public function resetForm()
    {
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        $this->resetErrorBag();
        
    }

    public function changePassword()
    {
        $this->validate();

        try {
            // Update the user's password using the User model
            $user = Auth::user();
            \App\Models\User::where('id', $user->id)->update([
                'password' => Hash::make($this->new_password)
            ]);

            // Clear the form
            $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

            $this->dispatch('swal:alert', type: 'success',  text: 'Your password has been changed successfully.');


        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while changing your password. Please try again.');
        }
    }

    #[Layout('layouts.app')]
    #[Title('Change Password')]
    public function render()
    {
        return view('livewire.pages.user.change-password');
    }
}
