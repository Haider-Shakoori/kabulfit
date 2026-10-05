<section class="bg-gradient-to-r from-[#881C27] to-[#2A6867] py-14 text-center text-white sm:py-20">
    <div class="mx-auto max-w-7xl px-4">
        <span class="inline-flex rounded-full bg-[#D4AF37] px-4 py-2 text-xs font-semibold text-black">FAQ</span>
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
                <article class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <button type="button" class="flex w-full items-center justify-between gap-4 p-5 text-start font-semibold text-gray-900" @click="open = open === {{ $loop->index }} ? -1 : {{ $loop->index }}">
                        <span>{{ $question }}</span><span class="text-xl text-[#881C27]" x-text="open === {{ $loop->index }} ? '−' : '+'"></span>
                    </button>
                    <div x-show="open === {{ $loop->index }}" x-transition class="border-t border-gray-100 px-5 py-4 text-sm leading-7 text-gray-600">{{ $answer }}</div>
                </article>
            @endforeach
        </div>
    </div>
</section>