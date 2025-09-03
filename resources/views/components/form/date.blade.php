@props([
    'label' => null,
    'name',
    'value' => '',
    'required' => false,
    'readonly' => false,
    'min' => null,
    'class' => '',
])

@if ($label)
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
        @if ($required)
            <span class="text-danger">*</span>
        @endif
    </label>
@endif

<input type="date" name="{{ $name }}" id="{{ $name }}" value="{{ old($name, $value) }}"
    @if ($readonly) readonly @endif @if ($min !== null) min="{{ $min }}" @endif
    {{ $attributes->merge([
        'class' => 'form-control form-control-sm ' . ($errors->has($name) ? 'is-invalid ' : '') . $class,
    ]) }}>

@error($name)
    <div class="invalid-feedback">{{ $message }}</div>
@enderror
