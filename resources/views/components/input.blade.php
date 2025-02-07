@props(['type' => 'text', 'value' => '', 'name' => ''])

<input type="{{ $type }}"
    name="{{ $name }}"
    value="{{ $value }}"
    {{ $attributes->merge(['class' => 'form-control']) }}>