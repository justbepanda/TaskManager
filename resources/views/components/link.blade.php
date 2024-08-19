@php
$classes = 'inline underline text-indigo-400 hover:text-indigo-700 hover:border-gray-300';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
