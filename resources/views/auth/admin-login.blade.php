<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login — Buganda Tourism Board</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased min-h-screen flex items-center justify-center" style="background: linear-gradient(135deg, #07071a 0%, #0d0d2b 100%); font-family: 'Montserrat', sans-serif;">

<div class="w-full max-w-sm px-4">

    {{-- Logo --}}
    <div class="text-center mb-8">
        <a href="{{ route('home') }}" class="inline-flex flex-col items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-20 h-20 rounded-full object-cover">
            <div class="text-3xl font-light text-white" style="font-family: 'Cormorant Garamond', serif;">
                Buganda <span style="color: #d4941a;">Tourism</span>
            </div>
        </a>
        <p class="text-xs tracking-widest uppercase mt-1" style="color: #e6b030;">Admin Panel</p>
    </div>

    {{-- Card --}}
    <div class="bg-white/5 backdrop-blur border border-white/10 p-8">
        <h2 class="text-lg font-light text-white text-center mb-6" style="font-family: 'Cormorant Garamond', serif;">Administrator Sign In</h2>

        @if($errors->any())
            <div class="bg-red-500/20 border border-red-500/40 text-red-300 text-sm px-4 py-3 mb-5">
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('status'))
            <div class="bg-green-500/20 border border-green-500/40 text-green-300 text-sm px-4 py-3 mb-5">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-xs text-gray-400 uppercase tracking-widest mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full bg-white/10 border border-white/20 text-white text-sm px-4 py-3 focus:outline-none focus:border-yellow-500 placeholder-gray-600"
                       placeholder="admin@example.com">
            </div>

            <div class="mb-6">
                <label class="block text-xs text-gray-400 uppercase tracking-widest mb-2">Password</label>
                <input type="password" name="password" required
                       class="w-full bg-white/10 border border-white/20 text-white text-sm px-4 py-3 focus:outline-none focus:border-yellow-500"
                       placeholder="••••••••">
            </div>

            <div class="flex items-center mb-6">
                <input type="checkbox" name="remember" id="remember" class="mr-2">
                <label for="remember" class="text-xs text-gray-400">Remember me</label>
            </div>

            <button type="submit"
                    class="w-full py-3 text-white text-sm font-semibold tracking-wider uppercase transition-opacity hover:opacity-90"
                    style="background: linear-gradient(135deg, #d4941a, #e6b030);">
                Sign In to Admin
            </button>
        </form>
    </div>

    <div class="text-center mt-6">
        <a href="{{ route('home') }}" class="text-gray-500 text-sm hover:text-gray-300 transition-colors">&larr; Back to Site</a>
    </div>
</div>

</body>
</html>
