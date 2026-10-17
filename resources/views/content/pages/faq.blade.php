<section class="bg-gradient-to-r from-[#881C27] to-[#2A6867] py-14 text-center text-white sm:py-20">
    <div class="mx-auto max-w-7xl px-4">
        <span class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-[#D4AF37] text-2xl font-bold text-gray-900">?</span>
        <h1 class="mt-5 text-4xl font-bold sm:text-5xl">{{ $translation->title }}</h1>
        @if ($translation->excerpt)
            <p class="mx-auto mt-4 max-w-2xl text-lg text-white/80">{{ $translation->excerpt }}</p>
        @endif
    </div>
</section>
<section class="py-16 sm:py-20">
    <div class="mx-auto max-w-4xl px-4">
        <div class="space-y-4" x-data="{ open: 0 }">
            @foreach ($paragraphs->chunk(2) as $pair)
                @php
                    $question = $pair->values()->get(0);
                    $answer = $pair->values()->get(1);
                @endphp
                <article class="overflow-hidden rounded-xl border border-gray-200 bg-white">
                    <button type="button" class="flex w-full items-center justify-between gap-4 p-5 text-start font-semibold text-gray-900" @click="open = open === {{ $loop->index }} ? -1 : {{ $loop->index }}">
                        <span>{{ $question }}</span><span class="text-xl text-[#881C27]" x-text="open === {{ $loop->index }} ? '−' : '+'"></span>
                    </button>
                    <div x-show="open === {{ $loop->index }}" x-transition class="border-t border-gray-100 px-5 py-4 text-sm leading-7 text-gray-600">{{ $answer }}</div>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-white py-16 text-center" data-section="faq-support">
    <div class="mx-auto max-w-3xl px-4">
        <h2 class="text-3xl font-bold text-gray-900">{{ $locale === 'ps' ? 'لا هم پوښتنې لرئ؟' : ($locale === 'fa' ? 'هنوز سوال دارید؟' : 'Still Have Questions?') }}</h2>
        <p class="mx-auto mt-3 max-w-2xl text-gray-600">{{ $locale === 'ps' ? 'زموږ د پېرودونکو ملاتړ ټیم ستاسو د مرستې لپاره حاضر دی.' : ($locale === 'fa' ? 'تیم پشتیبانی مشتریان ما آماده کمک به شما است.' : 'Our customer support team is here to help you.') }}</p>
        <a href="{{ route('content.page', ['locale' => $locale, 'slug' => $locale === 'en' ? 'contact' : ($locale === 'fa' ? 'تماس' : 'اړیکه')]) }}" class="base44-gradient-cta mt-7 inline-flex rounded-full px-6 py-3 font-semibold">
            {{ $locale === 'ps' ? 'له ملاتړ سره اړیکه' : ($locale === 'fa' ? 'تماس با پشتیبانی' : 'Contact Support') }}
        </a>
    </div>
</section>