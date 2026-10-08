@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'form-input w-full border-b-2 border-gray-300 focus:border-blue-500 focus:outline-none']) !!}>
