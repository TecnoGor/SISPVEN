@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-sm mt-3 list-none space-y-2']) }}>
        @foreach ((array) $messages as $message)
            <li class="bg-red-100 border-l-4 border-red-600 text-red-600 font-bold p-3 text-sm">{{ $message }}</li>
        @endforeach
    </ul>
@endif
