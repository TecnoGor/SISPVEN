@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-secondary focus:ring-gray-500 rounded-md shadow-sm']) !!}>
