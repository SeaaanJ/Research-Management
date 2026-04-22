{{-- resources/views/admin/groups/index.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    All Groups
                </h2>
                <p class="text-sm text-gray-400 mt-0.5">
                    Monitor all research groups on the platform
                </p>
            </div>
            <div class="bg-yellow-500 text-white px-4 py-1 rounded-full text-sm font-bold">
                ADMIN
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Stats --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Total Groups</p>
                    <p class="text-3xl font-extrabold text-indigo-600 mt-1">{{ $groups->count() }}</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Total Members</p>
                    <p class="text-3xl font-extrabold text-green-600 mt-1">{{ $groups->sum('users_count') }}</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Total Papers</p>
                    <p class="text-3xl font-extrabold text-blue-600 mt-1">{{ $groups->sum('papers_count') }}</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Avg Members</p>
                    <p class="text-3xl font-extrabold text-purple-600 mt-1">
                        {{ $groups->count() > 0 ? round($groups->sum('users_count') / $groups->count(), 1) : 0 }}
                    </p>
                </div>
            </div>

            {{-- Groups List --}}
            <div class="space-y-4">
                @forelse($groups as $group)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
                        <div class="flex items-start justify-between gap-4">

                            {{-- Group Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg shrink-0">
                                        {{ strtoupper(substr($group->name, 0, 2)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-bold text-gray-900 text-lg truncate">
                                            {{ $group->name }}
                                        </h3>
                                        <p class="text-xs text-gray-400">
                                            ID: #{{ $group->id }} · Created {{ $group->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>

                                @if($group->description)
                                    <p class="text-sm text-gray-500 mb-3">{{ $group->description }}</p>
                                @endif

                                <div class="flex items-center gap-4 flex-wrap">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-gray-400">Owner:</span>
                                        <span class="text-xs font-semibold text-indigo-600">
                                            {{ $group->owner->first_name }} {{ $group->owner->last_name }}
                                        </span>
                                    </div>
                                    <span class="text-gray-300">·</span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-gray-400">Members:</span>
                                        <span class="text-xs font-bold text-green-600">{{ $group->users_count }}</span>
                                    </div>
                                    <span class="text-gray-300">·</span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-gray-400">Papers:</span>
                                        <span class="text-xs font-bold text-blue-600">{{ $group->papers_count }}</span>
                                    </div>
                                </div>

                                {{-- Members Preview --}}
                                <div class="flex items-center gap-2 mt-3">
                                    <p class="text-xs text-gray-400">Members:</p>
                                    <div class="flex -space-x-2">
                                        @foreach($group->users->take(5) as $member)
                                            <div class="w-7 h-7 rounded-full bg-indigo-100 border-2 border-white flex items-center justify-center text-xs font-bold text-indigo-600"
                                                 title="{{ $member->first_name }} {{ $member->last_name }}">
                                                {{ strtoupper(substr($member->first_name, 0, 1)) }}
                                            </div>
                                        @endforeach
                                        @if($group->users->count() > 5)
                                            <div class="w-7 h-7 rounded-full bg-gray-100 border-2 border-white flex items-center justify-center text-xs font-bold text-gray-600">
                                                +{{ $group->users->count() - 5 }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="shrink-0">
                                <a href="{{ route('admin.groups.show', $group) }}"
                                   class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-indigo-700 transition font-semibold inline-block">
                                    View Details
                                </a>
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl p-12 text-center">
                        <p class="text-gray-500 font-medium">No groups yet.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>