<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

        <title>{{ config('app.name', 'CSV-JSON') }}</title>

        @routes
        @vite('resources/js/app.js')
        @inertiaHead
    </head>

    <body class="bg-bg-main overflow-y-hidden">
        @inertia
    </body>
</html>
