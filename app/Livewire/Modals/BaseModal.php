<?php

namespace App\Livewire\Modals;

use LivewireUI\Modal\ModalComponent;

class BaseModal extends ModalComponent
{
    public static function modalMaxWidth(): string
    {
        return 'md'; // sm, md, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl, 7xl
    }

    public static function closeModalOnEscape(): bool
    {
        return true;
    }

    public static function closeModalOnClickAway(): bool
    {
        return true;
    }

    public static function dispatchCloseEvent(): bool
    {
        return true;
    }

    
}