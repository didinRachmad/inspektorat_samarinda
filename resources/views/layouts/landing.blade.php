@extends('layouts.app')

@section('content')
    <main>
        @yield('landing-content')
    </main>
    @vite(['resources/css/landing.css', 'resources/js/landing.js'])
@endsection
