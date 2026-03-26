<nav x-data="{ notifOpen: false, activeTab: 'groups' }" class="sticky top-0 flex backdrop-blur-lg bg-white/80 z-50 items-center justify-between px-8 py-5 border-b delay-1">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div class="flex items-center justify-between h-16 w-full">

            
            <div class="flex flex-1 justify-start">
                <a href="/" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="Reach Logo" class="h-14 w-auto rotate-animate-hover">
                    <span class="text-2xl font-bold text-dark-600">Reach</span>
                </a>
            </div>

            
            <div class="flex items-center gap-1 justify-center">
                {{-- Home --}}
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('dashboard') ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 16 16">
                        <path stroke-linejoin="round" d="M2 6.5L8 2l6 4.5V14H10v-4H6v4H2z"/>
                    </svg>
                    Home
                </a>

                {{-- Groups --}}
                <a href="{{ route('groups') }}"
                   class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('groups') ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 16 16">
                        <rect x="1" y="5" width="6" height="10" rx="1.5"/>
                        <rect x="9" y="1" width="6" height="14" rx="1.5"/>
                    </svg>
                    Groups
                </a>

                {{-- Explore --}}
                <a href="#"
                   class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 16 16">
                        <circle cx="8" cy="8" r="6"/>
                        <path stroke-linejoin="round" d="M10.5 5.5l-2 3-3 2 2-3z"/>
                    </svg>
                    Explore
                </a>
            </div>

           
            <div class="flex items-center gap-3 flex-1 justify-end">

                {{-- Notification Bell --}}
                <div class="relative">
                    <button @click="notifOpen = !notifOpen" @click.outside="notifOpen = false"
                            class="relative w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-gray-800 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 16 16">
                            <path d="M8 1a5 5 0 0 1 5 5v2.5l1 2H2l1-2V6a5 5 0 0 1 5-5z"/>
                            <path d="M6.5 13a1.5 1.5 0 0 0 3 0"/>
                        </svg>
                        @if(isset($pendingInvites) && $pendingInvites->count() > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-semibold w-4 h-4 rounded-full flex items-center justify-center border-2 border-white">
                                {{ $pendingInvites->count() }}
                            </span>
                        @endif
                    </button>

                    {{-- Notification Dropdown --}}
                    <div x-show="notifOpen"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 top-[calc(100%+8px)] w-80 bg-white rounded-xl border border-gray-200 shadow-lg overflow-hidden z-50"
                         style="display: none;">
                        
                        {{-- Dropdown Content Header --}}
                        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-800">Pending Invites</span>
                            @if(isset($pendingInvites) && $pendingInvites->count() > 0)
                                <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-2 py-0.5 rounded-full">
                                    {{ $pendingInvites->count() }}
                                </span>
                            @endif
                        </div>

                        {{-- Dropdown Body --}}
                        <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
                            @if(isset($pendingInvites) && $pendingInvites->count() > 0)
                                @foreach($pendingInvites as $invite)
                                    <div class="px-4 py-3">
                                        <p class="text-sm font-semibold text-gray-900">{{ $invite->group->name }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            Invited by {{ $invite->sender->first_name }} {{ $invite->sender->last_name }}
                                            · {{ $invite->created_at->diffForHumans() }}
                                        </p>
                                        <div class="flex gap-2 mt-2">
                                            <form method="POST" action="{{ route('invites.accept', $invite) }}">
                                                @csrf
                                                <button class="bg-indigo-600 text-white text-xs px-3 py-1.5 rounded-lg hover:bg-indigo-700 transition font-semibold">Accept</button>
                                            </form>
                                            <form method="POST" action="{{ route('invites.decline', $invite) }}">
                                                @csrf
                                                <button class="bg-gray-100 text-gray-600 text-xs px-3 py-1.5 rounded-lg hover:bg-gray-200 transition font-semibold">Decline</button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="px-4 py-8 text-center text-sm text-gray-400">
                                    No pending invites
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- User Dropdown --}}
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-2 pl-1 pr-3 py-1 rounded-full border border-gray-200 hover:bg-gray-50 transition">
                            <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-semibold text-indigo-600">
                                {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name, 0, 1)) }}
                            </div>
                            <span class="text-sm font-medium text-gray-800">{{ Auth::user()->first_name }}</span>
                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 12 12">
                                <path d="M3 4.5l3 3 3-3"/>
                            </svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                Log Out
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div> {{-- End Right --}}

        </div>
    </div>
</nav>