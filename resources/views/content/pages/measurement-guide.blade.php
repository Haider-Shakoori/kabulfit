<section class="bg-gradient-to-r from-[#881C27] to-[#2A6867] py-14 text-center text-white sm:py-20">
    <div class="mx-auto max-w-7xl px-4">
        <span class="inline-flex rounded-full px-4 py-2 text-sm font-semibold text-[#D4AF37]">{{ __('site.perfect_fit_guarantee') }}</span>
        <x-icon name="ruler" class="mx-auto mt-4 h-14 w-14 text-[#D4AF37]" />
        <h1 class="mt-5 text-4xl font-bold sm:text-5xl">{{ $translation->title }}</h1>
        @if ($translation->excerpt)
            <p class="mx-auto mt-4 max-w-2xl text-lg text-white/80">{{ $translation->excerpt }}</p>
        @endif
        <a href="#guide-section" class="base44-gold-cta mt-7 inline-flex items-center gap-2 rounded-full px-6 py-3 font-semibold shadow-lg">
            <span aria-hidden="true">▶</span>
            {{ __('site.watch_tutorial') }}
        </a>
    </div>
</section>
<section id="guide-section" class="bg-[#F7F5F0] py-16 sm:py-20">
    <div class="mx-auto max-w-7xl px-4">
        <div class="mb-10 text-center">
            <h2 class="text-3xl font-bold text-gray-900">{{ $locale === 'ps' ? 'ویډیويي لارښوونې' : ($locale === 'fa' ? 'راهنماهای ویدیویی' : 'Video Guides') }}</h2>
            <p class="mx-auto mt-3 max-w-2xl text-gray-500">{{ $locale === 'ps' ? 'د هرې اندازې لپاره د اخیستلو لارښوونه وګورئ.' : ($locale === 'fa' ? 'راهنمای اندازه‌گیری هر بخش را مشاهده کنید.' : 'Watch the correct technique for each body measurement.') }}</p>
        </div>

        @if(($measurementGuideVideos ?? collect())->isNotEmpty())
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach($measurementGuideVideos as $guide)
                    @php
                        $videoUrl = $guide->localizedVideoUrl($locale);
                        $isYoutube = str_contains($videoUrl, 'youtube.com') || str_contains($videoUrl, 'youtu.be');
                        $embedUrl = $videoUrl;

                        if ($isYoutube) {
                            if (str_contains($embedUrl, 'watch?v=')) {
                                $embedUrl = str_replace('watch?v=', 'embed/', $embedUrl);
                            } elseif (str_contains($embedUrl, 'youtu.be/')) {
                                $embedUrl = str_replace('youtu.be/', 'youtube.com/embed/', $embedUrl);
                            }
                        }
                    @endphp
                    <article class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                        <div class="aspect-video bg-gray-950">
                            @if($isYoutube)
                                <iframe src="{{ $embedUrl }}" title="{{ $guide->title ?: str($guide->measurement_field)->replace('_', ' ')->title() }}" class="h-full w-full" loading="lazy" allowfullscreen></iframe>
                            @else
                                <video src="{{ $videoUrl }}" controls preload="metadata" class="h-full w-full bg-black"></video>
                            @endif
                        </div>
                        <div class="p-5">
                            <div class="flex flex-wrap gap-2">
                                <span class="rounded-full bg-[#881C27]/10 px-2.5 py-1 text-xs font-semibold text-[#881C27]">{{ str($guide->category)->title() }}</span>
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs text-gray-600">{{ str($guide->measurement_field)->replace('_', ' ')->title() }}</span>
                            </div>
                            <h3 class="mt-3 text-lg font-semibold text-gray-900">{{ $guide->title ?: str($guide->measurement_field)->replace('_', ' ')->title() }}</h3>
                            @if($guide->description)
                                <p class="mt-2 text-sm leading-6 text-gray-600">{{ $guide->description }}</p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="rounded-2xl border border-dashed border-gray-300 bg-[#FDFBF7] py-12 text-center text-gray-500">
                {{ $locale === 'ps' ? 'ویډیويي لارښوونې به ژر اضافه شي.' : ($locale === 'fa' ? 'راهنماهای ویدیویی به‌زودی اضافه می‌شوند.' : 'Video guides will be added soon.') }}
            </div>
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
                <a href="{{ route('measurements.create', ['locale' => $locale]) }}" class="base44-gradient-cta inline-flex items-center gap-2 rounded-xl px-6 py-3 font-semibold"><x-icon name="ruler" class="h-4 w-4" />{{ __('measurements.new_profile') }}</a>
            @endauth
        </div>
    </div>
</section>