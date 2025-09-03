@extends('layouts.dashboard')

@php
    $page = 'profile';
    $action = 'index';
@endphp

@section('dashboard-content')
    <x-breadcrumbs></x-breadcrumbs>
    @include('profile.partials.update-profile-information-form')

    @include('profile.partials.update-password-form')

    @include('profile.partials.delete-user-form')
@endsection
