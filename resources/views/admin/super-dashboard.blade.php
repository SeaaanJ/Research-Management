{{-- resources/views/admin/super-dashboard.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Super Admin Dashboard
                </h2>
                <p class="text-sm text-gray-400 mt-0.5">
                    Full system control and oversight
                </p>
            </div>
            <div class="bg-red-600 text-white px-4 py-1 rounded-full text-sm font-bold">
                SUPER ADMIN
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 text-center">
                    <div class="py-20">
                        <div class="text-6xl mb-4">👑</div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">
                            Super Admin Dashboard
                        </h3>
                        <p class="text-gray-500">
                            System administration features coming soon.
                        </p>
                        <p class="text-sm text-gray-400 mt-4">
                            Logged in as: <span class="font-mono font-bold text-red-600">{{ Auth::user()->email }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>