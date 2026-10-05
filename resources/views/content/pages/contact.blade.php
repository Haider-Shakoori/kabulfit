<section class="bg-gradient-to-r from-[#881C27] to-[#2A6867] py-14 text-center text-white sm:py-20">
    <div class="mx-auto max-w-7xl px-4">
        <h1 class="text-4xl font-bold sm:text-5xl">{{ $translation->title }}</h1>
        @if ($translation->excerpt)
            <p class="mx-auto mt-4 max-w-2xl text-lg text-white/80">{{ $translation->excerpt }}</p>
        @endif
    </div>
</section>
<section class="py-16 sm:py-20">
    <div class="mx-auto grid max-w-6xl gap-8 px-4 lg:grid-cols-2">
        <div class="space-y-4">
            <article class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm"><span class="grid h-12 w-12 place-items-center rounded-full bg-[#8B1538]/10 text-[#8B1538]">✉</span><h2 class="mt-4 text-xl font-semibold text-gray-900">Email</h2><a href="mailto:{{ $contactEmail }}" class="mt-2 block text-[#00A651] hover:underline">{{ $contactEmail }}</a></article>
            <article class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm"><span class="grid h-12 w-12 place-items-center rounded-full bg-[#2A6867]/10 text-[#2A6867]">◉</span><h2 class="mt-4 text-xl font-semibold text-gray-900">WhatsApp</h2><a href="https://wa.me/93794120017" target="_blank" rel="noopener noreferrer" class="mt-2 block text-[#00A651] hover:underline">+93 79 412 0017</a></article>
            @foreach ($paragraphs as $paragraph)<p class="leading-7 text-gray-600">{{ $paragraph }}</p>@endforeach
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-2xl font-bold text-gray-900">Send us a message</h2>
            <p class="mt-2 text-sm text-gray-500">Include your order number for order-related questions.</p>
            <div class="mt-6 grid gap-4">
                <label class="grid gap-1.5"><span class="text-sm font-medium">Name</span><input class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                <label class="grid gap-1.5"><span class="text-sm font-medium">Email</span><input type="email" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                <label class="grid gap-1.5"><span class="text-sm font-medium">Message</span><textarea rows="5" class="rounded-md border border-gray-200 px-3 py-2.5"></textarea></label>
                <a href="mailto:{{ $contactEmail }}" class="inline-flex justify-center rounded-md bg-[#8B1538] px-5 py-3 text-sm font-semibold text-white">Send Message</a>
            </div>
        </div>
    </div>
</section>