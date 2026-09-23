@props(['name'])
<svg {{ $attributes->merge(['class' => 'ui-icon']) }} viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
    @switch($name)
        @case('ruler')
            <path d="M21.3 15.3a2.4 2.4 0 0 1 0 3.4l-2.6 2.6a2.4 2.4 0 0 1-3.4 0L2.7 8.7a2.41 2.41 0 0 1 0-3.4l2.6-2.6a2.41 2.41 0 0 1 3.4 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="m14.5 12.5 2-2M11.5 9.5l2-2M8.5 6.5l2-2M17.5 15.5l2-2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            @break
        @case('truck')
            <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2M15 18H9M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="17" cy="18" r="2" stroke="currentColor" stroke-width="1.7"/>
            <circle cx="7" cy="18" r="2" stroke="currentColor" stroke-width="1.7"/>
            @break
        @case('shield')
            <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
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
            <path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            @break
        @case('shopping-bag')
            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M3 6h18M16 10a4 4 0 0 1-8 0" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            @break
        @case('menu')
            <path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            @break
        @case('search')
            <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
            <path d="m21 21-4.3-4.3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            @break
        @case('heart')
            <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            @break
        @case('eye')
            <path d="M2.1 12.3a1 1 0 0 1 0-.6 10.7 10.7 0 0 1 19.8 0 1 1 0 0 1 0 .6 10.7 10.7 0 0 1-19.8 0Z" stroke="currentColor" stroke-width="1.7"/>
            <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.7"/>
            @break
        @case('sparkles')
            <path d="M9.94 15.5A2 2 0 0 0 8.5 14.06l-6.14-1.58a.5.5 0 0 1 0-.96L8.5 9.94A2 2 0 0 0 9.94 8.5l1.58-6.14a.5.5 0 0 1 .96 0l1.58 6.14a2 2 0 0 0 1.44 1.44l6.14 1.58a.5.5 0 0 1 0 .96l-6.14 1.58a2 2 0 0 0-1.44 1.44l-1.58 6.14a.5.5 0 0 1-.96 0L9.94 15.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
            <path d="M20 3v4M22 5h-4M4 17v2M5 18H3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            @break
        @case('cart')
            <circle cx="8" cy="21" r="1" stroke="currentColor" stroke-width="2"/>
            <circle cx="19" cy="21" r="1" stroke="currentColor" stroke-width="2"/>
            <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            @break
        @case('user')
            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
            @break
        @case('mail')
            <rect x="2" y="4" width="20" height="16" rx="2" stroke="currentColor" stroke-width="2"/>
            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            @break
        @case('map-pin')
            <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2"/>
            @break
        @case('whatsapp')
            <path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6z" fill="currentColor" transform="scale(.0536)"/>
            @break
        @case('tiktok')
            <path d="M448 209.91a210.06 210.06 0 0 1-122.77-39.25V349.38A162.55 162.55 0 1 1 185 188.31V278.2a74.62 74.62 0 1 0 52.23 71.18V0l88 0a121.18 121.18 0 0 0 1.86 22.17A122.18 122.18 0 0 0 381 102.39a121.43 121.43 0 0 0 67 20.14Z" fill="currentColor" transform="scale(.0536)"/>
            @break
        @case('facebook')
            <path d="M14 8h3V4.5c-.6-.1-1.9-.3-3.4-.3-3.3 0-5.6 2-5.6 5.8v3H4.5v4H8v7h4.3v-7h3.5l.6-4h-4.1v-2.6c0-1.2.4-2.4 1.7-2.4Z" fill="currentColor"/>
            @break
        @case('instagram')
            <rect x="3.5" y="3.5" width="17" height="17" rx="5" stroke="currentColor" stroke-width="1.7"/>
            <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.7"/>
            <circle cx="17.5" cy="6.8" r="1" fill="currentColor"/>
            @break
        @case('arrow-right')
            <path d="M5 12h14" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            <path d="m12 5 7 7-7 7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            @break
        @case('play')
            <path d="M6 3l14 9-14 9V3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            @break
        @case('star')
            <path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            @break
        @case('youtube')
            <path d="M21 8.1a3 3 0 0 0-2.1-2.2C17 5.4 12 5.4 12 5.4s-5 0-6.9.5A3 3 0 0 0 3 8.1 31 31 0 0 0 2.5 12 31 31 0 0 0 3 15.9a3 3 0 0 0 2.1 2.2c1.9.5 6.9.5 6.9.5s5 0 6.9-.5a3 3 0 0 0 2.1-2.2 31 31 0 0 0 .5-3.9 31 31 0 0 0-.5-3.9Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
            <path d="m10 9 5 3-5 3V9Z" fill="currentColor"/>
            @break
        @default
            <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.7"/>
    @endswitch
</svg>
