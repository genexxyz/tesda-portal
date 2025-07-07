@props(['for' => null])

@php
    $field = $for ?? $attributes->wire('model')->value();
@endphp

@error($field)
    <p {{ $attributes->merge(['class' => 'mt-1 text-xs text-red-600']) }}>{{ $message }}</p>
@enderror