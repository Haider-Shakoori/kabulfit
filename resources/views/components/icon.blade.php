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
        @case('truck')
            <path d="M3 6h11v11H3V6Zm11 4h4l3 3v4h-7v-7Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            <circle cx="7" cy="18" r="2" stroke="currentColor" stroke-width="1.7"/>
            <circle cx="18" cy="18" r="2" stroke="currentColor" stroke-width="1.7"/>
            @break
        @case('shield')
            <path d="M12 3 19 6v5c0 4.7-2.9 8-7 10-4.1-2-7-5.3-7-10V6l7-3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            <path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            @break
        @case('star')
            <path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1-5.4-2.9-5.4 2.9 1-6.1-4.4-4.3 6.1-.9L12 3Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
            @break
        @case('book')
            <path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H11v16H6.5A2.5 2.5 0 0 0 4 21.5v-16Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
            <path d="M20 5.5A2.5 2.5 0 0 0 17.5 3H13v16h4.5A2.5 2.5 0 0 1 20 21.5v-16Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
            @break
        @case('shirt')
            <path d="m8 4-4 2.5 2.2 4L8 9.4V21h8V9.4l1.8 1.1 2.2-4L16 4c-.6 1.4-2 2.2-4 2.2S8.6 5.4 8 4Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
            @break
        @case('info')
            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/>
            <path d="M12 10.5V17M12 7h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            @break
        @case('plus')
            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            @break
        @case('minus')
            <path d="M5 12h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            @break
        @case('share')
            <circle cx="18" cy="5" r="2.5" stroke="currentColor" stroke-width="1.6"/>
            <circle cx="6" cy="12" r="2.5" stroke="currentColor" stroke-width="1.6"/>
            <circle cx="18" cy="19" r="2.5" stroke="currentColor" stroke-width="1.6"/>
            <path d="m8.3 10.9 7.4-4.6M8.3 13.1l7.4 4.6" stroke="currentColor" stroke-width="1.6"/>
            @break
        @case('trash')
            <path d="M4 7h16M9 7V4h6v3m-8 0 1 14h8l1-14M10 11v6m4-6v6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            @break
        @case('dashboard')
            <path d="M4 4h6v6H4V4Zm10 0h6v6h-6V4ZM4 14h6v6H4v-6Zm10 0h6v6h-6v-6Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            @break
        @case('package')
            <path d="m4 7 8-4 8 4-8 4-8-4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            <path d="M4 7v10l8 4 8-4V7M12 11v10" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            @break
        @case('users')
            <circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.7"/>
            <path d="M3.5 20a5.5 5.5 0 0 1 11 0" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            <path d="M15 6.5a3 3 0 0 1 0 5.5M16 15a5 5 0 0 1 4.5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            @break
        @case('tag')
            <path d="M4 4h6l10 10-6 6L4 10V4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            <circle cx="8" cy="8" r="1" fill="currentColor"/>
            @break
        @case('settings')
            <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.7"/>
            <path d="M19 12a7 7 0 0 0-.1-1.1l2-1.6-2-3.4-2.5 1a7 7 0 0 0-1.9-1.1L14 3h-4l-.5 2.8A7 7 0 0 0 7.6 7l-2.5-1-2 3.4 2 1.6A7 7 0 0 0 5 12c0 .4 0 .7.1 1.1l-2 1.6 2 3.4 2.5-1a7 7 0 0 0 1.9 1.1L10 21h4l.5-2.8a7 7 0 0 0 1.9-1.1l2.5 1 2-3.4-2-1.6c.1-.4.1-.7.1-1.1Z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/>
            @break
        @case('chart')
            <path d="M5 20V10m7 10V4m7 16v-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            @break
        @case('logout')
            <path d="M10 4H5v16h5M14 8l4 4-4 4m4-4H9" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            @break
        @default
            <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.7"/>
    @endswitch
</svg>
