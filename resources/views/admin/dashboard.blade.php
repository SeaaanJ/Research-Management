<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Admin Dashboard
                </h2>
                <p class="text-sm text-gray-400 mt-0.5">
                    Manage platform users and monitor activity
                </p>
            </div>
            <div class="bg-yellow-500 text-white px-4 py-1 rounded-full text-sm font-bold">
                ADMIN
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Stats Row --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Total Users</p>
                    <p class="text-3xl font-extrabold text-indigo-600 mt-1">{{ $totalUsers }}</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Total Groups</p>
                    <p class="text-3xl font-extrabold text-green-600 mt-1">{{ $totalGroups }}</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Total Papers</p>
                    <p class="text-3xl font-extrabold text-blue-600 mt-1">{{ $totalPapers }}</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Published</p>
                    <p class="text-3xl font-extrabold text-purple-600 mt-1">{{ $publishedPapers }}</p>
                </div>
            </div>

            {{-- Main Content Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- LEFT — Users List --}}
                <div class="lg:col-span-2 space-y-4">

                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-bold text-gray-900">All Users</h3>
                        <span class="text-sm text-gray-400">{{ $users->count() }} total</span>
                    </div>

                    @if($users->isEmpty())
                        <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl p-12 text-center">
                            <p class="text-gray-500 font-medium">No users yet.</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($users as $user)
                                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
                                    <div class="flex items-start justify-between gap-4">

                                        {{-- User Info --}}
                                        <div class="flex items-start gap-4 flex-1 min-w-0">
                                            {{-- Avatar --}}
                                            <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-lg shrink-0">
                                                {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                                            </div>

                                            {{-- Details --}}
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-bold text-gray-900 text-base truncate">
                                                    {{ $user->first_name }}
                                                    @if($user->middle_name)
                                                        {{ $user->middle_name }}
                                                    @endif
                                                    {{ $user->last_name }}
                                                </h4>

                                                <p class="text-sm text-gray-500 truncate">
                                                    {{ $user->email }}
                                                </p>

                                                @if($user->institution)
                                                    <p class="text-xs text-indigo-500 mt-0.5 truncate">
                                                        {{ $user->institution }}
                                                    </p>
                                                @endif

                                                <div class="flex items-center gap-3 mt-2 flex-wrap">
                                                    <span class="text-xs text-gray-400">
                                                        ID: <span class="font-mono font-bold">{{ $user->id }}</span>
                                                    </span>
                                                    <span class="text-xs text-gray-300">·</span>
                                                    <span class="text-xs text-gray-400">
                                                        {{ $user->groups_count }} {{ Str::plural('group', $user->groups_count) }}
                                                    </span>
                                                    <span class="text-xs text-gray-300">·</span>
                                                    <span class="text-xs text-gray-400">
                                                        Joined {{ $user->created_at->diffForHumans() }}
                                                    </span>
                                                </div>

                                                {{-- Badges --}}
                                                <div class="flex items-center gap-2 mt-2">
                                                    @if($user->received_invites_count > 0)
                                                        <span class="bg-yellow-100 text-yellow-700 text-xs font-semibold px-2 py-0.5 rounded-full">
                                                            {{ $user->received_invites_count }} pending {{ Str::plural('invite', $user->received_invites_count) }}
                                                        </span>
                                                    @endif
                                                    @if($user->email_verified_at)
                                                        <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-0.5 rounded-full">
                                                            Verified
                                                        </span>
                                                    @else
                                                        <span class="bg-gray-100 text-gray-500 text-xs font-semibold px-2 py-0.5 rounded-full">
                                                            Unverified
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Actions --}}
                                        <div class="flex flex-col gap-2 shrink-0">
    @if($user->activeBan)
        {{-- User is banned --}}
        <div class="bg-red-50 border border-red-200 rounded-lg p-2 text-center">
            <p class="text-xs font-bold text-red-700 uppercase">Banned</p>
            <p class="text-xs text-red-600 mt-0.5">
                {{ $user->activeBan->type === 'permanent' ? 'Permanent' : $user->activeBan->getRemainingTime() }}
            </p>
        </div>
        <button onclick="unbanUser({{ $user->id }}, '{{ $user->first_name }} {{ $user->last_name }}')"
                class="bg-green-600 text-white text-xs px-3 py-1.5 rounded-lg hover:bg-green-700 transition font-semibold">
            Unban User
        </button>
    @else
        {{-- User is not banned --}}
        <button onclick="openBanModal({{ $user->id }}, '{{ $user->first_name }} {{ $user->last_name }}')"
                class="bg-red-600 text-white text-xs px-3 py-1.5 rounded-lg hover:bg-red-700 transition font-semibold">
            Ban User
        </button>
    @endif
    <button class="bg-indigo-600 text-white text-xs px-3 py-1.5 rounded-lg hover:bg-indigo-700 transition">
        View Details
    </button>
