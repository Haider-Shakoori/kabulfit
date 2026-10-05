@extends('layouts.app')

@section('content')
@php
    $pageKey = $translation->page->page_key;
    $paragraphs = collect(preg_split('/\R{2,}/u', trim($translation->body ?? '')))->filter()->values();
    $locale = app()->getLocale();
    $contactEmail = app(\App\Services\Settings\SiteSettings::class)->get('site.contact_email', 'info@kabulfit.com');
@endphp

@if($pageKey === 'about')
    <section class="relative overflow-hidden bg-gradient-to-r from-[#881C27] to-[#2A6867] py-10 text-white sm:py-14 md:py-16">
        <div class="absolute inset-0 opacity-10" style="background-image:radial-gradient(circle at 1px 1px,white 1px,transparent 0);background-size:30px 30px"></div>
        <div class="relative mx-auto max-w-7xl px-4">
            <div class="max-w-3xl">
                <span class="inline-flex rounded-full bg-[#D4AF37] px-4 py-2 text-xs font-semibold text-black">{{ $locale === 'ps' ? 'زموږ کیسه' : ($locale === 'fa' ? 'داستان ما' : 'Our Story') }}</span>
                <h1 class="mt-5 text-4xl font-bold leading-tight sm:text-5xl lg:text-6xl">{{ $translation->title }}</h1>
                @if($translation->excerpt)
                    <p class="mt-5 text-lg leading-8 text-white/80 sm:text-xl">{{ $translation->excerpt }}</p>
                @endif
            </div>
        </div>
    </section>

    <section class="py-12 sm:py-16 md:py-20">
        <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 md:grid-cols-2">
            <img src="https://qtrypzzcjebvfcihiynt.supabase.co/storage/v1/object/public/base44-prod/public/6944d141c2878421ef544832/f065351e6_bn.jpg" alt="Afghan craftsmanship" class="aspect-square w-full rounded-3xl object-cover shadow-2xl">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl">{{ $locale === 'ps' ? 'د هنر میراث' : ($locale === 'fa' ? 'میراث هنر' : 'A Legacy of Artistry') }}</h2>
                @foreach($paragraphs as $paragraph)
                    <p class="mt-5 leading-8 text-gray-600">{{ $paragraph }}</p>
                @endforeach
                <div class="mt-6 flex flex-wrap gap-3">
                    @foreach([
                        $locale === 'ps' ? 'لاسي جوړ' : ($locale === 'fa' ? 'دست‌دوز' : 'Handcrafted'),
                        $locale === 'ps' ? 'اصلي' : ($locale === 'fa' ? 'اصیل' : 'Authentic'),
                        $locale === 'ps' ? 'پایدار' : ($locale === 'fa' ? 'پایدار' : 'Sustainable'),
                        $locale === 'ps' ? 'کیفیت' : ($locale === 'fa' ? 'کیفیت' : 'Quality'),
                    ] as $tag)
                        <span class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-3 py-1.5 text-sm text-gray-700">✓ {{ $tag }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-16">
        <div class="mx-auto max-w-7xl px-4">
            <div class="mb-10 text-center">
                <h2 class="text-3xl font-bold text-gray-900">{{ $locale === 'ps' ? 'زموږ ارزښتونه' : ($locale === 'fa' ? 'ارزش‌های ما' : 'Our Values') }}</h2>
            </div>
            <div class="grid gap-6 md:grid-cols-4">
                @foreach([
                    ['ae5604c05_MasterArtisian.jpg', $locale === 'ps' ? 'استادان' : ($locale === 'fa' ? 'استادکاران' : 'Master Artisans')],
                    ['f93ac8c2b_FineEmbroidery.jpg', $locale === 'ps' ? 'ښکلې ګنډنه' : ($locale === 'fa' ? 'خامک‌دوزی ظریف' : 'Fine Embroidery')],
                    ['c83945fa0_QualityFabrics.jpg', $locale === 'ps' ? 'کیفیت لرونکي ټوکر' : ($locale === 'fa' ? 'پارچه باکیفیت' : 'Quality Fabrics')],
                    ['f065351e6_bn.jpg', $locale === 'ps' ? 'افغان میراث' : ($locale === 'fa' ? 'میراث افغانی' : 'Afghan Heritage')],
                ] as [$image, $title])
                    <article class="rounded-xl border border-gray-200 bg-white p-5 text-center shadow-sm transition hover:shadow-lg">
                        <img src="https://qtrypzzcjebvfcihiynt.supabase.co/storage/v1/object/public/base44-prod/public/6944d141c2878421ef544832/{{ $image }}" alt="{{ $title }}" class="mx-auto h-24 w-24 rounded-full object-cover">
                        <h3 class="mt-4 font-semibold text-gray-900">{{ $title }}</h3>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-gradient-to-r from-[#881C27] to-[#2A6867] py-16 text-white">
        <div class="mx-auto grid max-w-7xl grid-cols-2 gap-8 px-4 text-center md:grid-cols-4">
            @foreach([['5000+', 'Happy Customers'], ['50+', 'Countries Served'], ['200+', 'Artisans Supported'], ['100%', 'Authentic Products']] as [$value, $label])
                <div><p class="text-4xl font-bold text-[#D4AF37] md:text-5xl">{{ $value }}</p><p class="mt-2 text-sm text-white/70">{{ $label }}</p></div>
            @endforeach
        </div>
    </section>

@elseif($pageKey === 'faq')
    <section class="bg-gradient-to-r from-[#881C27] to-[#2A6867] py-14 text-center text-white sm:py-20">
        <div class="mx-auto max-w-7xl px-4">
            <span class="inline-flex rounded-full bg-[#D4AF37] px-4 py-2 text-xs font-semibold text-black">FAQ</span>
            <h1 class="mt-5 text-4xl font-bold sm:text-5xl">{{ $translation->title }}</h1>
            @if($translation->excerpt)
                <p class="mx-auto mt-4 max-w-2xl text-lg text-white/80">{{ $translation->excerpt }}</p>
            @endif
        </div>
    </section>

    <section class="py-16 sm:py-20">
        <div class="mx-auto max-w-4xl px-4">
            <div class="space-y-4" x-data="{ open: 0 }">
                @foreach($paragraphs->chunk(2) as $pair)
                    @php($question = $pair->values()->get(0))
                    @php($answer = $pair->values()->get(1))
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

@elseif($pageKey === 'measurement-guide')
    <section class="bg-gradient-to-r from-[#881C27] to-[#2A6867] py-14 text-center text-white sm:py-20">
        <div class="mx-auto max-w-7xl px-4">
            <x-icon name="ruler" class="mx-auto h-14 w-14 text-[#D4AF37]" />
            <h1 class="mt-5 text-4xl font-bold sm:text-5xl">{{ $translation->title }}</h1>
            @if($translation->excerpt)
                <p class="mx-auto mt-4 max-w-2xl text-lg text-white/80">{{ $translation->excerpt }}</p>
            @endif
        </div>
    </section>

    <section class="py-16 sm:py-20">
        <div class="mx-auto grid max-w-6xl items-start gap-8 px-4 lg:grid-cols-2">
            <div class="overflow-hidden rounded-3xl bg-white shadow-xl">
                <img src="{{ asset('images/kabulfit-live/measurement-guide.png') }}" alt="{{ $translation->title }}" class="w-full object-cover">
            </div>
            <div class="space-y-5">
                @foreach($paragraphs as $paragraph)
                    <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="flex gap-4">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-[#8B1538]/10 font-bold text-[#8B1538]">{{ $loop->iteration }}</span>
                            <p class="leading-7 text-gray-600">{{ $paragraph }}</p>
                        </div>
                    </article>
                @endforeach
                @auth
                    <a href="{{ route('measurements.create', ['locale' => $locale]) }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#881C27] to-[#2A6867] px-6 py-3 font-semibold text-white"><x-icon name="ruler" class="h-4 w-4" />{{ __('measurements.new_profile') }}</a>
                @endauth
            </div>
        </div>
    </section>

@elseif($pageKey === 'contact')
    <section class="bg-gradient-to-r from-[#881C27] to-[#2A6867] py-14 text-center text-white sm:py-20">
        <div class="mx-auto max-w-7xl px-4">
            <h1 class="text-4xl font-bold sm:text-5xl">{{ $translation->title }}</h1>
            @if($translation->excerpt)
                <p class="mx-auto mt-4 max-w-2xl text-lg text-white/80">{{ $translation->excerpt }}</p>
            @endif
        </div>
    </section>

    <section class="py-16 sm:py-20">
        <div class="mx-auto grid max-w-6xl gap-8 px-4 lg:grid-cols-2">
            <div class="space-y-4">
                <article class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <span class="grid h-12 w-12 place-items-center rounded-full bg-[#8B1538]/10 text-[#8B1538]">✉</span>
                    <h2 class="mt-4 text-xl font-semibold text-gray-900">{{ $locale === 'ps' ? 'برېښنالیک' : ($locale === 'fa' ? 'ایمیل' : 'Email') }}</h2>
                    <a href="mailto:{{ $contactEmail }}" class="mt-2 block text-[#00A651] hover:underline">{{ $contactEmail }}</a>
                </article>
                <article class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <span class="grid h-12 w-12 place-items-center rounded-full bg-[#2A6867]/10 text-[#2A6867]">◉</span>
                    <h2 class="mt-4 text-xl font-semibold text-gray-900">WhatsApp</h2>
                    <a href="https://wa.me/93794120017" target="_blank" rel="noopener noreferrer" class="mt-2 block text-[#00A651] hover:underline">+93 79 412 0017</a>
                </article>
                @foreach($paragraphs as $paragraph)
                    <p class="leading-7 text-gray-600">{{ $paragraph }}</p>
                @endforeach
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
                <h2 class="text-2xl font-bold text-gray-900">{{ $locale === 'ps' ? 'موږ ته پیغام واستوئ' : ($locale === 'fa' ? 'برای ما پیام بفرستید' : 'Send us a message') }}</h2>
                <p class="mt-2 text-sm text-gray-500">{{ $locale === 'ps' ? 'د فرمایش پوښتنې لپاره د فرمایش شمېره هم ولیکئ.' : ($locale === 'fa' ? 'برای پیگیری سفارش، شماره سفارش را نیز درج کنید.' : 'Include your order number for order-related questions.') }}</p>
                <div class="mt-6 grid gap-4">
                    <label class="grid gap-1.5"><span class="text-sm font-medium">Name</span><input class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                    <label class="grid gap-1.5"><span class="text-sm font-medium">Email</span><input type="email" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                    <label class="grid gap-1.5"><span class="text-sm font-medium">Message</span><textarea rows="5" class="rounded-md border border-gray-200 px-3 py-2.5"></textarea></label>
                    <a href="mailto:{{ $contactEmail }}" class="inline-flex justify-center rounded-md bg-[#8B1538] px-5 py-3 text-sm font-semibold text-white">{{ $locale === 'ps' ? 'پیغام واستوئ' : ($locale === 'fa' ? 'ارسال پیام' : 'Send Message') }}</a>
                </div>
            </div>
        </div>
    </section>

@else
    @php
        $heroIcon = match($pageKey) {
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
            @if($translation->excerpt)
                <p class="mt-4 max-w-2xl text-lg text-white/80 {{ $pageKey === 'shipping-policy' ? 'mx-auto' : '' }}">{{ $translation->excerpt }}</p>
            @endif
        </div>
    </section>

    @if($pageKey === 'shipping-policy')
        <section class="border-b bg-white py-10">
            <div class="mx-auto grid max-w-7xl grid-cols-2 gap-6 px-4 md:grid-cols-4">
                @foreach([
                    ['truck', 'Worldwide', 'Tracked shipping'],
                    ['shield', 'Secure', 'Protected handling'],
                    ['chart', 'Live', 'Order tracking'],
                    ['package', 'Careful', 'Packed for transit'],
                ] as [$icon, $title, $desc])
                    <div class="text-center"><span class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-[#8B1538]/10 text-[#8B1538]"><x-icon :name="$icon" class="h-6 w-6" /></span><h3 class="mt-3 font-semibold text-gray-900">{{ $title }}</h3><p class="text-sm text-gray-500">{{ $desc }}</p></div>
                @endforeach
            </div>
        </section>
    @endif

    <section class="py-16 sm:py-20">
        <div class="mx-auto max-w-4xl px-4">
            <div class="space-y-6">
                @foreach($paragraphs as $paragraph)
                    <article class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
                        <div class="flex items-start gap-4">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-[#EF5350]/10 text-[#EF5350]">{{ $loop->iteration }}</span>
                            <p class="leading-8 text-gray-600">{{ $paragraph }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
@endsection
