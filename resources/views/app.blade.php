<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Kinhold</title>
        <meta name="description" content="Kinhold — the family hub for calendars, tasks, documents, and more.">
        <meta name="theme-color" content="#1B3A4B">

        <!-- Open Graph -->
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="Kinhold">
        <meta property="og:title" content="Kinhold — Your Family Hub">
        <meta property="og:description" content="An open-source family hub for calendars, tasks, documents, and more. Privacy-first, AI-powered, self-hostable.">
        <meta property="og:image" content="{{ url('/images/og-card.png') }}">
        <meta property="og:url" content="{{ url('/') }}">

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="Kinhold — Your Family Hub">
        <meta name="twitter:description" content="An open-source family hub for calendars, tasks, documents, and more. Privacy-first, AI-powered, self-hostable.">
        <meta name="twitter:image" content="{{ url('/images/og-card.png') }}">

        <!-- Canonical -->
        <link rel="canonical" href="{{ url('/') }}">

        <!-- Favicon & Icons -->
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="manifest" href="/manifest.json">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet">

        <!-- Vite -->
        @vite(['resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div id="app"></div>
    </body>
</html>
