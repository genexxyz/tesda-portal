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

            return redirect()->intended('/dashboard')->with('success', 'Welcome back!');
        }

        $this->addError('email', 'The provided credentials do not match our records.');
        $this->reset('password');
    }
    
    public function render()
    {
        return view('livewire.auth.login');
    }
}