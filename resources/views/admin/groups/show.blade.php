{{-- resources/views/admin/groups/show.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ route('admin.groups.index') }}" class="text-sm text-indigo-600 hover:underline">
                    ← Back to All Groups
                </a>
                <h2 class="font-semibold text-xl text-gray-800 mt-1">{{ $group->name }}</h2>
                <p class="text-xs text-gray-400 mt-0.5">Group ID: #{{ $group->id }}</p>
            </div>
            <div class="bg-yellow-500 text-white px-4 py-1 rounded-full text-sm font-bold">
                ADMIN VIEW
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Main Layout --}}
            <div class="flex gap-6">

                {{-- LEFT — Papers --}}
                <div class="flex-1 space-y-6">

                    {{-- Papers List --}}
                    <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Papers in this Group</h3>

                        @if($papers->isEmpty())
                            <div class="border-2 border-dashed border-gray-200 rounded-xl p-12 text-center">
                                <p class="text-gray-500 font-medium">No papers uploaded yet.</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($papers as $paper)
                                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-3">

                                        {{-- Paper Info --}}
                                        <div class="flex items-start gap-4">
                                            <div class="text-3xl mt-1">
                                                {{ $paper->file_type === 'pdf' ? '📄' : '📝' }}
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <p class="font-semibold text-gray-900">{{ $paper->title }}</p>

                                                    @if($paper->topic)
                                                        <span class="bg-indigo-50 text-indigo-600 text-xs font-medium px-2 py-0.5 rounded-full border border-indigo-200">
                                                            {{ $paper->topic }}
                                                        </span>
                                                    @endif

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
                                                    · {{ $paper->created_at->diffForHumans() }}
                                                    · <span class="uppercase font-mono">{{ $paper->file_type }}</span>
                                                    @if($paper->published && $paper->published_at)
                                                        · Published {{ $paper->published_at->diffForHumans() }}
                                                    @endif
                                                </p>

                                                {{-- Comments Count --}}
                                                @if($paper->comments_count > 0)
                                                    <div class="mt-2">
                                                        <span class="inline-flex items-center gap-1 bg-yellow-50 text-yellow-700 text-xs font-semibold px-2 py-0.5 rounded-full border border-yellow-200">
                                                            💬 {{ $paper->comments_count }} {{ Str::plural('comment', $paper->comments_count) }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- View Comments --}}
                                        @if($paper->comments->count() > 0)
                                            <div class="pt-3 border-t border-gray-100">
                                                <p class="text-xs font-semibold text-gray-400 uppercase mb-2">Recent Comments</p>
                                                <div class="space-y-2">
                                                    @foreach($paper->comments->take(3) as $comment)
                                                        <div class="bg-white border border-gray-100 rounded-lg p-3 text-xs">
                                                            <p class="font-semibold text-gray-700">
                                                                {{ $comment->user->first_name }} {{ $comment->user->last_name }}
                                                            </p>
                                                            <p class="text-gray-600 mt-0.5">{{ $comment->comment }}</p>
                                                            <p class="text-gray-400 mt-1">{{ $comment->created_at->diffForHumans() }}</p>
                                                        </div>
                                                    @endforeach
                                                    @if($paper->comments->count() > 3)
                                                        <p class="text-xs text-gray-400 text-center">
                                                            + {{ $paper->comments->count() - 3 }} more comments
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>
                {{-- END LEFT --}}

                {{-- RIGHT — Sidebar --}}
                <div class="w-80 space-y-4">

                    {{-- Group Info Card --}}
                    <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
                        <h3 class="text-sm font-bold text-gray-400 uppercase mb-4">Group Information</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-xs text-gray-400">Owner</p>
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ $group->owner->first_name }} {{ $group->owner->last_name }}
                                </p>
                                <p class="text-xs text-gray-400">{{ $group->owner->email }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Created</p>
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ $group->created_at->format('M d, Y') }}
                                </p>
                                <p class="text-xs text-gray-400">{{ $group->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Members Card --}}
                    <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
                        <h3 class="text-sm font-bold text-gray-400 uppercase mb-4">
                            Team Members ({{ $group->users->count() }})
                        </h3>
                        <div class="space-y-3 max-h-64 overflow-y-auto pr-1">
                            @foreach($group->users as $member)
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-semibold text-indigo-600">
                                        {{ strtoupper(substr($member->first_name, 0, 1)) }}{{ strtoupper(substr($member->last_name, 0, 1)) }}
                                    </div>
                                    <div class="text-sm text-gray-700 font-medium flex-1 min-w-0">
                                        <p class="truncate">{{ $member->first_name }} {{ $member->last_name }}</p>
                                        @if($member->id === $group->user_id)
                                            <span class="text-indigo-500 text-xs">Owner</span>
                                        @else
                                            <span class="text-gray-400 text-xs">Member</span>
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
                                <span class="font-bold text-green-600">
                                    {{ $papers->where('published', true)->count() }}
                                </span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Drafts</span>
                                <span class="font-bold text-yellow-600">
                                    {{ $papers->where('published', false)->count() }}
                                </span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Total Comments</span>
                                <span class="font-bold text-purple-600">
                                    {{ $papers->sum('comments_count') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Recent Activity --}}
                    <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
                        <h3 class="text-sm font-bold text-gray-400 uppercase mb-4">Recent Activity</h3>
                        @if($recentActivity->isEmpty())
                            <p class="text-xs text-gray-400 text-center py-4">No activity yet.</p>
                        @else
                            <div class="space-y-3 max-h-72 overflow-y-auto pr-1">
                                @foreach($recentActivity as $activity)
                                    <div class="flex items-start gap-2">
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs shrink-0
                                            {{ $activity['color'] === 'indigo' ? 'bg-indigo-100 text-indigo-600' : 'bg-yellow-100 text-yellow-600' }}">
                                            {{ $activity['icon'] }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-semibold text-gray-700">
                                                {{ $activity['user'] }}
                                            </p>
                                            <p class="text-xs text-gray-500 truncate">
                                                {{ $activity['message'] }} {{ $activity['detail'] }}
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                {{ \Carbon\Carbon::parse($activity['time'])->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>
                {{-- END RIGHT --}}

            </div>

        </div>
    </div>
</x-app-layout>