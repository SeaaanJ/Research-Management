
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reach — Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-200 flex items-center justify-center">

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden flex w-full max-w-3xl">

        {{-- Left Side — Image --}}
      <div class="hidden md:block w-1/2 relative">
    <!-- Background Image -->
    <img src="{{ asset('images/imagelogin.png') }}" alt="Login Visual"
         class="w-full h-full object-cover">

    <!-- Centered Logo with blur background -->
    <div class="absolute inset-0 flex items-center justify-center">
        <div class="bg-white/30 backdrop-blur-sm p-4 rounded-xl">
            <img src="{{ asset('images/logo.png') }}" alt="Logo"
                 class="w-40 h-40 rounded-full object-contain">
        </div>
    </div>
</div>

        {{-- Right Side — Form --}}
        <div class="w-full md:w-1/2 p-10 flex flex-col justify-center">

            {{-- Logo --}}
            <div class="flex items-center gap-2 mb-6">
                <img src="{{ asset('images/logo.png') }}" alt="Reach Logo" class="h-8 w-auto">
                <span class="text-xl font-bold text-gray-900">Reach</span>
            </div>

            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Sign into your account</h2>

            {{-- Session Status --}}
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-4">
                    <x-text-input
                        id="email"
                        class="block w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        type="email"
                        name="email"
                        :value="old('email')"
                        placeholder="Email address"
                        required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                {{-- Password --}}
                <div class="mb-4">
                    <x-text-input
                        id="password"
                        class="block w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        type="password"
                        name="password"
                        placeholder="Password"
                        required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                {{-- Login Button --}}
                <button type="submit"
                    class="w-full bg-gray-900 hover:bg-indigo-600 text-white font-semibold py-2 rounded-lg transition duration-200 mb-4">
                    Login
                </button>

                {{-- Forgot Password & Register --}}
                <div class="text-sm text-gray-500 space-y-1">
                    @if (Route::has('password.request'))
                        <div>
                            <a href="{{ route('password.request') }}"
                               class="text-indigo-600 hover:underline">Forgot password?</a>
                        </div>
                    @endif
                    <div>
                        Don't have an account?
                        <a href="{{ route('register') }}"
                           class="text-indigo-600 font-medium hover:underline">Register here</a>
                    </div>
                </div>

                {{-- Remember Me --}}
                <div class="mt-6 pt-6 border-t border-gray-100 text-xs text-gray-400 flex gap-4">
                    <label for="remember_me" class="inline-flex items-center gap-2">
                        <input id="remember_me" type="checkbox"
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                               name="remember">
                        <span>Remember me</span>
                    </label>
                    <a href="#" class="hover:text-gray-600">Terms of use</a>
                    <a href="#" class="hover:text-gray-600">Privacy policy</a>
                </div>

            </form>
        </div>
    </div>

</body>

