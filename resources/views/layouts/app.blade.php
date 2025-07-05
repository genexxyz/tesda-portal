<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@stack('title', config('app.name'))</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-100">
    
    <div class="flex flex-col min-h-screen">
        <x-partials.navigation />
        
        <main class="flex-1 py-6">
            <div class="ml-4 mr-4 md:ml-20">
                {{ $slot }}
            </div>
        </main>
    </div>

    @livewireScripts
    @livewire('wire-elements-modal')
</body>
</html>