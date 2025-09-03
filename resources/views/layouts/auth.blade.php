@extends('layouts.app')

@section('content')
    <main>
        @yield('auth-content')
    </main>

    @vite(['resources/css/auth.css', 'resources/js/auth.js'])
@endsection
