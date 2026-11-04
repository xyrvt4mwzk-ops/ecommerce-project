<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'DigitalMarket') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="app-shell">
            @unless(request()->routeIs('login', 'register', 'password.*', 'verification.*'))
                @include('layouts.navigation')
            @endunless
            <x-ui.flash />
            <main class="min-h-screen py-8 sm:py-12"><div class="page-wrap">{{ $slot }}</div></main>
        </div>
    </body>
</html>
