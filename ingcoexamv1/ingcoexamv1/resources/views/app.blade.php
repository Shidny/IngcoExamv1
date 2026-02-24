<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" >
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    
    <title inertia>{{ config('app.name', 'Login') }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" crossorigin="anonymous" />
    
    <!-- Prevent FOUC -->
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        #app {
            height: 100%;
        }
    </style>
   
    @vite('resources/js/app.js')
    @inertiaHead
</head>
<body class="hold-transition {{ request()->is('dashboard') ? 'sidebar-mini layout-fixed' : (request()->is('register') ? 'register-page' : 'login-page') }}">
    @inertia
</body>
</html>