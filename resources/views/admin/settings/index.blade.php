@extends('layouts.admin')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <x-form-errors />
    @if(session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
    @endif

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
        <p class="mt-1 text-sm text-gray-500">Manage storefront contact details, SEO, and localized metadata.</p>
    </div>

    <form method="POST" action="{{ route('admin.settings.update', ['locale' => app()->getLocale()]) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5 flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-lg bg-gray-100 text-gray-600"><x-icon name="settings" class="h-5 w-5" /></span>
                <div><h2 class="font-semibold text-gray-900">General</h2><p class="text-sm text-gray-500">Primary contact configuration</p></div>
            </div>
            <label class="grid gap-1.5 max-w-xl"><span class="text-sm font-medium">Contact email</span><input type="email" name="contact_email" value="{{ $values['contact_email'] }}" required class="rounded-md border border-gray-200 px-3 py-2.5 outline-none focus:border-[#8B1538]"></label>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5 flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-lg bg-gray-100 text-gray-600"><x-icon name="users" class="h-5 w-5" /></span>
                <div><h2 class="font-semibold text-gray-900">Social Links</h2><p class="text-sm text-gray-500">Base44 footer and contact social destinations</p></div>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <label class="grid gap-1.5"><span class="text-sm font-medium">Facebook URL</span><input type="url" name="facebook_url" value="{{ $values['facebook_url'] }}" class="rounded-md border border-gray-200 px-3 py-2.5 outline-none focus:border-[#8B1538]"></label>
                <label class="grid gap-1.5"><span class="text-sm font-medium">Instagram URL</span><input type="url" name="instagram_url" value="{{ $values['instagram_url'] }}" class="rounded-md border border-gray-200 px-3 py-2.5 outline-none focus:border-[#8B1538]"></label>
                <label class="grid gap-1.5"><span class="text-sm font-medium">TikTok URL</span><input type="url" name="tiktok_url" value="{{ $values['tiktok_url'] }}" class="rounded-md border border-gray-200 px-3 py-2.5 outline-none focus:border-[#8B1538]"></label>
                <label class="grid gap-1.5"><span class="text-sm font-medium">YouTube URL</span><input type="url" name="youtube_url" value="{{ $values['youtube_url'] }}" class="rounded-md border border-gray-200 px-3 py-2.5 outline-none focus:border-[#8B1538]"></label>
                <label class="grid gap-1.5 md:col-span-2"><span class="text-sm font-medium">WhatsApp URL</span><input type="url" name="whatsapp_url" value="{{ $values['whatsapp_url'] }}" class="rounded-md border border-gray-200 px-3 py-2.5 outline-none focus:border-[#8B1538]"></label>
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5 flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-lg bg-gray-100 text-gray-600"><x-icon name="shield" class="h-5 w-5" /></span>
                <div><h2 class="font-semibold text-gray-900">Payment Integrations</h2><p class="text-sm text-gray-500">Credential status only. Secrets remain in the server environment.</p></div>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-lg bg-gray-50 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <strong class="text-gray-900">Stripe</strong>
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $values['payment_integrations']['stripe'] ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">{{ $values['payment_integrations']['stripe'] ? 'Configured' : 'Not configured' }}</span>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">PaymentIntent + verified webhook flow</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <strong class="text-gray-900">PayPal</strong>
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $values['payment_integrations']['paypal'] ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">{{ $values['payment_integrations']['paypal'] ? 'Configured' : 'Not configured' }}</span>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Mode: {{ strtoupper($values['payment_integrations']['paypal_mode']) }} · Currencies: {{ implode(', ', $values['payment_integrations']['paypal_currencies']) }}</p>
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5 flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-lg bg-gray-100 text-gray-600"><x-icon name="chart" class="h-5 w-5" /></span>
                <div><h2 class="font-semibold text-gray-900">Analytics</h2><p class="text-sm text-gray-500">Optional Meta Pixel and Google Analytics identifiers. No secret keys are stored here.</p></div>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <label class="grid gap-1.5"><span class="text-sm font-medium">Meta Pixel ID</span><input name="meta_pixel_id" value="{{ $values['meta_pixel_id'] }}" placeholder="2203881733746506" class="rounded-md border border-gray-200 px-3 py-2.5 outline-none focus:border-[#8B1538]"></label>
                <label class="grid gap-1.5"><span class="text-sm font-medium">GA4 Measurement ID</span><input name="ga_measurement_id" value="{{ $values['ga_measurement_id'] }}" placeholder="G-XXXXXXXXXX" class="rounded-md border border-gray-200 px-3 py-2.5 uppercase outline-none focus:border-[#8B1538]"></label>
            </div>
        </section>

        @foreach(config('kabulfit.supported_locales') as $loc)
            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="mb-5 flex items-center justify-between">
                    <div><h2 class="font-semibold text-gray-900">Homepage SEO</h2><p class="text-sm text-gray-500">{{ strtoupper($loc) }}</p></div>
                    <span class="rounded-full bg-[#8B1538]/10 px-3 py-1 text-xs font-semibold text-[#8B1538]">{{ strtoupper($loc) }}</span>
                </div>
                <div class="grid gap-4">
                    <label class="grid gap-1.5"><span class="text-sm font-medium">Title</span><input name="titles[{{ $loc }}]" value="{{ $values['titles'][$loc] }}" required class="rounded-md border border-gray-200 px-3 py-2.5 outline-none focus:border-[#8B1538]"></label>
                    <label class="grid gap-1.5"><span class="text-sm font-medium">Description</span><textarea name="descriptions[{{ $loc }}]" rows="4" maxlength="500" required class="rounded-md border border-gray-200 px-3 py-2.5 outline-none focus:border-[#8B1538]">{{ $values['descriptions'][$loc] }}</textarea></label>
                </div>
            </section>
        @endforeach

        <div class="flex justify-end"><button type="submit" class="rounded-md bg-[#8B1538] px-6 py-2.5 text-sm font-semibold text-white hover:bg-[#6d102c]">Save settings</button></div>
    </form>
</div>
@endsection
