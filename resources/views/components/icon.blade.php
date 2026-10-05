@props(['name'])
<svg {{ $attributes->merge(['class' => 'ui-icon']) }} viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
    @switch($name)
        @case('ruler')
            <path d="M4 20 20 4l-4-4L0 16l4 4Z" transform="translate(2 2) scale(.83)" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="m8 14 2 2m1-5 2 2m1-5 2 2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            @break
        @case('globe')
            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/>
            <path d="M3.5 12h17M12 3c2.5 2.5 3.7 5.5 3.7 9S14.5 18.5 12 21M12 3C9.5 5.5 8.3 8.5 8.3 12S9.5 18.5 12 21" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            @break
        @case('quality')
            <path d="m12 2.8 2.25 2.1 3.05-.2.75 2.95 2.45 1.8-1.55 2.65.95 2.9-2.8 1.2-.9 2.95-3.05-.55L12 21.2l-2.15-2.6-3.05.55-.9-2.95L3.1 15l.95-2.9L2.5 9.45l2.45-1.8.75-2.95 3.05.2L12 2.8Z" stroke="currentColor" stroke-width="1.55" stroke-linejoin="round"/>
            <path d="m8.7 12.1 2.05 2.05 4.6-4.65" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            @break
        @case('scissors')
            <circle cx="6.5" cy="7.5" r="2.6" stroke="currentColor" stroke-width="1.6"/>
            <circle cx="6.5" cy="16.5" r="2.6" stroke="currentColor" stroke-width="1.6"/>
            <path d="m8.7 9 11.1 7.5M8.7 15 19.8 7.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            @break
        @case('thread')
            <path d="M7 5.5h10l-1.2 13h-7.6L7 5.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
            <path d="M8 9h8m-7.4 4h6.8M9 3h6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            @break
        @case('home')
            <path d="m3 11 9-8 9 8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M5.5 9.5V21h13V9.5M9.5 21v-6h5v6" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            @break
        @case('bag')
            <path d="M5 8h14l-1 13H6L5 8Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            <path d="M9 9V6a3 3 0 0 1 6 0v3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            @break
        @case('heart')
            <path d="M20.8 5.9c-1.7-2-4.9-2.2-6.8-.4L12 7.4 10 5.5C8.1 3.7 4.9 3.9 3.2 5.9c-1.8 2.1-1.5 5.2.4 7.1L12 21l8.4-8c1.9-1.9 2.2-5 .4-7.1Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            @break
        @case('user')
            <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.7"/>
            <path d="M4.5 21a7.5 7.5 0 0 1 15 0" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            @break
        @case('search')
            <circle cx="10.5" cy="10.5" r="6.5" stroke="currentColor" stroke-width="1.7"/>
            <path d="m15.5 15.5 5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            @break
        @case('menu')
            <path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            @break
        @case('close')
            <path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            @break
        @case('arrow-right')
            <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            @break
        @case('eye')
            <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            <circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.7"/>
            @break
        @default
            <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.7"/>
    @endswitch
</svg>
