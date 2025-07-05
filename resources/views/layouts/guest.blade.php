<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
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

<body class="antialiased">
    <div class="flex flex-col md:flex-row">
        <div class="bg-primary w-full md:w-1/2 flex justify-center items-center">
            <img class="w-30 md:w-50 rounded-full m-10" src="{{url('storage/assets/img/default_logo.png')}}" alt="">
        </div>
        <div class="flex justify-center items-center w-full md:w-1/2 bg-primary md:bg-accent">
            {{ $slot }}
        </div>
    </div>

    @livewireScripts
</body>

</html>