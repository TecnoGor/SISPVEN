<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn bg-primary text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white whitespace-nowrap']) }}>
    {{ $slot }}
</button>
