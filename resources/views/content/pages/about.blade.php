<section class="relative overflow-hidden bg-gradient-to-r from-[#881C27] to-[#2A6867] py-12 text-white sm:py-16">
    <div class="absolute inset-0 opacity-10" style="background-image:radial-gradient(circle at 1px 1px,white 1px,transparent 0);background-size:30px 30px"></div>
    <div class="relative mx-auto max-w-7xl px-4">
        <a href="#our-story" class="base44-gold-cta inline-flex rounded-full px-4 py-2 text-xs font-semibold">{{ $locale === 'ps' ? 'زموږ کیسه' : ($locale === 'fa' ? 'داستان ما' : 'Our Story') }}</a>
        <h1 class="mt-5 max-w-3xl text-4xl font-bold leading-tight sm:text-5xl lg:text-6xl">{{ $translation->title }}</h1>
        @if ($translation->excerpt)
            <p class="mt-5 max-w-3xl text-lg leading-8 text-white/80 sm:text-xl">{{ $translation->excerpt }}</p>
        @endif
    </div>
</section>

<section id="our-story" class="py-12 sm:py-16 md:py-20">
    <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 md:grid-cols-2">
        <img src="/images/kabulfit-base44/source/f065351e6_bn.jpg" alt="Afghan craftsmanship" class="aspect-square w-full rounded-3xl object-cover shadow-2xl">
        <div>
            <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl">{{ $locale === 'ps' ? 'د هنر میراث' : ($locale === 'fa' ? 'میراث هنر' : 'A Legacy of Artistry') }}</h2>
            @foreach ($paragraphs as $paragraph)
                <p class="mt-5 leading-8 text-gray-600">{{ $paragraph }}</p>
            @endforeach
            <div class="mt-6 flex flex-wrap gap-3">
                @foreach (['Handcrafted', 'Authentic', 'Sustainable', 'Quality'] as $tag)
                    <span class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-3 py-1.5 text-sm text-gray-700">✓ {{ $tag }}</span>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="bg-white py-16">
    <div class="mx-auto max-w-7xl px-4">
        <h2 class="mb-10 text-center text-3xl font-bold text-gray-900">{{ $locale === 'ps' ? 'زموږ ارزښتونه' : ($locale === 'fa' ? 'ارزش‌های ما' : 'Our Values') }}</h2>
        <div class="grid gap-6 md:grid-cols-4">
            @foreach ([
                ['ae5604c05_MasterArtisian.jpg', 'Master Artisans'],
                ['f93ac8c2b_FineEmbroidery.jpg', 'Fine Embroidery'],
                ['c83945fa0_QualityFabrics.jpg', 'Quality Fabrics'],
                ['f065351e6_bn.jpg', 'Afghan Heritage'],
            ] as [$image, $title])
                <article class="rounded-xl border border-gray-200 bg-white p-5 text-center shadow-sm transition hover:shadow-lg">
                    <img src="{{ asset('images/kabulfit-base44/source/'.$image) }}" alt="{{ $title }}" class="mx-auto h-24 w-24 rounded-full object-cover">
                    <h3 class="mt-4 font-semibold text-gray-900">{{ $title }}</h3>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-gradient-to-r from-[#881C27] to-[#2A6867] py-16 text-white">
    <div class="mx-auto grid max-w-7xl grid-cols-2 gap-8 px-4 text-center md:grid-cols-4">
        @foreach ([['5000+', 'Happy Customers'], ['50+', 'Countries Served'], ['200+', 'Artisans Supported'], ['100%', 'Authentic Products']] as [$value, $label])
            <div><p class="text-4xl font-bold text-[#D4AF37] md:text-5xl">{{ $value }}</p><p class="mt-2 text-sm text-white/70">{{ $label }}</p></div>
        @endforeach
    </div>
</section>

<section class="bg-gradient-to-r from-[#881C27] to-[#2A6867] py-16 text-center text-white" data-section="about-closing-cta">
    <div class="mx-auto max-w-4xl px-4">
        <h2 class="text-3xl font-bold sm:text-4xl">{{ $locale === 'ps' ? 'د افغان ښکلا تجربه وکړئ' : ($locale === 'fa' ? 'ظرافت افغان را تجربه کنید' : 'Experience Afghan Elegance') }}</h2>
        <p class="mx-auto mt-4 max-w-2xl text-white/80">{{ $locale === 'ps' ? 'زموږ اصلي افغان کالي او دودیز خیاطي وپلټئ.' : ($locale === 'fa' ? 'لباس‌های اصیل افغان و خیاطی سفارشی ما را کشف کنید.' : 'Discover authentic Afghan clothing and custom tailoring crafted with care.') }}</p>
        <a href="{{ route('shop', ['locale' => $locale]) }}" class="base44-gradient-cta mt-7 inline-flex rounded-full border border-white/20 px-6 py-3 font-semibold shadow-lg">
            {{ $locale === 'ps' ? 'ټولګه وپلټئ' : ($locale === 'fa' ? 'مشاهده مجموعه' : 'Shop Collection') }}
        </a>
    </div>
</section>