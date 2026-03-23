<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ route('dashboard') }}" class="text-sm text-indigo-600 hover:underline">← Back to Dashboard</a>
                <h2 class="font-semibold text-xl text-gray-800 mt-1">{{ $group->name }}</h2>
            </div>
            <span class="text-xs text-gray-400">Group ID: #{{ $group->id }}</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Members --}}
            <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
                <h3 class="text-sm font-bold text-gray-400 uppercase mb-3">Team Members</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach ($group->users as $member)
                        <span class="px-3 py-1 bg-gray-100 border border-gray-200 rounded-full text-sm text-gray-700">
                            {{ $member->first_name }} {{ $member->last_name }}
                            @if($member->id === $group->user_id)
                                <span class="text-indigo-500 font-bold">(Owner)</span>
                            @endif
                        </span>
                    @endforeach
                </div>
            </div>

            {{-- Upload Paper --}}
            <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Upload New Project</h3>
                <form action="{{ route('papers.store', $group) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <x-text-input name="title" class="w-full" placeholder="e.g. Quantum Research Paper Vol.1" required />
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description (optional)</label>
                        <textarea name="description" rows="2"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="Brief description of the paper..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">File (PDF or Word)</label>
                        <input type="file" name="file" accept=".pdf,.doc,.docx"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                               required />
                        <p class="text-xs text-gray-400 mt-1">Accepted: .pdf, .doc, .docx — Max 20MB</p>
                        @error('file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <x-primary-button>Upload Paper</x-primary-button>
                </form>
            </div>

            {{-- Papers List --}}
            <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Project Files</h3>

                @if($papers->isEmpty())
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-12 text-center">
                        <p class="text-4xl mb-3">📂</p>
                        <p class="text-gray-500 font-medium">No projects uploaded yet.</p>
                        <p class="text-gray-400 text-sm">Use the form above to upload the first paper.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($papers as $paper)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200">
                                <div class="flex items-center gap-4">
                                    {{-- File Icon --}}
                                    <div class="text-3xl">
                                        {{ $paper->file_type === 'pdf' ? '📄' : '📝' }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $paper->title }}</p>
                                        @if($paper->description)
                                            <p class="text-sm text-gray-500">{{ $paper->description }}</p>
                                        @endif
                                        <p class="text-xs text-gray-400 mt-1">
                                            Uploaded by {{ $paper->uploader->first_name }} {{ $paper->uploader->last_name }}
                                            · {{ $paper->created_at->diffForHumans() }}
                                            · <span class="uppercase font-mono">{{ $paper->file_type }}</span>
                                        </p>
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('papers.download', $paper) }}"
                                       class="bg-indigo-600 text-white text-xs px-3 py-1.5 rounded-lg hover:bg-indigo-700 transition">
                                        Download
                                    </a>
                                    @if($paper->user_id === auth()->id())
                                        <form method="POST" action="{{ route('papers.destroy', $paper) }}">
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

        </div>
    </div>
</x-app-layout>