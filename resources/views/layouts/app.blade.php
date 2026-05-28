<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'My Address App')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="font-family: sans-serif; margin: 0; padding: 20px; background-color: #f0f4f8;">
    <div class="container">
        @yield('content')
    </div>

    @yield('scripts')
</body>
</html>

