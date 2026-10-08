@props(['active'])

@php
$classes = ($active ?? false)
    ? 'font-bold px-4 py-2 border border-green-600 text-center md:text-left text-base text-white bg-green-600 shadow-lg rounded-t transition duration-200 ease-in-out hover:cursor-pointer hover:scale-105'
    : 'font-bold px-4 py-2 border border-gray-200 text-center md:text-left text-base text-default bg-white rounded-t transition duration-200 ease-in-out hover:cursor-pointer hover:bg-green-50 hover:text-green-700';
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
