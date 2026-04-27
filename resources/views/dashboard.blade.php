<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reach — Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
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
                    <p class="text-3xl font-extrabold text-indigo-600 mt-1">
                        {{ $groups->count() }}
                    </p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Published</p>
                    <p class="text-3xl font-extrabold text-green-600 mt-1">
                        {{ $publishedPapers->count() }}
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
                    <p class="text-3xl font-extrabold text-gray-900 mt-1">
                        {{ $pendingInvites->count() }}
                    </p>
                </div>
            </div>

            {{-- ===== MAIN CONTENT ROW ===== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- ===== LEFT — PUBLISHED PAPERS ===== --}}
                {{-- ===== LEFT — PUBLISHED PAPERS ===== --}}
<div class="lg:col-span-2 space-y-4">

    <div class="flex items-center justify-between">
        <h3 class="text-base font-bold text-gray-900">Published Papers</h3>
        <a href="{{ route('groups') }}"
           class="text-sm text-indigo-600 hover:underline">
            View Groups
        </a>
    </div>

    @if($allPublishedPapers->isEmpty())
        <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl p-12 text-center">
            <p class="text-gray-500 font-medium">No published papers yet.</p>
            <p class="text-gray-400 text-sm mt-1">
                Go to your groups and publish a paper to see it here.
            </p>
            <a href="{{ route('groups') }}"
               class="inline-block mt-4 bg-indigo-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                Go to Groups
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($allPublishedPapers as $paper)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
                    <div class="flex items-start gap-4">

                        {{-- File Icon --}}
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-xl shrink-0">
                            {{ $paper->file_type === 'pdf' ? '📄' : '📝' }}
                        </div>

                        {{-- Paper Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-0.5 rounded-full">
                                    Published
                                </span>
                                @if($paper->topic)
                                    <span class="bg-indigo-50 text-indigo-600 text-xs font-medium px-2 py-0.5 rounded-full border border-indigo-200">
                                        {{ $paper->topic }}
                                    </span>
                                @endif
                                <span class="text-xs text-gray-400 uppercase font-mono">
                                    {{ $paper->file_type }}
                                </span>
                            </div>

                            <h4 class="font-bold text-gray-900 text-base leading-snug">
                                {{ $paper->title }}
                            </h4>

                            {{-- ✅ Abstract with "See more" --}}
                            @if($paper->abstract)
                                <div class="mt-2">
                                    <p class="text-sm text-gray-600 leading-relaxed"
                                       id="abstract-{{ $paper->id }}">
                                        {{ Str::limit($paper->abstract, 200) }}
                                    </p>
                                    @if(strlen($paper->abstract) > 200)
                                        <button onclick="toggleAbstract({{ $paper->id }})"
                                                id="toggle-{{ $paper->id }}"
                                                class="text-xs text-indigo-600 hover:underline mt-1 font-semibold">
                                            See more
                                        </button>
                                    @endif
                                </div>
                            @endif

                            <div class="flex items-center gap-3 mt-2 flex-wrap">
                                <span class="text-xs text-gray-400">
                                    By {{ $paper->uploader->first_name }} {{ $paper->uploader->last_name }}
                                </span>
                                <span class="text-xs text-gray-300">·</span>
                                <span class="text-xs text-indigo-500 font-medium">
                                    {{ $paper->group->name }}
                                </span>
                                <span class="text-xs text-gray-300">·</span>
                                <span class="text-xs text-gray-400">
                                    {{ $paper->published_at?->diffForHumans() }}
                                </span>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex flex-col gap-2 shrink-0">
                            <a href="{{ route('papers.download', $paper) }}"
                               class="bg-gray-100 text-gray-600 text-xs px-3 py-1.5 rounded-lg hover:bg-gray-200 transition text-center">
                                Download
                            </a>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>

<script>
    const fullAbstracts = {
        @foreach($allPublishedPapers as $paper)
            {{ $paper->id }}: `{{ addslashes($paper->abstract ?? '') }}`,
        @endforeach
    };

    function toggleAbstract(paperId) {
        const abstractEl = document.getElementById(`abstract-${paperId}`);
        const toggleBtn = document.getElementById(`toggle-${paperId}`);
        const fullText = fullAbstracts[paperId];

        if (toggleBtn.textContent === 'See more') {
            abstractEl.textContent = fullText;
            toggleBtn.textContent = 'See less';
        } else {
            abstractEl.textContent = fullText.substring(0, 200) + '...';
            toggleBtn.textContent = 'See more';
        }
    }
</script>
                {{-- ===== END LEFT ===== --}}

                {{-- ===== RIGHT SIDEBAR ===== --}}
                <div class="space-y-6">

                    {{-- Recent Activity --}}
<div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100">
    <h3 class="text-sm font-bold text-gray-400 uppercase mb-4">
        Recent Activity
    </h3>
    @if($recentActivity->isEmpty())
        <p class="text-xs text-gray-400 text-center py-4">
            No recent activity.
        </p>
    @else
        <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
            @foreach($recentActivity as $activity)
                <a href="{{ $activity['url'] }}"
                   class="flex items-start gap-3 hover:bg-gray-50 rounded-lg p-2 -mx-2 transition cursor-pointer group">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0 mt-0.5 transition group-hover:scale-110
                        {{ $activity['type'] === 'upload' ? 'bg-indigo-100 text-indigo-600 group-hover:bg-indigo-200' : 'bg-yellow-100 text-yellow-600 group-hover:bg-yellow-200' }}">
                        {{ $activity['type'] === 'upload' ? '↑' : 'C' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-700 group-hover:text-gray-900">
                            {{ $activity['message'] }}
                        </p>
                        <p class="text-xs text-gray-500 truncate group-hover:text-gray-700">
                            {{ $activity['detail'] }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $activity['group'] }}
                            · {{ \Carbon\Carbon::parse($activity['time'])->diffForHumans() }}
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

                    {{-- End Recent Activity --}}

                </div>
                {{-- ===== END RIGHT SIDEBAR ===== --}}

            </div>
            {{-- ===== END MAIN CONTENT ROW ===== --}}

        </div>
    </div>
            

    <script>
    
    </script>

</x-app-layout>