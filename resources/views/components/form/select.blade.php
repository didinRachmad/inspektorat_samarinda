@props([
    'label' => null,
    'name',
    'options' => [],
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

<select name="{{ $name }}" id="{{ $name }}"
    {{ $attributes->merge([
        'class' => 'form-select form-select-sm select2' . ($errors->has($name) ? ' is-invalid' : ''),
    ]) }}>
    <option value="">-- Pilih {{ $label ?? $name }} --</option>
    @foreach ($options as $optionValue => $optionLabel)
        <option value="{{ $optionValue }}" {{ old($name, $value) == $optionValue ? 'selected' : '' }}>
            {{ $optionLabel }}
        </option>
    @endforeach
</select>

@error($name)
    <div class="invalid-feedback">{{ $message }}</div>
@enderror
