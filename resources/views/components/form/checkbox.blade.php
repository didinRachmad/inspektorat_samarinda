@props(['label', 'name', 'value' => 1, 'checked' => false])

<div class="form-check">
    <input type="checkbox" name="{{ $name }}" id="{{ $name }}" value="{{ $value }}"
        {{ old($name, $checked) ? 'checked' : '' }}
        {{ $attributes->merge([
            'class' => 'form-check-input' . ($errors->has($name) ? ' is-invalid' : ''),
        ]) }}>
    <label class="form-check-label" for="{{ $name }}">
        {{ $label }}
    </label>
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
