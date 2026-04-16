<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Welcome back, {{ Auth::user()->first_name }}
                </h2>
                <p class="text-sm text-gray-400 mt-0.5">
                    Here's what's happening across your research groups.
                </p>
            </div>
            <div class="bg-gray-900 text-white px-4 py-1 rounded-full text-sm">
                Your ID: <span class="font-mono font-bold text-indigo-400">{{ Auth::id() }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div id="flash-success"
                     class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex justify-between items-center transition-opacity duration-500">
                    <span>{{ session('success') }}</span>
                    <button onclick="dismissFlash('flash-success')"
                            class="text-green-500 hover:text-green-700 ml-4 font-bold text-lg">&times;</button>
                </div>
            @endif
            @if(session('error'))
                <div id="flash-error"
                     class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex justify-between items-center transition-opacity duration-500">
                    <span>{{ session('error') }}</span>
                    <button onclick="dismissFlash('flash-error')"
                            class="text-red-500 hover:text-red-700 ml-4 font-bold text-lg">&times;</button>
                </div>
            @endif

            {{-- ===== PENDING INVITES ===== --}}
            @if($pendingInvites->count() > 0)
                <div class="bg-white shadow-sm rounded-2xl p-6 border border-indigo-100">
                    <h3 class="text-sm font-bold text-indigo-600 uppercase mb-4 flex items-center gap-2">
                        Pending Invites
                        <span class="bg-indigo-600 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                            {{ $pendingInvites->count() }}
                        </span>
                    </h3>
                    <div class="space-y-3">
                        @foreach($pendingInvites as $invite)
                            <div class="flex items-center justify-between bg-indigo-50 border border-indigo-100 rounded-xl px-4 py-3">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $invite->group->name }}</p>
                                    <p class="text-xs text-gray-500">
                                        Invited by
                                        <span class="font-medium">
                                            {{ $invite->sender->first_name }} {{ $invite->sender->last_name }}
                                        </span>
                                        · {{ $invite->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('invites.accept', $invite) }}">
                                        @csrf
                                        <button class="bg-indigo-600 text-white text-xs px-4 py-1.5 rounded-lg hover:bg-indigo-700 transition font-semibold">
                                            Accept
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('invites.decline', $invite) }}">
                                        @csrf
                                        <button class="bg-gray-100 text-gray-600 text-xs px-4 py-1.5 rounded-lg hover:bg-gray-200 transition font-semibold">
                                            Decline
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ===== STATS ROW ===== --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Groups</p>
                    <p class="text-3xl font-extrabold text-indigo-600 mt-1">{{ $groups->count() }}</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Published</p>
                    <p class="text-3xl font-extrabold text-green-600 mt-1">
                        {{ $groups->sum(fn($g) => $g->papers()->where('published', true)->count()) }}
                    </p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Drafts</p>
                    <p class="text-3xl font-extrabold text-yellow-500 mt-1">
                        {{ $groups->sum(fn($g) => $g->papers()->where('published', false)->count()) }}
                    </p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Pending Invites</p>
                    <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ $pendingInvites->count() }}</p>
                </div>
            </div>

            {{-- ===== MAIN CONTENT ROW ===== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- ===== LEFT — GROUPS ===== --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Groups Grid --}}
                    <div class="space-y-4">
                        <h3 class="text-base font-bold text-gray-900">Your Groups</h3>
                        @forelse ($groups as $group)
                            <div class="bg-white shadow-sm rounded-2xl p-5 border border-gray-100">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h4 class="text-lg font-black text-gray-900">{{ $group->name }}</h4>
                                        <p class="text-xs text-gray-400">ID: #{{ $group->id }}</p>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('groups.show', $group) }}"
                                           class="bg-indigo-600 text-white text-xs px-3 py-1.5 rounded-lg hover:bg-indigo-700 transition">
                                            View
                                        </a>
                                        @if($group->user_id === auth()->id())
                                            <button onclick="openDeleteModal({{ $group->id }}, '{{ $group->name }}')"
                                                    class="bg-red-100 text-red-600 text-xs px-3 py-1.5 rounded-lg hover:bg-red-200 transition">
                                                Delete
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                {{-- Invite --}}
                                <div class="pt-3 border-t border-gray-100">
                                    <form action="{{ route('groups.invite', $group) }}" method="POST">
                                        @csrf
                                        <label class="text-xs font-semibold text-gray-400 uppercase">
                                            Send Invite by User ID
                                        </label>
                                        <div class="flex gap-2 mt-1">
                                            <x-text-input name="user_id" type="number"
                                                          placeholder="Enter user ID..."
                                                          class="flex-1 text-sm" required />
                                            <button class="bg-indigo-600 text-white px-3 py-1 rounded-lg text-sm hover:bg-indigo-700">
                                                Send
                                            </button>
                                        </div>
                                    </form>

                                    {{-- Pending sent invites --}}
                                    @php $sentInvites = $group->invites->where('status', 'pending'); @endphp
                                    @if($sentInvites->count() > 0)
                                        <div class="mt-2 flex flex-wrap gap-1">
                                            @foreach($sentInvites as $sent)
                                                <span class="text-xs bg-yellow-50 border border-yellow-200 text-yellow-700 px-2 py-0.5 rounded-full">
                                                    ID #{{ $sent->receiver_id }} — waiting
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                {{-- Members --}}
                                <div class="mt-4 flex flex-wrap gap-2">
                                    @foreach ($group->users as $member)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                            {{ $member->first_name }} {{ $member->last_name }}
                                            @if($member->id === $group->user_id)
                                                <span class="text-indigo-500">(Owner)</span>
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl p-12 text-center">
                                <p class="text-gray-500">No groups yet.</p>
                                <p class="text-gray-400 text-sm mt-1">Create one above to get started.</p>
                            </div>
                        @endforelse
                    </div>

                </div>
                {{-- ===== END LEFT ===== --}}

                {{-- ===== RIGHT SIDEBAR — COMBINED TABS ===== --}}
                <div>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-6">
 
                        {{-- Tab Headers --}}
                        <div class="flex border-b border-gray-100">
                            <button onclick="switchTab('published')" id="tab-published"
                                    class="flex-1 py-3 text-xs font-bold uppercase tracking-wide text-indigo-600 border-b-2 border-indigo-600 bg-indigo-50 transition">
                                📄 Recent
                            </button>
                            <button onclick="switchTab('activity')" id="tab-activity"
                                    class="flex-1 py-3 text-xs font-bold uppercase tracking-wide text-gray-400 border-b-2 border-transparent hover:text-gray-600 transition">
                                💬 Activity
                            </button>
                        </div>
 
                        {{-- Recently Published Panel --}}
                        <div id="panel-published" class="p-5">
                            @if($recentPublished->isEmpty())
                                <p class="text-xs text-gray-400 text-center py-6">No published papers yet.</p>
                            @else
                                <div class="space-y-3 max-h-[520px] overflow-y-auto pr-1">
                                    @foreach($recentPublished as $paper)
                                        <a href="{{ route('groups.show', $paper->group_id) }}"
                                           class="block hover:bg-gray-50 rounded-lg p-2 -mx-2 transition">
                                            <div class="flex items-start gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center text-sm shrink-0">
                                                    {{ ($paper->file_type ?? '') === 'pdf' ? '📄' : '📝' }}
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-semibold text-gray-900 truncate">
                                                        {{ $paper->title }}
                                                    </p>
                                                    <p class="text-xs text-gray-400 mt-0.5">
                                                        {{ $paper->group->name }}
                                                        · {{ $paper->published_at?->diffForHumans() }}
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
 
                        {{-- Recent Activity Panel --}}
                        <div id="panel-activity" class="p-5 hidden">
                            @if($recentActivity->isEmpty())
                                <p class="text-xs text-gray-400 text-center py-6">No recent activity.</p>
                            @else
                                <div class="space-y-3 max-h-[520px] overflow-y-auto pr-1">
                                    @foreach($recentActivity as $activity)
                                        <a href="{{ route('groups.show', $activity['group_id']) }}"
                                           class="flex items-start gap-3 hover:bg-gray-50 rounded-lg p-2 -mx-2 transition">
                                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs shrink-0 mt-0.5
                                                {{ $activity['type'] === 'upload' ? 'bg-indigo-100 text-indigo-600' : 'bg-yellow-100 text-yellow-600' }}">
                                                {{ $activity['type'] === 'upload' ? '↑' : '💬' }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-semibold text-gray-700">
                                                    {{ $activity['message'] }}
                                                </p>
                                                <p class="text-xs text-gray-500 truncate">
                                                    {{ $activity['detail'] }}
                                                </p>
                                                <p class="text-xs text-gray-400 mt-0.5">
                                                    {{ $activity['group'] }}
                                                    · {{ \Carbon\Carbon::parse($activity['time'])->diffForHumans() }}
                                                </p>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
 
                    </div>
                </div>

            </div>

            {{-- ===== DISCOVERY FEED ===== --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-black text-gray-900">Discover Research</h3>
                        <p class="text-sm text-gray-400 mt-0.5">
                            Latest published papers from the research community
                        </p>
                    </div>
                    <a href="{{ route('explore') }}"
                       class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-indigo-700 transition font-semibold">
                        Explore More
                    </a>
                </div>

                {{-- Discovery Grid --}}
                <div id="discoveryFeed"
                     class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    {{-- Skeleton loaders --}}
                    @for($i = 0; $i < 6; $i++)
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3 animate-pulse">
                            <div class="h-3 bg-gray-200 rounded w-1/3"></div>
                            <div class="h-4 bg-gray-200 rounded w-full"></div>
                            <div class="h-4 bg-gray-200 rounded w-4/5"></div>
                            <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                            <div class="h-10 bg-gray-100 rounded w-full"></div>
                            <div class="flex gap-2">
                                <div class="h-5 bg-gray-100 rounded w-16"></div>
                                <div class="h-5 bg-gray-100 rounded w-16"></div>
                            </div>
                        </div>
                    @endfor
                </div>

                {{-- Load More --}}
                <div class="text-center mt-6">
                    <button id="loadMoreBtn"
                            onclick="loadMoreDiscovery()"
                            class="hidden bg-white border border-gray-200 text-gray-600 text-sm px-6 py-2 rounded-lg hover:bg-gray-50 transition font-medium">
                        Load More
                    </button>
                </div>
            </div>
            {{-- ===== END DISCOVERY FEED ===== --}}

        </div>
    </div>

    {{-- ===== DELETE MODAL ===== --}}
    <div id="deleteModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
        <div class="relative flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 z-10 space-y-5">
                <div class="text-center">
                    <h3 class="text-xl font-black text-gray-900">Delete Group</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        You are about to delete
                        <span id="modalGroupName" class="font-bold text-red-600"></span>.
                        This cannot be undone.
                    </p>
                </div>
                <div class="bg-gray-50 border border-dashed border-gray-300 rounded-xl p-4 text-center">
                    <p class="text-xs text-gray-400 mb-1">Type this exactly to confirm:</p>
                    <p id="confirmStringDisplay"
                       class="text-2xl font-mono font-black text-red-600 tracking-widest transition-opacity duration-200">
                    </p>
                    {{-- Progress bar --}}
                    <div class="mt-2 h-1 bg-gray-200 rounded-full overflow-hidden">
                        <div id="stringProgress"
                             class="h-full bg-indigo-500 transition-all duration-100"
                             style="width: 100%">
                        </div>
                    </div>
                </div>
                <div id="globalError"
                     class="hidden bg-red-50 border border-red-300 text-red-700 text-sm px-4 py-3 rounded-xl flex items-center gap-2">
                    <span>&#10008;</span><span id="globalErrorText"></span>
                </div>
                <div id="globalSuccess"
                     class="hidden bg-green-50 border border-green-300 text-green-700 text-sm px-4 py-3 rounded-xl flex items-center gap-2">
                    <span>&#10004;</span><span id="globalSuccessText"></span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmation Code</label>
                    <input type="text" id="confirmStringInput"
                           placeholder="Type the code above..."
                           autocomplete="off"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm font-mono tracking-widest focus:outline-none focus:ring-2 focus:ring-red-400 transition" />
                    <div id="confirmError"
                         class="hidden mt-2 text-xs text-red-600 bg-red-50 border border-red-200 px-3 py-2 rounded-lg flex items-center gap-1">
                        <span>&#9888;</span><span id="confirmErrorText"></span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Your Password</label>
                    <div class="relative">
                        <input type="password" id="deletePassword"
                               placeholder="Enter your password..."
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 transition" />
                        <button type="button" onclick="togglePassword()"
                                class="absolute right-3 top-2 text-gray-400 hover:text-gray-600">
                            <svg id="eyeIcon" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    <div id="passwordError"
                         class="hidden mt-2 text-xs text-red-600 bg-red-50 border border-red-200 px-3 py-2 rounded-lg flex items-center gap-1">
                        <span>&#9888;</span><span id="passwordErrorText"></span>
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
    {{-- ===== END DELETE MODAL ===== --}}

    {{-- Discovery Feed Script --}}
    <script>
        let discoveryPage = 1;
        let discoveryLoading = false;

        const TOPICS = [
            'Artificial Intelligence', 'Climate Change', 'Medicine',
            'Biology', 'Physics', 'Economics', 'Psychology', 'Computer Science'
        ];

        async function loadDiscovery(page = 1, append = false) {
            if (discoveryLoading) return;
            discoveryLoading = true;

            const topic = TOPICS[Math.floor(Math.random() * TOPICS.length)];

            try {
                const params = new URLSearchParams({ topic, page, query: '' });
                const res    = await fetch(`/explore/search?${params}`, {
                    headers: { Accept: 'application/json' },
                });
                const data   = await res.json();

                if (!data.papers || data.papers.length === 0) return;

                renderDiscoveryCards(data.papers, append);
                document.getElementById('loadMoreBtn').classList.remove('hidden');
            } catch (e) {
                console.error('Discovery feed failed:', e);
            } finally {
                discoveryLoading = false;
            }
        }

        function renderDiscoveryCards(papers, append = false) {
            const feed = document.getElementById('discoveryFeed');

            if (!append) feed.innerHTML = '';

            papers.slice(0, 6).forEach(paper => {
                const topicBadges = (paper.topics || [])
                    .slice(0, 2)
                    .map(t => `<span class="bg-indigo-50 text-indigo-600 text-xs px-2 py-0.5 rounded-full border border-indigo-100">${t}</span>`)
                    .join('');

                const card = document.createElement('div');
                card.className = 'bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3 hover:shadow-md transition flex flex-col';
                card.innerHTML = `
                    <div class="flex items-center gap-2 flex-wrap">
                        ${paper.is_oa
                            ? '<span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-0.5 rounded-full">Open Access</span>'
                            : '<span class="bg-gray-100 text-gray-500 text-xs font-semibold px-2 py-0.5 rounded-full">Restricted</span>'
                        }
                        ${paper.year !== 'N/A'
                            ? `<span class="bg-blue-50 text-blue-600 text-xs font-semibold px-2 py-0.5 rounded-full">${paper.year}</span>`
                            : ''
                        }
                    </div>

                    <h4 class="font-bold text-gray-900 text-sm leading-snug line-clamp-2 flex-1">
                        ${paper.title}
                    </h4>

                    <p class="text-xs text-gray-500 font-medium truncate">${paper.authors}</p>

                    ${paper.journal
                        ? `<p class="text-xs text-indigo-500 italic truncate">${paper.journal}</p>`
                        : ''
                    }

                    <p class="text-xs text-gray-500 leading-relaxed line-clamp-3">
                        ${paper.abstract}
                    </p>

                    <div class="flex items-center justify-between flex-wrap gap-2 pt-2 border-t border-gray-50">
                        <div class="flex flex-wrap gap-1">${topicBadges}</div>
                        <span class="text-xs text-gray-400">
                            ${paper.cited_by.toLocaleString()} citations
                        </span>
                    </div>

                    ${paper.url
                        ? `<a href="${paper.url}" target="_blank"
                               class="block w-full text-center bg-indigo-600 text-white text-xs font-semibold py-2 rounded-lg hover:bg-indigo-700 transition mt-1">
                               View Paper
                           </a>`
                        : `<span class="block w-full text-center bg-gray-100 text-gray-400 text-xs font-semibold py-2 rounded-lg mt-1 cursor-not-allowed">
                               No Link Available
                           </span>`
                    }
                `;
                feed.appendChild(card);
            });
        }

        function loadMoreDiscovery() {
            discoveryPage++;
            loadDiscovery(discoveryPage, true);
        }

        // Load discovery feed on page load
        document.addEventListener('DOMContentLoaded', () => {
            loadDiscovery(1, false);
        });



        function switchTab(tab) {
            const tabs = ['published', 'activity'];
            tabs.forEach(t => {
                const btn   = document.getElementById('tab-' + t);
                const panel = document.getElementById('panel-' + t);
                if (t === tab) {
                    btn.className   = 'flex-1 py-3 text-xs font-bold uppercase tracking-wide text-indigo-600 border-b-2 border-indigo-600 bg-indigo-50 transition';
                    panel.classList.remove('hidden');
                } else {
                    btn.className   = 'flex-1 py-3 text-xs font-bold uppercase tracking-wide text-gray-400 border-b-2 border-transparent hover:text-gray-600 transition';
                    panel.classList.add('hidden');
                }
            });
        }
    </script>

</x-app-layout>