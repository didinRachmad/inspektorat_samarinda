@props([
    'label' => null,
    'name',
    'value' => '',
    'required' => false,
])

@if ($label)
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
        @if ($required)
            <span class="text-danger">*</span>
        @endif
    </label>
@endif

<textarea name="{{ $name }}" id="{{ $name }}"
    {{ $attributes->merge([
        'class' => 'form-control form-control-sm' . ($errors->has($name) ? ' is-invalid' : ''),
    ]) }}>{{ old($name, $value) }}</textarea>

@error($name)
    <div class="invalid-feedback">{{ $message }}</div>
@enderror
