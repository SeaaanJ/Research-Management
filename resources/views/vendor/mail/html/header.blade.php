@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url ?? config('app.url') }}" style="display: inline-block; text-decoration: none;">
            <span style="font-size: 28px; font-weight: 800; color: rgb(79, 70, 229);"><img src="{{ asset('images/logo.png') }}" alt="Reach Logo" class="h-14 w-auto rotate-animate-hover"></span>
        </a>
    </td>
</tr>