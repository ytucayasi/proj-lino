@props(['link', 'label', 'active' => false])

@php
    $baseClasses = 'w-full ps-2 py-1 border-s-2'; // Clases que siempre se aplican
    $activeClasses = $active
        ? 'border-primary-500 text-primary-500 dark:text-primary-300'
        : 'border-transparent hover:border-primary-300 hover:text-primary-300 dark:hover:text-primary-300';
@endphp

<li class="flex w-full h-fit">
    <a href="{{ $link }}" {{ $attributes->merge(['class' => $baseClasses . ' ' . $activeClasses]) }} wire:navigate>{{ $label }}</a>
</li>