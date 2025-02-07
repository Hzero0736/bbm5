@props(['options' => [], 'selected' => null, 'optionValue' => 'id', 'optionLabel' => 'name'])

<select {{ $attributes->merge(['class' => 'form-control']) }}>
    @foreach($options as $option)
    <option value="{{ $option->$optionValue }}"
        {{ $selected == $option->$optionValue ? 'selected' : '' }}>
        {{ $option->$optionLabel }}
    </option>
    @endforeach
</select>