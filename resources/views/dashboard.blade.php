<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Research Dashboard') }}
            </h2>
            {{-- User ID Badge --}}
            <div class="bg-gray-900 text-white px-4 py-1 rounded-full text-sm">
                Your ID: <span class="font-mono font-bold text-indigo-400">{{ Auth::id() }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Create Group Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold mb-4">Start a New Project</h3>
                <form action="{{ route('groups.store') }}" method="POST" class="flex gap-4">
                    @csrf
                    <x-text-input name="name" placeholder="Group Name (e.g. Quantum Lab)" class="w-full" required />
                    <x-primary-button>Create</x-primary-button>
                </form>
            </div>

            {{-- Groups Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse ($groups as $group)
                    <div class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-100">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="text-xl font-black text-gray-900">{{ $group->name }}</h4>
                                <p class="text-xs text-gray-400">Project ID: #{{ $group->id }}</p>
                            </div>
                            {{-- ✅ View Group Button --}}
    <a href="{{ route('groups.show', $group) }}"
       class="bg-indigo-600 text-white text-xs px-3 py-1.5 rounded-lg hover:bg-indigo-700 transition">
        View Group →
    </a>
                        </div>

                        {{-- Invite Section --}}
                        <div class="mt-4 pt-4 border-t border-gray-50">
                            <form action="{{ route('groups.invite', $group) }}" method="POST" class="space-y-2">
                                @csrf
                                <label class="text-xs font-semibold text-gray-500 uppercase">Invite Researcher (by ID)</label>
                                <div class="flex gap-2">
                                    <x-text-input name="user_id" type="number" placeholder="Enter ID..." class="w-full text-sm" required />
                                    <button class="bg-indigo-600 text-white px-3 py-1 rounded-md text-sm hover:bg-indigo-700">Add</button>
                                </div>
                            </form>
                        </div>

                        {{-- Member List --}}
                        <div class="mt-6">
                            <p class="text-xs font-bold text-gray-400 uppercase mb-2">Team Members</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($group->users as $member)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                        {{ $member->first_name }} {{ $member->last_name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl p-12 text-center">
                        <p class="text-gray-500">You haven't joined or created any groups yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>