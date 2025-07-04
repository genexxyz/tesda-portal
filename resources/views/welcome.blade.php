<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @vite(['resources/css/app.css', 'resources/js/app.js']);
</head>
<body>
    <div>
        <h1 class="text-3xl font-bold underline">
            Hello world!
        </h1>
        <p class="text-gray-700">This is a simple Laravel application using Tailwind CSS.</p>
        <button class="bg-primary hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Click Me
        </button>
    </div>
</body>
</html>