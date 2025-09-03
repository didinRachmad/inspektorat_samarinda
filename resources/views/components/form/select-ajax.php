@props([
'label' => null,
'name',
'id' => $name,
'url',
'selected' => null,
'selectedText' => null,
'required' => false,
'placeholder' => 'Pilih data...',
])

@if ($label)
<label class="form-label" for="{{ $id }}">
    {{ $label }}
    @if ($required)
    <span class="text-danger">*</span>
    @endif
</label>
@endif

<select id="{{ $id }}" name="{{ $name }}"
    data-select2-ajax="true"
    data-url="{{ $url }}"
    data-placeholder="{{ $placeholder }}"
    {{ $attributes->merge(['class' => 'form-select form-select-sm' . ($errors->has($name) ? ' is-invalid' : '')]) }}>
    @if ($selected && $selectedText)
    <option value="{{ $selected }}" selected>{{ $selectedText }}</option>
    @endif
</select>

@error($name)
<div class="invalid-feedback">{{ $message }}</div>
@enderror