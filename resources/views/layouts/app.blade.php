<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Sembark URL Shortener') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.navigation')

    @isset($header)
        <header>
            {{ $header }}
        </header>
    @endisset

    <main>
        {{ $slot }}
    </main>
</body>
</html>
