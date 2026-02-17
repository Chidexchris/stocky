<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title') || {{ config('app.name') }}</title>
    <meta content="Fahim Anzam Dip" name="author">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/favicon.png') }}">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#3c4b64">

    @include('includes.main-css')
    
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('Service Worker registered', reg))
                    .catch(err => console.log('Service Worker registration failed', err));
            });
        }
    </script>
</head>

<body class="c-app">
    @include('layouts.sidebar')
 
    <div class="c-wrapper">
        @if(session()->has('original_superadmin_id'))
            <div class="bg-primary text-white py-2 px-4 text-center font-weight-bold" style="font-size: 13px; border-bottom: 2px solid rgba(255,255,255,0.1);">
                <i class="bi bi-person-bounding-box mr-2"></i> 
                Impersonation Mode: Logged in as {{ auth()->user()->name }}
                <a href="{{ route('saas.stop-impersonate') }}" class="btn btn-sm btn-light ml-3 text-primary font-weight-bold" style="font-size: 11px;">
                    RETURN TO MASTER DASHBOARD
                </a>
            </div>
        @endif
        <header class="c-header c-header-light c-header-fixed">
            @include('layouts.header')
            <div class="c-subheader justify-content-between px-3">
                @yield('breadcrumb')
            </div>
        </header>

        <div class="c-body">
            <main class="c-main">
                @yield('content')
            </main>
        </div>

        @include('layouts.footer')
    </div>

    @include('includes.main-js')
</body>
</html>
