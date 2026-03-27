<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reach — Welcome</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<x-app-layout>

 

    <x-slot name="header">

    
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Groups</h2>
            <div class="bg-gray-900 text-white px-4 py-1 rounded-full text-sm">
                Your ID: <span class="font-mono font-bold text-indigo-400">{{ Auth::id() }}</span>
            </div>
        </div>
    </x-slot>

    {{-- Create Group Modal --}}
    <div x-data="{ createOpen: false }" @keydown.escape.window="createOpen = false">

        <div class="py-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Page Header Row --}}
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Your Projects</h3>
                    <button @click="createOpen = true"
                            class="inline-flex items-center gap-2 bg-indigo-600 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16">
                            <path d="M8 3v10M3 8h10"/>
                        </svg>
                        New Group
                    </button>
                </div>

                {{-- Groups Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse ($groups as $group)
                        <div class="bg-white shadow-sm sm:rounded-xl p-6 border border-gray-100">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h4 class="text-lg font-black text-gray-900">{{ $group->name }}</h4>
                                    <p class="text-xs text-gray-400 mt-0.5">Project ID: #{{ $group->id }}</p>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('groups.show', $group) }}"
                                       class="bg-indigo-600 text-white text-xs px-3 py-1.5 rounded-lg hover:bg-indigo-700 transition font-medium">
                                        View →
                                    </a>
                                    @if($group->user_id === auth()->id())
                                        <button onclick="openDeleteModal({{ $group->id }}, '{{ $group->name }}')"
                                                class="bg-red-50 text-red-600 text-xs px-3 py-1.5 rounded-lg hover:bg-red-100 transition font-medium border border-red-100">
                                            Delete
                                        </button>
                                    @endif
                                </div>
                            </div>

                            {{-- Members --}}
                            <div class="mt-4">
                                <p class="text-xs font-semibold text-gray-400 uppercase mb-2">Team Members</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($group->users as $member)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                            {{ $member->first_name }} {{ $member->last_name }}
                                            @if($member->id === $group->user_id)
                                                <span class="ml-1 text-indigo-500">(Owner)</span>
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Invite Section --}}
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <form action="{{ route('groups.invite', $group) }}" method="POST" class="space-y-2">
                                    @csrf
                                    <label class="text-xs font-semibold text-gray-400 uppercase">Invite by User ID</label>
                                    <div class="flex gap-2">
                                        <x-text-input name="user_id" type="number" placeholder="Enter user ID..." class="w-full text-sm" required />
                                        <button class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-indigo-700 transition font-medium">
                                            Send
                                        </button>
                                    </div>
                                </form>

                                @php $sentInvites = $group->invites->where('status', 'pending'); @endphp
                                @if($sentInvites->count() > 0)
                                    <div class="mt-3 flex flex-wrap gap-1">
                                        @foreach($sentInvites as $sent)
                                            <span class="text-xs bg-yellow-50 border border-yellow-200 text-yellow-700 px-2 py-0.5 rounded-full">
                                                ID #{{ $sent->receiver_id }} — waiting
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl p-12 text-center">
                            <p class="text-gray-500 mb-4">You haven't joined or created any groups yet.</p>
                            <button @click="createOpen = true"
                                    class="bg-indigo-600 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                                Create your first group
                            </button>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>

        {{-- CREATE GROUP MODAL --}}
        <div x-show="createOpen"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center px-4"
             style="display: none;">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="createOpen = false"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 z-10"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">

                <h3 class="text-xl font-black text-gray-900 mb-1">Start a New Project</h3>
                <p class="text-sm text-gray-500 mb-6">Give your research group a name to get started.</p>

                <form action="{{ route('groups.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Group Name</label>
                        <x-text-input name="name"
                                      placeholder="e.g. Quantum Lab, Climate Study..."
                                      class="w-full"
                                      required
                                      autofocus />
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="createOpen = false"
                                class="flex-1 bg-gray-100 text-gray-700 py-2.5 rounded-xl hover:bg-gray-200 transition font-medium text-sm">
                            Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 bg-indigo-600 text-white py-2.5 rounded-xl hover:bg-indigo-700 transition font-bold text-sm">
                            Create Group
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>


    {{-- DELETE MODAL (unchanged from your original) --}}
    <div id="deleteModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
        <div class="relative flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 z-10 space-y-5">
                <div class="text-center">
                    
                    <h3 class="text-xl font-black text-gray-900">Delete Group</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        You are about to delete
                        <span id="modalGroupName" class="font-bold text-red-600"></span>.
                        This action <span class="font-bold">cannot be undone.</span>
                    </p>
                </div>
                <div class="bg-gray-50 border border-dashed border-gray-300 rounded-xl p-4 text-center">
                    <p class="text-xs text-gray-400 mb-1">Type this exactly to confirm:</p>
                    <p id="confirmStringDisplay" class="text-2xl font-mono font-black text-red-600 tracking-widest"></p>

                    <div class="mt-3 h-1.5 w-full bg-gray-200 rounded-full overflow-hidden">
                    <div id="stringProgress" class="h-full bg-indigo-500 rounded-full transition-all duration-100"></div>
                    </div>
                </div>
                <div id="globalError" class="hidden bg-red-50 border border-red-300 text-red-700 text-sm px-4 py-3 rounded-xl flex items-center gap-2">
                    <span id="globalErrorText"></span>
                </div>
                <div id="globalSuccess" class="hidden bg-green-50 border border-green-300 text-green-700 text-sm px-4 py-3 rounded-xl flex items-center gap-2">
                   <span id="globalSuccessText"></span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmation Code</label>
                    <input type="text" id="confirmStringInput" placeholder="Type the code above..."
                           autocomplete="off"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm font-mono tracking-widest focus:outline-none focus:ring-2 focus:ring-red-400 transition" />
                    <div id="confirmError" class="hidden mt-2 text-xs text-red-600 bg-red-50 border border-red-200 px-3 py-2 rounded-lg flex items-center gap-1">
                        <span id="confirmErrorText"></span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Your Password</label>
                    <div class="relative">
                        <input type="password" id="deletePassword" placeholder="Enter your password..."
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 transition" />
                        <button type="button" onclick="togglePassword()"
                                class="absolute right-3 top-2 text-gray-400 hover:text-gray-600">
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" 
                                class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            </button>
                    </div>
                    <div id="passwordError" class="hidden mt-2 text-xs text-red-600 bg-red-50 border border-red-200 px-3 py-2 rounded-lg flex items-center gap-1">
                        <span id="passwordErrorText"></span>
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeDeleteModal()"
                            class="flex-1 bg-gray-100 text-gray-700 py-2 rounded-xl hover:bg-gray-200 transition font-medium text-sm">
                        Cancel
                    </button>
                    <button type="button" onclick="submitDelete()" id="deleteBtn"
                            class="flex-1 bg-red-600 text-white py-2 rounded-xl hover:bg-red-700 transition font-bold text-sm">
                        Delete Group
                    </button>
                </div>
            </div>
        </div>
    </div>



</x-app-layout>