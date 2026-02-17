<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Platform Master | {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('images/favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background: #0f172a;
            color: #f8fafc;
            font-family: 'Inter', sans-serif;
        }
        .glass-panel {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .accent-gradient {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-md">
    <div class="text-center mb-10">
        <h1 class="text-4xl font-black tracking-tighter mb-2">PLATFORM<span class="text-blue-500">MASTER</span></h1>
        <p class="text-slate-400 font-medium tracking-wide border-t border-slate-800 pt-2 mx-auto w-48 uppercase text-xs">Administrative Gateway</p>
    </div>

    <div class="glass-panel rounded-2xl p-8 shadow-2xl">
        <div class="mb-8">
            <h2 class="text-xl font-bold mb-1">Authorization Required</h2>
            <p class="text-slate-400 text-sm">Enter your secure credentials to manage the platform ecosystem.</p>
        </div>

        @if(session('error'))
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-xl mb-6 text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('superadmin.login') }}">
            @csrf

            <div class="space-y-6">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2" for="email">Admin Interface Identification</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500"><i class="bi bi-shield-lock"></i></span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                               class="w-full bg-slate-900 border border-slate-700 rounded-xl py-3 pl-10 pr-4 focus:outline-none focus:border-blue-500 transition-colors"
                               placeholder="admin.email@system.internal">
                    </div>
                    @error('email')
                        <p class="text-red-400 text-xs mt-2 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2" for="password">Encryption Key</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500"><i class="bi bi-key"></i></span>
                        <input id="password" type="password" name="password" required
                               class="w-full bg-slate-900 border border-slate-700 rounded-xl py-3 pl-10 pr-4 focus:outline-none focus:border-blue-500 transition-colors"
                               placeholder="••••••••••••••••">
                    </div>
                </div>

                <button type="submit" class="w-full accent-gradient hover:opacity-90 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-500/20 flex items-center justify-center transition-all transform hover:-translate-y-1">
                    ACCESS SYSTEM
                    <i class="bi bi-arrow-right-short text-xl ml-2"></i>
                </button>
            </div>
        </form>
    </div>

    <p class="mt-8 text-center text-slate-600 text-xs tracking-widest uppercase">
        System Node: 127.0.0.1 // encrypted session
    </p>
</div>

</body>
</html>
