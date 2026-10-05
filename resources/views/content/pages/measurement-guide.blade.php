<section class="bg-gradient-to-r from-[#881C27] to-[#2A6867] py-14 text-center text-white sm:py-20">
    <div class="mx-auto max-w-7xl px-4">
        <x-icon name="ruler" class="mx-auto h-14 w-14 text-[#D4AF37]" />
        <h1 class="mt-5 text-4xl font-bold sm:text-5xl">{{ $translation->title }}</h1>
        @if ($translation->excerpt)
            <p class="mx-auto mt-4 max-w-2xl text-lg text-white/80">{{ $translation->excerpt }}</p>
        @endif
    </div>
</section>
<section class="py-16 sm:py-20">
    <div class="mx-auto grid max-w-6xl items-start gap-8 px-4 lg:grid-cols-2">
        <div class="overflow-hidden rounded-3xl bg-white shadow-xl"><img src="{{ asset('images/kabulfit-live/measurement-guide.png') }}" alt="{{ $translation->title }}" class="w-full object-cover"></div>
        <div class="space-y-5">
            @foreach ($paragraphs as $paragraph)
                <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm"><div class="flex gap-4"><span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-[#8B1538]/10 font-bold text-[#8B1538]">{{ $loop->iteration }}</span><p class="leading-7 text-gray-600">{{ $paragraph }}</p></div></article>
            @endforeach
            @auth
                <a href="{{ route('measurements.create', ['locale' => $locale]) }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#881C27] to-[#2A6867] px-6 py-3 font-semibold text-white"><x-icon name="ruler" class="h-4 w-4" />{{ __('measurements.new_profile') }}</a>
            @endauth
        </div>
    </div>
</section>