</div>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>
                {{-- END LEFT --}}

                {{-- RIGHT — Recent Activity --}}
<div class="space-y-6">

    <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100">
        <h3 class="text-sm font-bold text-gray-400 uppercase mb-4">
            Recent Activity
        </h3>

        @if($recentActivity->isEmpty())
            <p class="text-xs text-gray-400 text-center py-4">
                No recent activity.
            </p>
        @else
            <div class="space-y-3 max-h-[calc(100vh-300px)] overflow-y-auto pr-1">
                @foreach($recentActivity as $activity)
                    <a href="{{ $activity['url'] }}"
                       target="_blank"
                       class="flex items-start gap-3 hover:bg-gray-50 rounded-lg p-2 -mx-2 transition cursor-pointer group">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold shrink-0 mt-0.5 transition group-hover:scale-110
                            {{ $activity['color'] === 'indigo' ? 'bg-indigo-100 text-indigo-600 group-hover:bg-indigo-200' : '' }}
                            {{ $activity['color'] === 'yellow' ? 'bg-yellow-100 text-yellow-600 group-hover:bg-yellow-200' : '' }}
                            {{ $activity['color'] === 'green' ? 'bg-green-100 text-green-600 group-hover:bg-green-200' : '' }}
                            {{ $activity['color'] === 'purple' ? 'bg-purple-100 text-purple-600 group-hover:bg-purple-200' : '' }}">
                            {{ $activity['icon'] }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-700 group-hover:text-gray-900">
                                <span class="text-{{ $activity['color'] }}-600">{{ $activity['user'] }}</span>
                                {{ $activity['message'] }}
                            </p>
                            <p class="text-xs text-gray-600 font-medium truncate group-hover:text-gray-900">
                                {{ $activity['detail'] }}
                            </p>
                            @if($activity['context'])
                                <p class="text-xs text-gray-400 truncate">
                                    {{ $activity['context'] }}
                                </p>
                            @endif
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ \Carbon\Carbon::parse($activity['time'])->diffForHumans() }}
                            </p>
                        </div>
                        <div class="shrink-0 mt-1 opacity-0 group-hover:opacity-100 transition">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100">
        <h3 class="text-sm font-bold text-gray-400 uppercase mb-4">Quick Actions</h3>
        <div class="space-y-2">
            <button class="w-full flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition text-left">
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center text-red-600 text-sm font-bold">
                    🚫
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-700">Ban User</p>
                    <p class="text-xs text-gray-400">Suspend account access</p>
                </div>
            </button>
            <button class="w-full flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition text-left">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 text-sm font-bold">
                    📊
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-700">View Reports</p>
                    <p class="text-xs text-gray-400">Analytics and stats</p>
                </div>
            </button>
            <button class="w-full flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition text-left">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center text-green-600 text-sm font-bold">
                    ⚙️
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-700">System Settings</p>
                    <p class="text-xs text-gray-400">Configure platform</p>
                </div>
            </button>
        </div>
    </div>

