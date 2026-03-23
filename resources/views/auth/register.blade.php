<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reach — Register</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-200 flex items-center justify-center">
    <div class="min-h-screen flex items-center justify-center bg-gray-200">
        <div class="w-full max-w-5xl bg-white rounded-2xl shadow-lg overflow-hidden flex">

            <!-- Left Image Section -->
            <div class="hidden md:block w-1/2 relative">
                <img src="{{ asset('images/imagelogin.png') }}" alt="Visual"
                     class="w-full h-full object-cover">

                <!-- Optional overlay blur -->
                <div class="absolute inset-0 bg-white/10 backdrop-blur-sm"></div>

                <!-- Center Logo -->
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="bg-white/10 backdrop-blur-sm p-4 rounded-xl">
                        <img src="{{ asset('images/logo.png') }}"
                             class="w-50 h-50 object-contain">
                    </div>
                </div>
            </div>

            <!-- Right Form Section -->
            <div class="w-full md:w-1/2 p-10">
                 {{-- Logo --}}
            <div class="flex items-center gap-2 mb-6">
                <img src="{{ asset('images/logo.png') }}" alt="Reach Logo" class="h-8 w-auto">
                <span class="text-xl font-bold text-gray-900">Reach</span>
            </div>
                <h2 class="text-xl font-semibold text-gray-700 mb-2">Reach</h2>
                <h1 class="text-2xl font-bold text-gray-900 mb-6">
                    Create your account
                </h1>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="first_name" :value="__('First Name')" />
        <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" required />
    </div>
    <div>
        <x-input-label for="last_name" :value="__('Last Name')" />
        <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" required />
    </div>
</div>

<div class="mt-4">
    <x-input-label for="middle_name" :value="__('Middle Name (Optional)')" />
    <x-text-input id="middle_name" class="block mt-1 w-full" type="text" name="middle_name" />
</div>

<div class="mt-4">
    <x-input-label for="institution" :value="__('School / Institution')" />
    <x-text-input id="institution" class="block mt-1 w-full" type="text" name="institution" required />
</div>

                    <!-- Email -->
                    <div class="mb-4">
                        <x-text-input id="email"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                            type="email" name="email" :value="old('email')" required
                            placeholder="Email address" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <x-text-input id="password"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                            type="password" name="password" required
                            placeholder="Password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-6">
                        <x-text-input id="password_confirmation"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                            type="password" name="password_confirmation" required
                            placeholder="Confirm password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <!-- Button -->
                    <button type="submit"
                        class="w-full bg-gray-900 text-white py-3 rounded-lg hover:bg-gray-800 transition">
                        Register
                    </button>

                    <!-- Links -->
                    <div class="mt-4 text-sm text-gray-600">
                        Already have an account?
                        <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">
                            Login here
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</body>