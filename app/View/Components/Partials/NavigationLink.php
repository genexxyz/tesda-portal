<?php

namespace App\View\Components\Partials;

use App\Services\NavigationService;
use Illuminate\View\Component;

class NavigationLink extends Component
{
    public function __construct(
        public string $label,
        public string $route,
        public string $icon,
        public string $iconStyle = 'fas',
        public array $activeRoutes = []
    ) {}

    public function isActive(): bool
    {
        $currentRoute = request()->route()?->getName() ?? '';
        return NavigationService::isRouteActive($this->activeRoutes, $currentRoute);
    }

    public function render()
    {
        return view('components.partials.navigation-link', [
            'isActive' => $this->isActive()
        ]);
    }
}