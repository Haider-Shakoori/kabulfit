@php
    $contactSettings = app(\App\Services\Settings\SiteSettings::class);
    $whatsappUrl = $contactSettings->get('social.whatsapp', 'https://wa.me/93794120017');
    $facebookUrl = $contactSettings->get('social.facebook', 'https://www.facebook.com/KabulFitTailoring/');
    $instagramUrl = $contactSettings->get('social.instagram', '');
    $tiktokUrl = $contactSettings->get('social.tiktok', '');
    $youtubeUrl = $contactSettings->get('social.youtube', 'http://www.youtube.com/@Kabulfit');
    $faqSlug = match ($locale) {
        'fa' => 'پرسش-های-متداول',
        'ps' => 'ډېرې-پوښتل-شوې-پوښتنې',
        default => 'faq',
    };
@endphp

<section class="bg-gradient-to-r from-[#881C27] to-[#2A6867] py-14 text-center text-white sm:py-20">
    <div class="mx-auto max-w-7xl px-4">
        <h1 class="text-4xl font-bold sm:text-5xl">{{ __('site.get_in_touch') }}</h1>
        <p class="mx-auto mt-4 max-w-2xl text-lg text-white/80">{{ __('site.get_in_touch_subtitle') }}</p>
    </div>
</section>

