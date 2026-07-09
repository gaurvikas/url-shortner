<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="stylesheet" href="{{ asset('app.css') }}">
    </head>
    <body>
        <main class="guest-page">
            <div class="guest-card">
                {{ $slot }}
            </div>
        </main>
    </body>
</html>