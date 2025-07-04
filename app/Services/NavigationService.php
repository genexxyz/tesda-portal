<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class NavigationService
{
    public static function getNavigationForRole(string $roleName): array
    {
        $cacheKey = "navigation.{$roleName}";
        
        return Cache::remember($cacheKey, now()->addHours(24), function () use ($roleName) {
            $filePath = resource_path("data/navigation/{$roleName}.json");
            
            if (!File::exists($filePath)) {
                return [];
            }
            
            $content = File::get($filePath);
            $data = json_decode($content, true);
            
            return $data['navigation'] ?? [];
        });
    }
    
    public static function isRouteActive(array $activeRoutes, string $currentRoute): bool
    {
        foreach ($activeRoutes as $pattern) {
            if (str_contains($pattern, '*')) {
                $pattern = str_replace('*', '', $pattern);
                if (str_starts_with($currentRoute, $pattern)) {
                    return true;
                }
            } else {
                if ($currentRoute === $pattern) {
                    return true;
                }
            }
        }
        
        return false;
    }
}