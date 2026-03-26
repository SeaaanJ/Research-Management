<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Home') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Welcome Section --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-8 text-center">
                <div class="flex flex-col items-center justify-center space-y-4">
                    {{-- Icon Placeholder --}}
                    <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                    </div>

                    <h1 class="text-2xl font-bold text-gray-900">
                        Welcome back, {{ Auth::user()->first_name }}!
                    </h1>
                    
                    <p class="text-gray-500 max-w-sm mx-auto">
                        This is your personal dashboard. Start by exploring groups or checking your recent activity.
                    </p>

                    <div class="flex gap-3 mt-4">
                        <a href="{{ route('groups') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                            View Groups
                        </a>
                        <a href="#" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition">
                            Explore
                        </a>
                    </div>
                </div>
            </div>

            {{-- Secondary Content Area (Empty Grid) --}}
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="h-32 border-2 border-dashed border-gray-200 rounded-xl flex items-center justify-center text-gray-400 text-sm italic">
                    Recent Activity placeholder
                </div>
                <div class="h-32 border-2 border-dashed border-gray-200 rounded-xl flex items-center justify-center text-gray-400 text-sm italic">
                    Upcoming Events placeholder
                </div>
                <div class="h-32 border-2 border-dashed border-gray-200 rounded-xl flex items-center justify-center text-gray-400 text-sm italic">
                    Statistics placeholder
                </div>
            </div>

        </div>
    </div>
</x-app-layout>