</div>
{{-- END RIGHT --}}

            </div>

        </div>
    </div>


{{-- Ban User Modal --}}
<div id="banModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeBanModal()"></div>
    <div class="relative flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 z-10 space-y-5">
            <div class="text-center">
                <div class="text-4xl mb-2">🚫</div>
                <h3 class="text-xl font-black text-gray-900">Ban User</h3>
                <p class="text-sm text-gray-500 mt-1">
                    You are about to ban
                    <span id="banUserName" class="font-bold text-red-600"></span>
                </p>
            </div>

            <div id="banError" class="hidden bg-red-50 border border-red-300 text-red-700 text-sm px-4 py-3 rounded-xl">
                <span id="banErrorText"></span>
            </div>

            <form id="banForm">
                {{-- Ban Type --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ban Type</label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="type" value="temporary" checked
                                   onchange="document.getElementById('durationSection').classList.remove('hidden')"
                                   class="w-4 h-4 text-indigo-600">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Temporary Ban</p>
                                <p class="text-xs text-gray-500">Ban for a specific duration</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="type" value="permanent"
                                   onchange="document.getElementById('durationSection').classList.add('hidden')"
                                   class="w-4 h-4 text-red-600">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Permanent Ban</p>
                                <p class="text-xs text-gray-500">Ban indefinitely</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Duration --}}
                <div id="durationSection" class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Duration</label>
                    <select name="duration"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 bg-white">
                        <option value="1_day">1 Day</option>
                        <option value="1_week">1 Week</option>
                        <option value="1_month">1 Month</option>
                    </select>
                </div>

                {{-- Reason --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                    <textarea name="reason" rows="3" required
                              placeholder="Explain why this user is being banned..."
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 resize-none"></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeBanModal()"
                            class="flex-1 bg-gray-100 text-gray-700 py-2 rounded-xl hover:bg-gray-200 transition font-medium text-sm">
                        Cancel
                    </button>
                    <button type="submit" id="banSubmitBtn"
                            class="flex-1 bg-red-600 text-white py-2 rounded-xl hover:bg-red-700 transition font-bold text-sm">
                        Ban User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentBanUserId = null;

    function openBanModal(userId, userName) {
        currentBanUserId = userId;
        document.getElementById('banUserName').textContent = userName;
        document.getElementById('banModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        document.getElementById('banError').classList.add('hidden');
        document.getElementById('banForm').reset();
        document.getElementById('durationSection').classList.remove('hidden');
    }

    function closeBanModal() {
        document.getElementById('banModal').classList.add('hidden');
        document.body.style.overflow = '';
        currentBanUserId = null;
    }

    document.getElementById('banForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const btn = document.getElementById('banSubmitBtn');
        btn.textContent = 'Banning...';
        btn.disabled = true;

        const formData = new FormData(e.target);

        try {
            const res = await fetch(`/admin/users/${currentBanUserId}/ban`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    type: formData.get('type'),
                    duration: formData.get('duration'),
                    reason: formData.get('reason'),
                }),
            });

            const data = await res.json();

            if (data.success) {
                closeBanModal();
                window.location.reload();
            } else {
                document.getElementById('banError').classList.remove('hidden');
                document.getElementById('banErrorText').textContent = data.message;
            }
        } catch (e) {
            document.getElementById('banError').classList.remove('hidden');
            document.getElementById('banErrorText').textContent = 'Failed to ban user. Please try again.';
        } finally {
            btn.textContent = 'Ban User';
            btn.disabled = false;
        }
    });

    async function unbanUser(userId, userName) {
        if (!confirm(`Remove ban from ${userName}?`)) return;

        try {
            const res = await fetch(`/admin/users/${userId}/ban`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });

            const data = await res.json();

            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Failed to unban user.');
            }
        } catch (e) {
            alert('Failed to unban user. Please try again.');
        }
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeBanModal();
    });
</script>


</x-app-layout>