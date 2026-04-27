<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reach — Groups</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<x-app-layout>


    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ route('groups') }}" class="text-sm text-indigo-600 hover:underline">&larr; Back</a>
                <h2 class="font-semibold text-xl text-gray-800 mt-1">{{ $group->name }}</h2>
            </div>
            <span class="text-xs text-gray-400">Group ID: #{{ $group->id }}</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ── Upload Modal Trigger ── --}}
            <div x-data="{ openUpload: false }">
                <div class="flex justify-end mb-6">
                    <button
                        @click="openUpload = true"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition">
                        + Upload Project
                    </button>
                </div>

                {{-- Upload Modal --}}
                <div x-show="openUpload"
                     x-transition
                     class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center"
                     style="display: none;">
                    <div @click.outside="openUpload = false"
                         class="bg-white w-full max-w-lg rounded-xl p-6 shadow-lg border border-gray-100">

                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Upload New Project</h3>
                            <button @click="openUpload = false" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
                        </div>

                        <form action="{{ route('papers.store', $group) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf

                            {{-- Title --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                <x-text-input name="title" class="w-full" placeholder="e.g. Quantum Research Paper Vol.1" required />
                                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Topic --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Topic / Subject</label>
                                <select name="topic" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                                    <option value="" disabled selected>Select a topic...</option>
                                    <optgroup label="Sciences">
                                        <option value="Biology">Biology</option>
                                        <option value="Chemistry">Chemistry</option>
                                        <option value="Physics">Physics</option>
                                        <option value="Environmental Science">Environmental Science</option>
                                        <option value="Astronomy">Astronomy</option>
                                    </optgroup>
                                    <optgroup label="Technology">
                                        <option value="Computer Science">Computer Science</option>
                                        <option value="Artificial Intelligence">Artificial Intelligence</option>
                                        <option value="Cybersecurity">Cybersecurity</option>
                                        <option value="Data Science">Data Science</option>
                                        <option value="Software Engineering">Software Engineering</option>
                                    </optgroup>
                                    <optgroup label="Medicine & Health">
                                        <option value="Medicine">Medicine</option>
                                        <option value="Public Health">Public Health</option>
                                        <option value="Nursing">Nursing</option>
                                        <option value="Psychology">Psychology</option>
                                        <option value="Nutrition">Nutrition</option>
                                    </optgroup>
                                    <optgroup label="Social Sciences">
                                        <option value="Sociology">Sociology</option>
                                        <option value="Political Science">Political Science</option>
                                        <option value="Economics">Economics</option>
                                        <option value="Anthropology">Anthropology</option>
                                        <option value="Education">Education</option>
                                    </optgroup>
                                    <optgroup label="Humanities">
                                        <option value="History">History</option>
                                        <option value="Philosophy">Philosophy</option>
                                        <option value="Literature">Literature</option>
                                        <option value="Linguistics">Linguistics</option>
                                        <option value="Arts">Arts</option>
                                    </optgroup>
                                    <optgroup label="Engineering">
                                        <option value="Civil Engineering">Civil Engineering</option>
                                        <option value="Mechanical Engineering">Mechanical Engineering</option>
                                        <option value="Electrical Engineering">Electrical Engineering</option>
                                        <option value="Chemical Engineering">Chemical Engineering</option>
                                    </optgroup>
                                    <optgroup label="Business">
                                        <option value="Business Administration">Business Administration</option>
                                        <option value="Finance">Finance</option>
                                        <option value="Marketing">Marketing</option>
                                        <option value="Management">Management</option>
                                    </optgroup>
                                    <optgroup label="Other">
                                        <option value="Law">Law</option>
                                        <option value="Agriculture">Agriculture</option>
                                        <option value="Architecture">Architecture</option>
                                        <option value="Other">Other</option>
                                    </optgroup>
                                </select>
                                @error('topic') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Description --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description (optional)</label>
                                <textarea name="description" rows="2"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    placeholder="Brief description..."></textarea>
                            </div>

                            {{-- File --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">File (PDF or Word)</label>
                                <input type="file" name="file" accept=".pdf,.doc,.docx"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    required />
                                <p class="text-xs text-gray-400 mt-1">Accepted: .pdf, .doc, .docx — Max 20MB</p>
                                @error('file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex justify-end">
                                <x-primary-button>Upload Paper</x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            {{-- END Upload Modal --}}

            {{-- ── Main Layout ── --}}
            <div class="flex gap-6">

                {{-- LEFT — Papers --}}
                <div class="flex-1 space-y-6">

                    {{-- Flash Messages --}}
                    @if(session('success'))
                        <div id="flash-success" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg transition-opacity duration-500">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div id="flash-error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg transition-opacity duration-500">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Papers List --}}
                    <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Project Files</h3>

                        @if($papers->isEmpty())
                            <div class="border-2 border-dashed border-gray-200 rounded-xl p-12 text-center">
                                <p class="text-gray-500 font-medium">No projects uploaded yet.</p>
                                <p class="text-gray-400 text-sm mt-1">Click "Upload Project" to add the first paper.</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach ($papers as $paper)
                                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-3">

                                        {{-- Paper Info --}}
                                        <div class="flex items-start gap-4">
                                            <div class="text-3xl mt-1">
                                                {{ $paper->file_type === 'pdf' ? '📄' : '📝' }}
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <p class="font-semibold text-gray-900">{{ $paper->title }}</p>

                                                    @if($paper->published)
                                                        <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-0.5 rounded-full">
                                                            Published
                                                        </span>
                                                    @else
                                                        <span class="bg-gray-100 text-gray-500 text-xs font-semibold px-2 py-0.5 rounded-full">
                                                            Draft
                                                        </span>
                                                    @endif
                                                </div>

                                                @if($paper->description)
                                                    <p class="text-sm text-gray-500 mt-0.5">{{ $paper->description }}</p>
                                                @endif

                                                <p class="text-xs text-gray-400 mt-1">
                                                    Uploaded by {{ $paper->uploader->first_name }} {{ $paper->uploader->last_name }}
                                                    &middot; {{ $paper->created_at->diffForHumans() }}
                                                    &middot; <span class="uppercase font-mono">{{ $paper->file_type }}</span>
                                                    @if($paper->published && $paper->published_at)
                                                        &middot; Published {{ $paper->published_at->diffForHumans() }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Actions Row --}}
                                        {{-- Actions Row --}}
<div class="flex items-center gap-2 flex-wrap pt-2 border-t border-gray-100">

    {{-- View --}}
    <button onclick="openViewModal(
        {{ $paper->id }},
        '{{ addslashes($paper->title) }}',
        '{{ addslashes($paper->topic ?? '') }}',
        '{{ $paper->file_type }}',
        {{ $paper->published ? 'true' : 'false' }},
        '{{ route('papers.view', $paper) }}',
        '{{ route('papers.download', $paper) }}',
        {{ $group->user_id === auth()->id() ? 'true' : 'false' }}
    )"
        class="bg-gray-900 text-white text-xs px-3 py-1.5 rounded-lg hover:bg-indigo-600 transition">
        View
    </button>

    {{-- Download --}}
    <a href="{{ route('papers.download', $paper) }}"
       class="bg-indigo-600 text-white text-xs px-3 py-1.5 rounded-lg hover:bg-indigo-700 transition">
        Download
    </a>

    {{-- ✅ Publish / Unpublish with Abstract --}}
    @if($paper->user_id === auth()->id() || $group->user_id === auth()->id())
        @if($paper->published)
            <button onclick="unpublishPaper({{ $paper->id }})"
                    class="bg-green-100 text-green-700 text-xs px-3 py-1.5 rounded-lg hover:bg-green-200 transition font-semibold">
                Unpublish
            </button>
        @else
            <button onclick="openPublishModal({{ $paper->id }}, '{{ addslashes($paper->title) }}')"
                    class="bg-yellow-100 text-yellow-700 text-xs px-3 py-1.5 rounded-lg hover:bg-yellow-200 transition font-semibold border border-yellow-300">
                Publish
            </button>
        @endif
    @else
        <button disabled
                title="Only the uploader or group owner can publish"
                class="bg-gray-100 text-gray-400 text-xs px-3 py-1.5 rounded-lg cursor-not-allowed font-semibold border border-gray-200">
            Publish
        </button>
    @endif

    {{-- Delete --}}
    @if($paper->user_id === auth()->id())
        <form method="POST" action="{{ route('papers.destroy', $paper) }}" class="ml-auto">
            @csrf
            @method('DELETE')
            <button type="submit"
                    onclick="return confirm('Delete this paper?')"
                    class="bg-red-100 text-red-600 text-xs px-3 py-1.5 rounded-lg hover:bg-red-200 transition">
                Delete
            </button>
        </form>
    @endif

</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    {{-- END Papers List --}}

                </div>
                {{-- END LEFT --}}

                {{-- RIGHT — Sidebar --}}
                <div class="w-64 space-y-4">

                    {{-- Members Card --}}
                    <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
                        <h3 class="text-sm font-bold text-gray-400 uppercase mb-4">Team Members</h3>
                        <div class="space-y-3">
                            @foreach ($group->users as $member)
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-semibold text-indigo-600">
                                        {{ strtoupper(substr($member->first_name, 0, 1)) }}{{ strtoupper(substr($member->last_name, 0, 1)) }}
                                    </div>
                                    <div class="text-sm text-gray-700 font-medium">
                                        {{ $member->first_name }} {{ $member->last_name }}
                                        @if($member->id === $group->user_id)
                                            <span class="block text-indigo-500 text-xs">Owner</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Stats Card --}}
                    <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
                        <h3 class="text-sm font-bold text-gray-400 uppercase mb-4">Stats</h3>

                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Total Papers</span>
                                <span class="font-bold text-gray-900">{{ $papers->count() }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Published</span>
                                <span class="font-bold text-green-600">{{ $papers->where('published', true)->count() }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Drafts</span>
                                <span class="font-bold text-yellow-600">{{ $papers->where('published', false)->count() }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Members</span>
                                <span class="font-bold text-indigo-600">{{ $group->users->count() }}</span>
                            </div>
                        </div>

                        {{-- Publish / Unpublish button --}}
                        <div class="mt-5 pt-4 border-t border-gray-100">
                            @if($group->user_id === auth()->id())
                                @if($papers->where('published', false)->count() > 0)
                                    <form method="POST" action="{{ route('papers.publishAll', $group) }}">
                                        @csrf
                                        <button type="submit"
                                            class="w-full bg-indigo-600 text-white text-xs font-semibold py-2 rounded-lg hover:bg-indigo-700 transition flex items-center justify-center gap-2">
                                            Publish All
                                            <span class="bg-indigo-500 text-white text-xs px-1.5 py-0.5 rounded-full">
                                                {{ $papers->where('published', false)->count() }}
                                            </span>
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('papers.unpublishAll', $group) }}">
                                        @csrf
                                        <button type="submit"
                                            onclick="return confirm('Unpublish all papers?')"
                                            class="w-full bg-gray-900 text-white text-xs font-semibold py-2 rounded-lg hover:bg-red-600 transition flex items-center justify-center gap-2">
                                            Unpublish All
                                            <span class="bg-gray-700 text-white text-xs px-1.5 py-0.5 rounded-full">
                                                {{ $papers->where('published', true)->count() }}
                                            </span>
                                        </button>
                                    </form>
                                @endif
                            @else
                                <button disabled
                                    title="Only the group owner can publish all papers"
                                    class="w-full bg-gray-100 text-gray-400 text-xs font-semibold py-2 rounded-lg cursor-not-allowed flex items-center justify-center gap-2 border border-gray-200">
                                    Publish All
                                    <span class="bg-gray-200 text-gray-400 text-xs px-1.5 py-0.5 rounded-full">
                                        {{ $papers->where('published', false)->count() }}
                                    </span>
                                </button>
                            @endif
                        </div>
                    </div>

                </div>
                {{-- END RIGHT --}}

            </div>
            {{-- END Main Layout --}}

        </div>
    </div>


    {{-- ===== PUBLISH MODAL ===== --}}
<div id="publishModal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closePublishModal()"></div>
    <div class="relative flex items-center justify-center min-h-screen px-4 py-6">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-8 z-10 space-y-5">
            
            {{-- Header --}}
            <div class="text-center">
                <div class="text-4xl mb-2">📢</div>
                <h3 class="text-xl font-black text-gray-900">Publish Paper</h3>
                <p class="text-sm text-gray-500 mt-1">
                    <span id="publishPaperTitle" class="font-bold text-indigo-600"></span>
                </p>
            </div>

            {{-- Info Box --}}
            <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4">
                <p class="text-sm text-indigo-700 font-semibold mb-2">
                    ℹ️ Publishing this paper will:
                </p>
                <ul class="text-xs text-indigo-600 space-y-1 ml-5 list-disc">
                    <li>Make it visible on all user dashboards</li>
                    <li>Display the abstract to help others understand your work</li>
                    <li>Allow others to view and download the paper</li>
                </ul>
            </div>

            {{-- Error Message --}}
            <div id="publishError" class="hidden bg-red-50 border border-red-300 text-red-700 text-sm px-4 py-3 rounded-xl flex items-center gap-2">
                <span>⚠️</span>
                <span id="publishErrorText"></span>
            </div>

            {{-- Form --}}
            <form id="publishForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Abstract <span class="text-red-500">*</span>
                    </label>
                    <textarea id="publishAbstract"
                              name="abstract"
                              rows="8"
                              required
                              placeholder="Write a clear and concise abstract summarizing your research, methodology, findings, and conclusions..."
                              class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"></textarea>
                    <div class="flex justify-between items-center mt-1">
                        <p class="text-xs text-gray-400">Minimum 100 characters required</p>
                        <p id="charCount" class="text-xs text-gray-400">0 / 2000</p>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button"
                            onclick="closePublishModal()"
                            class="flex-1 bg-gray-100 text-gray-700 py-2.5 rounded-xl hover:bg-gray-200 transition font-medium text-sm">
                        Cancel
                    </button>
                    <button type="submit"
                            id="publishSubmitBtn"
                            class="flex-1 bg-indigo-600 text-white py-2.5 rounded-xl hover:bg-indigo-700 transition font-bold text-sm">
                        Publish Paper
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>


    {{--VIEW PAPER MODAL--}}
    <div id="viewPaperModal" class="fixed inset-0 z-50 hidden">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeViewModal()"></div>

        {{-- Dialog --}}
        <div class="relative flex items-center justify-center min-h-screen px-4 py-6 pointer-events-none">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-7xl pointer-events-auto flex flex-col" style="height: 90vh;">

                {{-- ── Modal Header ── --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 shrink-0">
                    <div class="flex items-center gap-3 min-w-0">
                        <span id="viewPaperIcon" class="text-2xl shrink-0"></span>
                        <div class="min-w-0">
                            <h3 id="viewPaperTitle" class="text-lg font-black text-gray-900 truncate"></h3>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span id="viewPaperStatus" class="text-xs font-semibold px-2 py-0.5 rounded-full shrink-0"></span>
                                <span id="viewPaperTopic" class="text-xs text-gray-400 truncate"></span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0 ml-4">
                        <a id="viewPaperDownloadLink" href="#"
                           class="bg-indigo-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg hover:bg-indigo-700 transition">
                            Download
                        </a>
                        <button onclick="closeViewModal()"
                                class="text-gray-400 hover:text-gray-600 text-2xl leading-none ml-1">
                            &times;
                        </button>
                    </div>
                </div>
                {{-- END Modal Header --}}

                {{-- ── Modal Body ── --}}
                <div class="flex-1 flex overflow-hidden">

                    {{-- LEFT — PDF / Doc Viewer --}}
                    <div class="flex-1 relative overflow-hidden bg-gray-100">

                        {{-- Loading spinner --}}
                        <div id="viewPaperLoading"
                             class="absolute inset-0 flex items-center justify-center bg-gray-50 z-10">
                            <div class="text-center space-y-3">
                                <svg class="w-8 h-8 animate-spin text-indigo-500 mx-auto"
                                     fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor"
                                          d="M4 12a8 8 0 018-8v8z"/>
                                </svg>
                                <p class="text-sm text-gray-400">Loading document...</p>
                            </div>
                        </div>

                        {{-- Fallback (unsupported file type) --}}
                        <div id="viewPaperFallback"
                             class="hidden absolute inset-0 flex items-center justify-center bg-gray-50 z-10">
                            <div class="text-center space-y-3 p-8">
                                <p class="text-gray-700 font-semibold">
                                    Preview not available for this file type
                                </p>
                                <p class="text-sm text-gray-400">
                                    Download the file to view its contents.
                                </p>
                                <a id="viewPaperFallbackDownload" href="#"
                                   class="inline-block bg-indigo-600 text-white text-sm font-semibold px-5 py-2 rounded-xl hover:bg-indigo-700 transition">
                                    Download File
                                </a>
                            </div>
                        </div>

                        {{-- Scrollable PDF container --}}
                        <div id="scrollContainer" class="relative w-full h-full overflow-auto">
                            <div id="pdfViewer"
                                 class="flex flex-col items-center gap-4 py-4 min-h-full">
                            </div>
                        </div>

                    </div>
                    {{-- END LEFT --}}

                    {{-- RIGHT — Comments Sidebar --}}
                    <div class="w-72 border-l border-gray-100 bg-white flex flex-col shrink-0">

                        {{-- Sidebar Header --}}
                        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between shrink-0">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wide">Comments</h4>
                            <span id="commentCount"
                                  class="bg-indigo-100 text-indigo-600 text-xs font-bold px-2 py-0.5 rounded-full min-w-[1.5rem] text-center">
                                0
                            </span>
                        </div>

                        {{-- Comments List --}}
                        <div id="commentsList"
                             class="flex-1 overflow-y-auto p-3 space-y-2 min-h-0">
                            <p class="text-xs text-gray-400 text-center mt-4">Loading comments...</p>
                        </div>

                        {{-- Comment Input --}}
                        <div class="p-3 border-t border-gray-100 shrink-0">
                            <textarea
                                id="commentInput"
                                rows="3"
                                placeholder="Write a comment..."
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none">
                            </textarea>
                            <button
                                id="commentSubmitBtn"
                                onclick="window.submitComment()"
                                class="mt-2 w-full bg-indigo-600 text-white text-xs font-semibold py-2
                                       rounded-lg hover:bg-indigo-700 transition disabled:opacity-60">
                                Post Comment
                            </button>
                        </div>

                    </div>
                    {{-- END Comments Sidebar --}}

                </div>
                {{-- END Modal Body --}}

            </div>
        </div>
    </div>
    {{-- END VIEW PAPER MODAL --}}


    {{-- ── Blade → JS globals ── --}}
    <script>
        window.__authUserId     = {{ auth()->id() }};
        window.__groupOwnerId   = {{ $group->user_id }};
        window.__authFirstName  = @json(auth()->user()->first_name);
        window.__authLastName   = @json(auth()->user()->last_name);


        let currentPublishPaperId = null;

    function openPublishModal(paperId, paperTitle) {
        currentPublishPaperId = paperId;
        document.getElementById('publishPaperTitle').textContent = paperTitle;
        document.getElementById('publishModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Reset form
        document.getElementById('publishForm').reset();
        document.getElementById('publishError').classList.add('hidden');
        document.getElementById('charCount').textContent = '0 / 2000';
        document.getElementById('charCount').className = 'text-xs text-gray-400';
    }

    function closePublishModal() {
        document.getElementById('publishModal').classList.add('hidden');
        document.body.style.overflow = '';
        currentPublishPaperId = null;
    }

    // Character counter
    document.addEventListener('DOMContentLoaded', () => {
        const abstractInput = document.getElementById('publishAbstract');
        const charCount = document.getElementById('charCount');
        
        if (abstractInput) {
            abstractInput.addEventListener('input', (e) => {
                const length = e.target.value.length;
                charCount.textContent = `${length} / 2000`;
                
                if (length < 100) {
                    charCount.className = 'text-xs text-red-500 font-semibold';
                } else if (length <= 2000) {
                    charCount.className = 'text-xs text-green-600 font-semibold';
                } else {
                    charCount.className = 'text-xs text-red-500 font-semibold';
                }
            });
        }
    });

    // Publish form submission
    document.getElementById('publishForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const abstract = document.getElementById('publishAbstract').value.trim();
        const btn = document.getElementById('publishSubmitBtn');
        const errorDiv = document.getElementById('publishError');
        const errorText = document.getElementById('publishErrorText');

        // Validate
        if (abstract.length < 100) {
            errorDiv.classList.remove('hidden');
            errorText.textContent = 'Abstract must be at least 100 characters long.';
            return;
        }

        if (abstract.length > 2000) {
            errorDiv.classList.remove('hidden');
            errorText.textContent = 'Abstract must not exceed 2000 characters.';
            return;
        }

        btn.textContent = 'Publishing...';
        btn.disabled = true;
        errorDiv.classList.add('hidden');

        try {
            const res = await fetch(`/papers/${currentPublishPaperId}/publish-with-abstract`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ abstract }),
            });

            const data = await res.json();

            if (data.success) {
                closePublishModal();
                window.location.reload();
            } else {
                errorDiv.classList.remove('hidden');
                errorText.textContent = data.message || 'Failed to publish paper.';
            }
        } catch (error) {
            console.error('Publish error:', error);
            errorDiv.classList.remove('hidden');
            errorText.textContent = 'Network error. Please try again.';
        } finally {
            btn.textContent = 'Publish Paper';
            btn.disabled = false;
        }
    });

    // Unpublish function
    async function unpublishPaper(paperId) {
        if (!confirm('Unpublish this paper? It will be removed from all dashboards.')) return;

        try {
            const res = await fetch(`/papers/${paperId}/unpublish`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });

            const data = await res.json();

            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Failed to unpublish paper.');
            }
        } catch (error) {
            console.error('Unpublish error:', error);
            alert('Network error. Please try again.');
        }
    }

    // Close on Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closePublishModal();
        }
    });
    </script>

</x-app-layout>