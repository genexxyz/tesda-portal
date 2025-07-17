@props([
    'title' => null,
    'breadcrumb' => null
])

@if($title)
    @push('title')
        {{ $title }} - {{ config('app.name') }}
    @endpush
@endif

<div class="bg-white border-b border-gray-200 px-4 py-6 sm:px-6 rounded-lg">
    <div class="max-w-8xl mx-auto">
        <!-- Breadcrumb -->
        @if($breadcrumb)
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route(Auth::user()->role->name .'.dashboard') }}" class="text-gray-700 hover:text-primary inline-flex items-center">
                            <x-icon name="home" style="fas" class="mr-2 w-4 h-4"/>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <x-icon name="chevron-right" style="fas" class="w-4 h-4 text-gray-400 mx-1"/>
                            <span class="text-gray-500">{{ $breadcrumb }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
        @endif

        <!-- Header Title -->
        @if($title)
            <h1 class="text-2xl font-bold text-primary">
                {{ $title }}
            </h1>
            @if ($academicYear)
            <p class="italic text-primary">{{ $academicYear->formatted_description}}</p>
            @endif
            
        @endif
    </div>
</div>