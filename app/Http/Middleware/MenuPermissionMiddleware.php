<?php

namespace App\Http\Middleware;

use App\Models\Menu;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class MenuPermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $permission)
    {
        // Ambil nama rute saat ini
        $routeName = Route::currentRouteName();
        // Memisahkan nama rute menjadi array berdasarkan tanda titik
        $routeParts = explode('.', $routeName);
        // Mengambil bagian pertama dari nama rute
        $mainRoute = $routeParts[0];

        // Cari menu berdasarkan nama rute
        $menu = Menu::where('route', $mainRoute)->first();

        if (!$menu) {
            abort(403, 'Menu tidak ditemukan.');
        }

        // Cek apakah pengguna memiliki izin untuk menu ini
        if (!Auth::user()->hasMenuPermission($menu->id, $permission)) {
            abort(403, 'Anda tidak memiliki akses untuk tindakan ini.');
        }

        return $next($request);
    }
}
