<div {{ $attributes->merge(['class' => 'border-l-4 font-bold p-3 text-sm']) }} 
    x-data="{ show: true }"
    x-show="show"
    x-transition
    x-init="setTimeout(() => show = false, 3000)"
>
    {{ $slot }}
</div>