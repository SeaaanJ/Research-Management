<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reach — Welcome</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-800">

    {{-- Navbar --}}
    <nav class=" sticky top-0 flex backdrop-blur-lg bg-white/80 z-50 items-center justify-between px-8 py-5 border-b fade-up delay-1">

    {{-- Logo Image --}}
    <a href="/" class="flex items-center gap-2 ">
    <img src="{{ asset('images/logo.png') }}" alt="Reach Logo" class="h-14 w-auto rotate-animate-hover">
    <span class="text-2xl font-bold text-dark-600">Reach</span>
</a>

    <div class="flex gap-4">
        <a href="{{ route('login') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 btn-landing">Login</a>
        <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 btn-landing">Sign Up</a>
    </div>
</nav>

    {{-- Hero Section --}}
    <div class="flex flex-col fade-up items-center justify-center text-center px-6 py-32">
         <img src="{{ asset('images/logo.png') }}" alt="Reach Logo" class="logo-landing mb-8">
        <main class="flex-grow">
        <h1 class="text-5xl font-extrabold text-gray-900 mb-6 leading-tight">
            Build something <span class="text-indigo-600">great.</span>
        </h1>
        <p class="text-xl text-gray-500 mb-10 max-w-xl">
           Redefining how teams discover, organize, and synthesize knowledge. </p>
        <a href="{{ route('register') }}"
           class="bg-indigo-600 text-white text-lg px-8 py-4 rounded-xl hover:bg-indigo-700 transition duration-300 btn-landing">
            Start for Free →
        </a>
        </main>
</div>

    {{-- Features Section --}}
    <section class="bg-gray-50 py-20 px-8 fade-up">
        <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
       @php
    $features = [
        ['<img src="images/brain.svg" alt="Sync">', 'Synthesis Sync', 'Automatically aggregates team notes into a unified, searchable knowledge graph.'],
        ['<img src="images/ppl.svg" alt="Roles">', 'Role-Based Workspaces', 'Assign Lead Researchers, Analysts, and Reviewers with custom permissions.'],
        ['<img src="images/telescope.svg" alt="Feed">', 'Smart Discovery Feed', 'AI suggests new papers based on the collective interest of your entire group.']
    ];
@endphp

@foreach($features as [$icon, $title, $desc])
    <div class="p-6 rounded-xl bg-dark-50 border border-gray-800...">
        {{-- 2. Use {!! !!} to render the raw HTML/SVG tag --}}
        <div class="text-3xl mb-5">{!! $icon !!}</div>
        
        <h3 class="text-xl font-bold text-black mb-2">{{ $title }}</h3>
        <p class="text-gray-400">{{ $desc }}</p>
    </div>
@endforeach
        </div>
    </section>

    {{-- Stats Section --}}
<section class="bg-white py-24 px-8 fade-up delay-3">
    <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-16 items-center">

        {{-- Left Side --}}
        <div>
            <h2 class="text-5xl font-extrabold text-gray-900 leading-tight mb-6">
                Why it’s great for  <br>
                <span class="text-indigo-600">groups</span>
            </h2>
            <p class="text-gray-500 text-lg leading-relaxed">
                It serves as a central hub for meeting notes, project timelines, and literature databases,
                 making it easy for teams to stay organized and aligned. By fostering a culture of shared knowledge 
                 and collaboration, it empowers teams to work more efficiently and make better-informed decisions.
            </p>
        </div>

        {{-- Right Side — Stats --}}
        <div class="flex flex-col gap-10">
            <div class="fade-up delay-4">
                <p class="text-6xl font-extrabold text-indigo-600">{{ \App\Models\User::count() }}</p>
                <p class="text-gray-500 mt-1">students served worldwide</p>
            </div>
            <div class="fade-up delay-5">
                <p class="text-6xl font-extrabold text-indigo-600">1</p>
                <p class="text-gray-500 mt-1">Research papers indexed</p>
            </div>
            <div class="fade-up delay-6">
                <p class="text-6xl font-extrabold text-indigo-600">1x</p>
                <p class="text-gray-500 mt-1">Research impact</p>
            </div>
        </div>

    </div>
</section>

{{-- Bottom Label Section --}}
<section class="bg-gray-50 py-20 px-8 text-center fade-up delay-4">
    <h2 class="text-4xl font-extrabold text-gray-900 mb-4">
        Data-driven lead capture solutions
    </h2>
    <p class="text-gray-500 text-lg max-w-2xl mx-auto">
        Everything you need to launch landing pages, optimize your strategy,
        and make the most of your leads.
    </p>
</section>

    {{-- Footer --}}
    <footer class="text-center py-8 text-gray-400 text-sm bg-dark-50 fade-up">
        © {{ date('Y') }} Reach. All rights reserved.
    </footer>

</body>
</html>