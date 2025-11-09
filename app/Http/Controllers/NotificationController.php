<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $notifications = $user->notifications()->latest()->paginate(20); // bisa diubah jumlah per halaman

        return view('notifications.index', compact('notifications'));
    }
}
