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

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div id="flash-success" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Stats Row --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Total Admins</p>
                    <p class="text-3xl font-extrabold text-indigo-600 mt-1">{{ $stats['total_admins'] }}</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Total Users</p>
                    <p class="text-3xl font-extrabold text-green-600 mt-1">{{ $stats['total_users'] }}</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Total Groups</p>
                    <p class="text-3xl font-extrabold text-blue-600 mt-1">{{ $stats['total_groups'] }}</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Active Bans</p>
                    <p class="text-3xl font-extrabold text-red-600 mt-1">{{ $stats['active_bans'] }}</p>
                </div>
            </div>

            {{-- Main Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- LEFT — Admins List --}}
                <div class="lg:col-span-2 space-y-4">

                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-bold text-gray-900">Admin Accounts</h3>
                        <a href="{{ route('super-admin.admins.create') }}"
                           class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-indigo-700 transition font-semibold">
                            + Create Admin
                        </a>
                    </div>

                    <div class="space-y-3">
                        @forelse($admins as $admin)
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 font-bold text-lg shrink-0">
                                        {{ strtoupper(substr($admin->first_name, 0, 1)) }}{{ strtoupper(substr($admin->last_name, 0, 1)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-bold text-gray-900 text-base">
                                            {{ $admin->first_name }} {{ $admin->last_name }}
                                        </h4>
                                        <p class="text-sm text-gray-500">{{ $admin->email }}</p>
                                        <div class="flex items-center gap-3 mt-2 flex-wrap">
                                            <span class="text-xs text-gray-400">
                                                ID: <span class="font-mono font-bold">{{ $admin->id }}</span>
                                            </span>
                                            <span class="text-xs text-gray-300">·</span>
                                            <span class="text-xs text-gray-400">
                                                Joined {{ $admin->created_at->diffForHumans() }}
                                            </span>
                                            @if($admin->bans_count > 0)
                                                <span class="text-xs text-gray-300">·</span>
                                                <span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-0.5 rounded-full">
                                                    {{ $admin->bans_count }} active {{ Str::plural('ban', $admin->bans_count) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl p-12 text-center">
                                <p class="text-gray-500 font-medium">No admin accounts yet.</p>
                            </div>
                        @endforelse
                    </div>

                </div>

                {{-- RIGHT — Admin Activity Feed --}}
                <div class="space-y-6">

                    <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold text-gray-400 uppercase">
                                Admin Activity Log
                            </h3>
                            <span class="bg-red-100 text-red-600 text-xs font-bold px-2 py-0.5 rounded-full">
                                LIVE
                            </span>
                        </div>

                        @if($recentActivities->isEmpty())
                            <p class="text-xs text-gray-400 text-center py-4">No activity yet.</p>
                        @else
                            <div class="space-y-3 max-h-[calc(100vh-350px)] overflow-y-auto pr-1">
                                @foreach($recentActivities as $activity)
                                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-100 hover:bg-gray-100 transition">
                                        <div class="flex items-start gap-3">
                                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0 mt-0.5
                                                {{ $activity->action_type === 'ban_user' ? 'bg-red-100 text-red-600' : '' }}
                                                {{ $activity->action_type === 'unban_user' ? 'bg-green-100 text-green-600' : '' }}
                                                {{ $activity->action_type === 'view_group' ? 'bg-blue-100 text-blue-600' : '' }}
                                                {{ $activity->action_type === 'create_admin' ? 'bg-purple-100 text-purple-600' : '' }}">
                                                @if($activity->action_type === 'ban_user') 🚫
                                                @elseif($activity->action_type === 'unban_user') ✅
                                                @elseif($activity->action_type === 'view_group') 👁️
                                                @elseif($activity->action_type === 'create_admin') 👤
                                                @else 📝
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-semibold text-gray-700">
                                                    {{ $activity->admin->first_name }} {{ $activity->admin->last_name }}
                                                </p>
                                                <p class="text-xs text-gray-600 break-words">
                                                    {{ $activity->description }}
                                                </p>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <p class="text-xs text-gray-400">
                                                        {{ $activity->created_at->diffForHumans() }}
                                                    </p>
                                                    @if($activity->ip_address)
                                                        <span class="text-xs text-gray-300">·</span>
                                                        <p class="text-xs text-gray-400 font-mono">
                                                            {{ $activity->ip_address }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>