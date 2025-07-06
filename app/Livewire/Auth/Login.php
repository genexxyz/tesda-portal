<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $email = '';
    public $password = '';

    #[Layout('layouts.guest')]
    #[Title('Login')]

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:6'
    ];

    public function login()
{
    $credentials = $this->validate();

    if (Auth::attempt($credentials)) {
        session()->regenerate();

        $user = Auth::user();
        
        // Check if user has a role
        if (!$user->role) {
            Auth::logout();
            $this->addError('email', 'Your account does not have proper access permissions.');
            return;
        }

        // Redirect based on role
        switch ($user->role->name) {
            case 'admin':
                return redirect()->route('admin.dashboard')->with('success', 'Welcome back, Admin!');
            case 'registrar':
                return redirect()->route('registrar.dashboard')->with('success', 'Welcome back, Registrar!');
            default:
                Auth::logout();
                $this->addError('email', 'Invalid role assigned to your account.');
                return;
        }
    }

    $this->addError('email', 'The provided credentials do not match our records.');
    $this->reset('password');
}
    
    public function render()
    {
        return view('livewire.auth.login');
    }
}