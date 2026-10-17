@php
    $heroIcon = match ($pageKey) {
        'shipping-policy' => 'truck',
        'privacy-policy' => 'shield',
        'return-policy' => 'arrow-right',
        'terms-and-conditions' => 'shield',
        default => 'shield',
    };
@endphp
<section class="bg-gradient-to-r from-[#881C27] to-[#2A6867] py-14 text-white sm:py-20">
    <div class="mx-auto max-w-7xl px-4 {{ $pageKey === 'shipping-policy' ? 'text-center' : '' }}">
        <x-icon :name="$heroIcon" class="{{ $pageKey === 'shipping-policy' ? 'mx-auto' : '' }} h-14 w-14 text-[#D4AF37]" />
        <h1 class="mt-5 text-4xl font-bold sm:text-5xl">{{ $translation->title }}</h1>
        @if ($translation->excerpt)
            <p class="mt-4 max-w-2xl text-lg text-white/80 {{ $pageKey === 'shipping-policy' ? 'mx-auto' : '' }}">{{ $translation->excerpt }}</p>
        @endif
    </div>
</section>
@if ($pageKey === 'shipping-policy')
    <section class="border-b bg-white py-10">
        <div class="mx-auto grid max-w-7xl grid-cols-2 gap-6 px-4 md:grid-cols-4">
            @foreach ([['truck','Worldwide','Tracked shipping'],['shield','Secure','Protected handling'],['chart','Live','Order tracking'],['package','Careful','Packed for transit']] as [$icon,$title,$desc])
                <div class="text-center"><span class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-[#8B1538]/10 text-[#8B1538]"><x-icon :name="$icon" class="h-6 w-6" /></span><h3 class="mt-3 font-semibold text-gray-900">{{ $title }}</h3><p class="text-sm text-gray-500">{{ $desc }}</p></div>
            @endforeach
        </div>
    </section>
@endif
<section class="py-16 sm:py-20">
    <div class="mx-auto max-w-4xl px-4">
        <div class="space-y-6">
            @foreach ($paragraphs as $paragraph)
                <article class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8"><div class="flex items-start gap-4"><span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-[#EF5350]/10 text-[#EF5350]">{{ $loop->iteration }}</span><p class="leading-8 text-gray-600">{{ $paragraph }}</p></div></article>
            @endforeach
        </div>
    </div>
</section>