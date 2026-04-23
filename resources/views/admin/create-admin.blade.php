<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ route('super-admin.dashboard') }}" class="text-sm text-indigo-600 hover:underline">
                    ← Back to Dashboard
                </a>
                <h2 class="font-semibold text-xl text-gray-800 mt-1">Create New Admin</h2>
            </div>
            <div class="bg-red-600 text-white px-4 py-1 rounded-full text-sm font-bold">
                SUPER ADMIN
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-8 border border-gray-100">
                
                <h3 class="text-2xl font-bold mb-6 text-gray-900">Create New Admin Account</h3>

                <form method="POST" action="{{ route('super-admin.admins.store') }}" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="first_name" :value="__('First Name')" />
                            <x-text-input id="first_name" class="block mt-1 w-full" type="text" 
                                          name="first_name" :value="old('first_name')" required autofocus />
                            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="last_name" :value="__('Last Name')" />
                            <x-text-input id="last_name" class="block mt-1 w-full" type="text" 
                                          name="last_name" :value="old('last_name')" required />
                            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" 
                                      name="email" :value="old('email')" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" 
                                      name="password" required />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        <p class="text-xs text-gray-400 mt-1">Minimum 8 characters</p>
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                        <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" 
                                      name="password_confirmation" required />
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('super-admin.dashboard') }}" 
                           class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-200 transition">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700 transition">
                            Create Admin Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>