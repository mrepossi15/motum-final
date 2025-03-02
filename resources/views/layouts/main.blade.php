<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laravel Trainer App')</title>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
  
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js"></script>
</head>
<body class="bg-black overflow-hidden">
    @if (!isset($hideNavbar) || !$hideNavbar)
        <header>
            <x-nav></x-nav>
        </header>
    @endif

    <main class="{{ $background ?? 'bg-black' }} min-h-screen">
        @if (session()->has('feedback.message'))
            <div class="alert alert-{{ session()->get('feedback.type', 'success') }}">
                {!! session()->get('feedback.message') !!}
            </div>
        @endif

        <div class="{{ isset($hideNavbar) && $hideNavbar ? '' : '' }}">
            @yield('content')
        </div>
    </main>

    <footer></footer>
    @stack('scripts')

</body>
</html>
