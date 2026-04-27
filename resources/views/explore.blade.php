<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reach — Explore</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<x-app-layout>
    
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Discover Open Access Research Papers
            </h2>
            <span class="text-xs text-gray-400">Powered by OpenAlex</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Search Bar --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <h3 class="text-lg font-bold text-gray-900 mb-4">Search Research Papers</h3>

    {{-- Main Search Row --}}
    <div class="flex gap-3 mb-3">
        <input type="text"
               id="searchInput"
               placeholder="e.g. Artificial Intelligence, Climate Change..."
               class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" />
        <button onclick="searchPapers(1)"
                id="searchBtn"
                class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition">
            Search
        </button>
    </div>

    {{-- Filters Row --}}
    <div class="flex flex-wrap gap-3 items-end">

        {{-- Topic Filter --}}
        <div class="flex flex-col gap-1">
            <label class="text-xs font-medium text-gray-500">Topic</label>
            <select id="topicFilter"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                <option value="">All Topics</option>
                <option value="Artificial Intelligence">Artificial Intelligence</option>
                <option value="Machine Learning">Machine Learning</option>
                <option value="Climate Change">Climate Change</option>
                <option value="Biology">Biology</option>
                <option value="Medicine">Medicine</option>
                <option value="Physics">Physics</option>
                <option value="Economics">Economics</option>
                <option value="Psychology">Psychology</option>
                <option value="Computer Science">Computer Science</option>
                <option value="Chemistry">Chemistry</option>
                <option value="Engineering">Engineering</option>
                <option value="Mathematics">Mathematics</option>
                <option value="Sociology">Sociology</option>
                <option value="Law">Law</option>
            </select>
        </div>

        {{-- Year From --}}
        <div class="flex flex-col gap-1">
            <label class="text-xs font-medium text-gray-500">Year From</label>
            <select id="yearFrom"
                    class="border border-gray-300 w-64 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                <option value="">Any</option>
                @for($y = now()->year; $y >= 1990; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
        </div>

        {{-- Year To --}}
        <div class="flex flex-col gap-1">
            <label class="text-xs font-medium text-gray-500">Year To</label>
            <select id="yearTo"
                    class="border border-gray-300 w-64 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                <option value="">Any</option>
                @for($y = now()->year; $y >= 1990; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
        </div>


        {{-- Clear Filters --}}
        <button onclick="clearFilters()"
                class="border border-gray-200 text-gray-500 px-4 py-2 rounded-lg text-sm hover:bg-gray-50 transition">
            Clear Filters
        </button>

    </div>

    {{-- Active Filters Display --}}
    <div id="activeFilters" class="hidden mt-3 flex flex-wrap gap-2"></div>

</div>

            {{-- Results --}}
            <div id="resultsContainer">

                {{-- Loading --}}
<div id="loadingState" class="hidden space-y-4">
    @for($i = 0; $i < 5; $i++)
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-3 animate-pulse">
        {{-- Top row: badge + title + button --}}
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 space-y-2">
                <div class="flex gap-2">
                    <div class="h-4 w-20 bg-gray-200 rounded-full"></div>
                    <div class="h-4 w-10 bg-gray-100 rounded-full"></div>
                </div>
                <div class="h-5 bg-gray-200 rounded w-3/4"></div>
                <div class="h-5 bg-gray-200 rounded w-1/2"></div>
            </div>
            <div class="h-8 w-20 bg-gray-200 rounded-lg shrink-0"></div>
        </div>

        {{-- Authors --}}
        <div class="h-3 bg-gray-100 rounded w-2/5"></div>

        {{-- Journal --}}
        <div class="h-3 bg-gray-100 rounded w-1/4"></div>

        {{-- Abstract lines --}}
        <div class="space-y-2 pt-1">
            <div class="h-3 bg-gray-100 rounded w-full"></div>
            <div class="h-3 bg-gray-100 rounded w-full"></div>
            <div class="h-3 bg-gray-100 rounded w-4/5"></div>
        </div>

        {{-- Footer: topics + cited by --}}
        <div class="flex items-center justify-between pt-1">
            <div class="flex gap-2">
                <div class="h-5 w-16 bg-gray-100 rounded-full"></div>
                <div class="h-5 w-20 bg-gray-100 rounded-full"></div>
                <div class="h-5 w-14 bg-gray-100 rounded-full"></div>
            </div>
            <div class="h-3 w-24 bg-gray-100 rounded"></div>
        </div>
    </div>
    @endfor
</div>

                {{-- Error --}}
                <div id="errorState" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    Failed to fetch papers. Please try again.
                </div>

                {{-- Empty --}}
                <div id="emptyState" class="hidden text-center py-16">
                    <p class="text-gray-500 font-medium">No papers found.</p>
                    <p class="text-gray-400 text-sm mt-1">Try a different search term.</p>
                </div>

                {{-- Default State --}}
<div id="defaultState" class="hidden text-center py-16">
    <p class="text-gray-500 font-medium">Search for research papers</p>
    <p class="text-gray-400 text-sm mt-1">
        Enter a topic or keyword to find published research from around the world.
    </p>
</div>




                {{-- Papers List --}}
                <div id="papersList" class="hidden space-y-4"></div>

                {{-- Pagination --}}
                <div id="pagination" class="hidden flex items-center justify-between mt-6">
                    <button id="prevBtn"
                            onclick="changePage(-1)"
                            class="bg-white border border-gray-200 text-gray-600 text-sm px-4 py-2 rounded-lg hover:bg-gray-50 transition disabled:opacity-40 disabled:cursor-not-allowed">
                        Previous
                    </button>
                    <span id="pageInfo" class="text-sm text-gray-500"></span>
                    <button id="nextBtn"
                            onclick="changePage(1)"
                            class="bg-white border border-gray-200 text-gray-600 text-sm px-4 py-2 rounded-lg hover:bg-gray-50 transition disabled:opacity-40 disabled:cursor-not-allowed">
                        Next
                    </button>
                </div>

            </div>

        </div>
    </div>

    <script>
    let currentPage  = 1;
    let totalResults = 0;
    let perPage      = 10;

    const randomTopics = [
        "Artificial Intelligence", "Climate Change", "Biology", "Medicine",
        "Physics", "Economics", "Psychology", "Computer Science", "Chemistry",
        "Engineering", "Mathematics", "Sociology", "Quantum Computing",
        "Neuroscience", "Genetics", "Astronomy", "Robotics", "Ecology"
    ];

    function getRandomTopic() {
        return randomTopics[Math.floor(Math.random() * randomTopics.length)];
    }

    // Auto-load random papers on page load
    window.addEventListener("DOMContentLoaded", () => {
        loadRandom();
    });

    function loadRandom() {
        const randomTopic = getRandomTopic();
        document.getElementById("searchInput").value = "";
        searchPapers(1, randomTopic);
    }

    // Search on Enter key
    document.getElementById("searchInput").addEventListener("keydown", (e) => {
        if (e.key === "Enter") searchPapers(1);
    });

    // Search on topic filter change
    document.getElementById("topicFilter").addEventListener("change", () => {
        searchPapers(1);
    });

    function showState(state) {
        ["loadingState", "errorState", "emptyState", "defaultState", "papersList", "pagination"]
            .forEach(id => {
                const el = document.getElementById(id);
                if (el) el.classList.add("hidden");
            });
        const target = document.getElementById(state);
        if (target) target.classList.remove("hidden");
    }

    async function searchPapers(page, fallbackQuery = null) {
        const query  = fallbackQuery || document.getElementById("searchInput").value.trim();
        const topic  = document.getElementById("topicFilter").value;
        const btn    = document.getElementById("searchBtn");

        // If truly nothing, pick a random topic
        const finalQuery = query || topic || getRandomTopic();

        currentPage     = page;
        btn.textContent = "Searching...";
        btn.disabled    = true;
        showState("loadingState");

        try {
            const params = new URLSearchParams({ query: finalQuery, topic, page });
            const res    = await fetch(`/explore/search?${params}`, {
                headers: { Accept: "application/json" },
            });

            const data = await res.json();

            if (!res.ok || data.error) {
                showState("errorState");
                return;
            }

            if (!data.papers || data.papers.length === 0) {
                showState("emptyState");
                return;
            }

            totalResults = data.meta.total;
            perPage      = data.meta.perPage;

            renderPapers(data.papers);
            renderPagination(data.meta);
            showState("papersList");
            document.getElementById("pagination").classList.remove("hidden");

        } catch (e) {
            console.error(e);
            showState("errorState");
        } finally {
            btn.textContent = "Search";
            btn.disabled    = false;
        }
    }

    function renderPapers(papers) {
        const list = document.getElementById("papersList");
        list.innerHTML = "";

        papers.forEach(paper => {
            const topicBadges = (paper.topics || [])
                .map(t => `<span class="bg-indigo-50 text-indigo-600 text-xs px-2 py-0.5 rounded-full border border-indigo-100">${t}</span>`)
                .join("");

            const card = document.createElement("div");
            card.className = "bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-3 hover:shadow-md transition";
            card.innerHTML = `
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            ${paper.is_oa ? '<span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-0.5 rounded-full">Open Access</span>' : ''}
                            ${paper.year !== 'N/A' ? `<span class="text-xs text-gray-400">${paper.year}</span>` : ''}
                        </div>
                        <h4 class="font-bold text-gray-900 text-base leading-snug">
                            ${paper.title}
                        </h4>
                    </div>
                    ${paper.url ? `
                        <a href="${paper.url}" target="_blank"
                           class="shrink-0 bg-indigo-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg hover:bg-indigo-700 transition">
                            View Paper
                        </a>
                    ` : `
                        <span class="shrink-0 bg-gray-100 text-gray-400 text-xs font-semibold px-3 py-1.5 rounded-lg cursor-not-allowed">
                            No Link
                        </span>
                    `}
                </div>

                <p class="text-xs text-gray-500 font-medium">${paper.authors}</p>

                ${paper.journal ? `<p class="text-xs text-indigo-500 italic">${paper.journal}</p>` : ''}

                <p class="text-sm text-gray-600 leading-relaxed">${paper.abstract}</p>

                <div class="flex items-center justify-between flex-wrap gap-2 pt-1">
                    <div class="flex flex-wrap gap-1">
                        ${topicBadges}
                    </div>
                    <span class="text-xs text-gray-400">
                        Cited by <span class="font-semibold text-gray-600">${paper.cited_by.toLocaleString()}</span>
                    </span>
                </div>
            `;

            list.appendChild(card);
        });
    }

    function renderPagination(meta) {
        const totalPages = Math.ceil(meta.total / meta.perPage);
        const pageInfo   = document.getElementById("pageInfo");
        const prevBtn    = document.getElementById("prevBtn");
        const nextBtn    = document.getElementById("nextBtn");

        pageInfo.textContent  = `Page ${meta.page} of ${totalPages} (${meta.total.toLocaleString()} results)`;
        prevBtn.disabled      = meta.page <= 1;
        nextBtn.disabled      = meta.page >= totalPages;
    }

    function changePage(direction) {
        searchPapers(currentPage + direction);
        window.scrollTo({ top: 0, behavior: "smooth" });
    }
</script>

</x-app-layout>