<?php

namespace App\View\Components\Buttons;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ImportExportButtons extends Component
{
    public $importModal;
    public $exportModal;
    public $importTooltip;
    public $exportTooltip;
    public $showImport;
    public $showExport;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $importModal = '',
        $exportModal = '',
        $importTooltip = 'Import data',
        $exportTooltip = 'Export data',
        $showImport = true,
        $showExport = true
    ) {
        $this->importModal = $importModal;
        $this->exportModal = $exportModal;
        $this->importTooltip = $importTooltip;
        $this->exportTooltip = $exportTooltip;
        $this->showImport = $showImport;
        $this->showExport = $showExport;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.buttons.import-export-buttons');
    }
}