<section class="py-16 sm:py-20">
    <div class="mx-auto max-w-7xl px-4">
        @if(session('status'))
            <div class="mx-auto mb-8 max-w-3xl rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-700">{{ session('status') }}</div>
        @endif
        <x-form-errors />

        <div class="grid gap-10 lg:grid-cols-3 lg:gap-12">
            <div class="space-y-5">
                <h2 class="text-2xl font-bold text-gray-900">{{ __('site.get_in_touch') }}</h2>

                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="flex items-start gap-4 rounded-xl p-4 transition hover:bg-white hover:shadow-sm">
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-[#25D366]/10 text-[#128C7E]">◉</span>
                    <span><span class="block text-sm text-gray-500">WhatsApp</span><strong class="mt-1 block text-gray-900">{{ $locale === 'ps' ? 'په واټساپ کې راسره اړیکه ونیسئ' : ($locale === 'fa' ? 'با ما در واتساپ تماس بگیرید' : 'Chat with us on WhatsApp') }}</strong></span>
                </a>

                <a href="mailto:{{ $contactEmail }}" class="flex items-start gap-4 rounded-xl p-4 transition hover:bg-white hover:shadow-sm">
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-[#881C27]/10 text-[#881C27]">✉</span>
                    <span><span class="block text-sm text-gray-500">{{ __('site.email_us') }}</span><strong class="mt-1 block text-gray-900">{{ $contactEmail }}</strong></span>
                </a>

                <a href="https://www.google.com/maps/search/Kabul,+Afghanistan" target="_blank" rel="noopener noreferrer" class="flex items-start gap-4 rounded-xl p-4 transition hover:bg-white hover:shadow-sm">
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-[#2A6867]/10 text-[#2A6867]">⌖</span>
                    <span><span class="block text-sm text-gray-500">{{ __('site.visit_us') }}</span><strong class="mt-1 block text-gray-900">{{ $locale === 'ps' ? 'کابل، افغانستان' : ($locale === 'fa' ? 'کابل، افغانستان' : 'Kabul, Afghanistan') }}</strong></span>
                </a>

                <div class="flex items-start gap-4 rounded-xl p-4">
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-[#D4AF37]/15 text-[#8B6C14]">◷</span>
                    <span><span class="block text-sm text-gray-500">{{ __('site.business_hours') }}</span><strong class="mt-1 block text-gray-900">{{ __('site.business_hours_value') }}</strong></span>
                </div>

                @foreach($paragraphs as $paragraph)
                    <p class="leading-7 text-gray-600">{{ $paragraph }}</p>
                @endforeach

                <div class="border-t border-gray-200 pt-6">
                    <p class="font-semibold text-gray-900">{{ __('site.follow_us_social') }}</p>
                    <div class="mt-4 flex flex-wrap gap-3">
                        @if($facebookUrl)<a href="{{ $facebookUrl }}" target="_blank" rel="noopener noreferrer" class="grid h-10 w-10 place-items-center rounded-full bg-gray-100 text-sm font-bold text-gray-600 transition hover:bg-[#881C27] hover:text-white">f</a>@endif
                        @if($instagramUrl)<a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer" class="grid h-10 w-10 place-items-center rounded-full bg-gray-100 text-xs font-bold text-gray-600 transition hover:bg-[#881C27] hover:text-white">◎</a>@endif
                        @if($tiktokUrl)<a href="{{ $tiktokUrl }}" target="_blank" rel="noopener noreferrer" class="grid h-10 w-10 place-items-center rounded-full bg-gray-100 text-xs font-bold text-gray-600 transition hover:bg-[#881C27] hover:text-white">♪</a>@endif
                        @if($youtubeUrl)<a href="{{ $youtubeUrl }}" target="_blank" rel="noopener noreferrer" class="grid h-10 w-10 place-items-center rounded-full bg-gray-100 text-xs font-bold text-gray-600 transition hover:bg-[#881C27] hover:text-white">▶</a>@endif
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
                    <h2 class="text-2xl font-bold text-gray-900">{{ __('site.send_message') }}</h2>
                    <form method="POST" action="{{ route('contact.store', ['locale' => $locale]) }}" class="mt-6 space-y-6">
                        @csrf
                        <input type="text" name="website" value="" tabindex="-1" autocomplete="off" class="absolute -start-[9999px] h-px w-px opacity-0" aria-hidden="true">

                        <div class="grid gap-6 sm:grid-cols-2">
                            <label class="grid gap-1.5">
                                <span class="text-sm font-medium text-gray-700">{{ __('site.your_name') }} *</span>
                                <input name="name" value="{{ old('name', auth()->user()?->name) }}" required maxlength="150" autocomplete="name" class="rounded-md border border-gray-200 px-3 py-2.5 outline-none focus:border-[#881C27] focus:ring-2 focus:ring-[#881C27]/10">
                            </label>
                            <label class="grid gap-1.5">
                                <span class="text-sm font-medium text-gray-700">{{ __('site.your_email') }} *</span>
                                <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required maxlength="254" autocomplete="email" class="rounded-md border border-gray-200 px-3 py-2.5 outline-none focus:border-[#881C27] focus:ring-2 focus:ring-[#881C27]/10">
                            </label>
                        </div>

                        <label class="grid gap-1.5">
                            <span class="text-sm font-medium text-gray-700">{{ __('site.subject') }}</span>
                            <input name="subject" value="{{ old('subject') }}" maxlength="255" class="rounded-md border border-gray-200 px-3 py-2.5 outline-none focus:border-[#881C27] focus:ring-2 focus:ring-[#881C27]/10">
                        </label>

                        <label class="grid gap-1.5">
                            <span class="text-sm font-medium text-gray-700">{{ __('site.your_message') }} *</span>
                            <textarea name="message" rows="7" required minlength="5" maxlength="5000" class="rounded-md border border-gray-200 px-3 py-2.5 outline-none focus:border-[#881C27] focus:ring-2 focus:ring-[#881C27]/10">{{ old('message') }}</textarea>
                        </label>

                        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#881C27] to-[#2A6867] px-6 py-3 font-semibold text-white transition hover:opacity-90">
                            <span>➤</span>
                            {{ __('site.send_message') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-gray-50 py-16 sm:py-20">
    <div class="mx-auto max-w-7xl px-4 text-center">
        <span class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-[#881C27]/10 text-2xl text-[#881C27]">?</span>
        <h2 class="mt-6 text-3xl font-bold text-gray-900">{{ __('site.frequent_questions') }}</h2>
        <p class="mx-auto mt-3 max-w-2xl text-gray-600">{{ __('site.check_faq') }}</p>
        <a href="{{ route('content.page', ['locale' => $locale, 'slug' => $faqSlug]) }}" class="mt-8 inline-flex rounded-full bg-gradient-to-r from-[#881C27] to-[#2A6867] px-6 py-3 font-semibold text-white">{{ __('site.view_faq') }}</a>
    </div>
</section>
