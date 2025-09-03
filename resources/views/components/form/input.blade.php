@props([
    'label' => null,
    'name',
    'type' => 'text',
    'value' => '',
    'required' => false,
    'readonly' => false,
    'min' => null,
    'class' => '',
    'format' => false, // tambahkan opsional untuk auto-format angka
])

@php
    $val = old($name, $value);

    // Jika format = true, dan tipe adalah text atau number
    if ($format && is_numeric($val)) {
        // Default: 2 digit desimal, koma sebagai pemisah desimal, titik sebagai pemisah ribuan
        $val = number_format($val, 2, ',', '.');
    }
@endphp

@if ($label)
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
        @if ($required)
            <span class="text-danger">*</span>
        @endif
    </label>
@endif

<input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" value="{{ $val }}"
    {{ $readonly ? 'readonly' : '' }} {{ $min !== null ? "min=$min" : '' }}
    {{ $attributes->merge([
        'class' => 'form-control form-control-sm ' . ($errors->has($name) ? 'is-invalid ' : '') . $class,
    ]) }}>

@error($name)
    <div class="invalid-feedback">{{ $message }}</div>
@enderror
