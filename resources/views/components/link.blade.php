@php
$classes = 'inline underline text-indigo-700 hover:text-indigo-400 ';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
