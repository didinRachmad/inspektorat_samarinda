<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Models\Menu;

if (!function_exists('currentMenu')) {
    function currentMenu()
    {
        $routeName = Route::currentRouteName();
        if (!$routeName) {
            return null;
        }

        $cacheKey = "menu_{$routeName}";

        return Cache::remember($cacheKey, now()->addHour(), function () use ($routeName) {
            return Menu::where('route', Str::before($routeName, '.'))->first();
        });
    }
}
