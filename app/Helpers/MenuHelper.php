<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Models\Menu;

if (!function_exists('currentMenu')) {
    function currentMenu()
    {
        $route = Str::before(Route::currentRouteName(), '.');
        return Cache::rememberForever("menu_{$route}", function () use ($route) {
            return Menu::where('route', $route)->first();
        });
    }
}
