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
        'password' => 'required|min:8'
    ];

    public function login()
    {
        $credentials = $this->validate();

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Check if user has a role
            if (!$user->role) {
                Auth::logout();
                $this->addError('email', 'Your account does not have proper access permissions.');
                return;
            }

            // Check user status
            if ($user->status !== 'active') {
                Auth::logout();
                
                switch ($user->status) {
                    case 'inactive':
                        $this->addError('email', 'Your account is currently inactive. Please contact the administrator.');
                        break;
                    case 'dropped':
                        $this->addError('email', 'Your account has been dropped. Please contact the administrator.');
                        break;
                    default:
                        $this->addError('email', 'Your account status does not allow access. Please contact the administrator.');
                        break;
                }
                return;
            }

            // Regenerate session for security
            session()->regenerate();

            // Redirect based on role
            switch ($user->role->name) {
                case 'admin':
                    return redirect()->route('admin.dashboard')->with('success', 'Welcome back, Admin!');
                case 'registrar':
                    return redirect()->route('registrar.dashboard')->with('success', 'Welcome back, Registrar!');
                case 'program-head':
                    return redirect()->route('program-head.dashboard')->with('success', 'Welcome back, Program Head!');
                case 'tesda-focal':
                    return redirect()->route('tesda-focal.dashboard')->with('success', 'Welcome back!');
                case 'student':
                    return redirect()->route('student.dashboard')->with('success', 'Welcome back, Student!');
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