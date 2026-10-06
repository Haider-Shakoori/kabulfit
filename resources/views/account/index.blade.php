@extends('layouts.app')

@section('content')
@php
    $locale = app()->getLocale();
    $contactEmail = app(\App\Services\Settings\SiteSettings::class)->get('site.contact_email', 'info@kabulfit.com');
    $labels = [
        'subtitle' => $locale === 'ps' ? 'خپل پروفایل او غوره توبونه اداره کړئ' : ($locale === 'fa' ? 'پروفایل و تنظیمات خود را مدیریت کنید' : 'Manage your profile and preferences'),
        'orders' => $locale === 'ps' ? 'فرمایشونه' : ($locale === 'fa' ? 'سفارش‌ها' : 'Orders'),
        'wishlist' => $locale === 'ps' ? 'خوښې' : ($locale === 'fa' ? 'علاقه‌مندی‌ها' : 'Wishlist'),
        'addresses' => $locale === 'ps' ? 'ادرسونه' : ($locale === 'fa' ? 'آدرس‌ها' : 'Addresses'),
        'admin' => $locale === 'ps' ? 'اډمین ډشبورډ' : ($locale === 'fa' ? 'داشبورد مدیریت' : 'Admin Dashboard'),
        'edit_profile' => $locale === 'ps' ? 'پروفایل سمول' : ($locale === 'fa' ? 'ویرایش پروفایل' : 'Edit Profile'),
        'save' => $locale === 'ps' ? 'خوندي کول' : ($locale === 'fa' ? 'ذخیره' : 'Save'),
        'cancel' => $locale === 'ps' ? 'لغوه' : ($locale === 'fa' ? 'لغو' : 'Cancel'),
        'recent_orders' => $locale === 'ps' ? 'وروستي فرمایشونه' : ($locale === 'fa' ? 'سفارش‌های اخیر' : 'Recent Orders'),
        'settings' => $locale === 'ps' ? 'تنظیمات' : ($locale === 'fa' ? 'تنظیمات' : 'Settings'),
        'view_all_orders' => $locale === 'ps' ? 'ټول فرمایشونه وګورئ' : ($locale === 'fa' ? 'مشاهده همه سفارش‌ها' : 'View All Orders'),
        'no_orders' => $locale === 'ps' ? 'تر اوسه فرمایش نشته' : ($locale === 'fa' ? 'هنوز سفارشی ندارید' : 'No orders yet'),
        'start_shopping' => $locale === 'ps' ? 'خرید پیل کړئ' : ($locale === 'fa' ? 'شروع خرید' : 'Start Shopping'),
        'set_default' => $locale === 'ps' ? 'اصلي یې کړئ' : ($locale === 'fa' ? 'پیش‌فرض کنید' : 'Set Default'),
        'add_address' => $locale === 'ps' ? 'نوی ادرس زیات کړئ' : ($locale === 'fa' ? 'افزودن آدرس جدید' : 'Add New Address'),
        'email_preferences' => $locale === 'ps' ? 'د برېښنالیک غوره توبونه' : ($locale === 'fa' ? 'تنظیمات ایمیل' : 'Email Preferences'),
        'order_updates' => $locale === 'ps' ? 'د فرمایش تازه معلومات' : ($locale === 'fa' ? 'به‌روزرسانی سفارش' : 'Order updates'),
        'promotions' => $locale === 'ps' ? 'تخفیفونه' : ($locale === 'fa' ? 'پیشنهادها' : 'Promotions'),
        'new_arrivals' => $locale === 'ps' ? 'نوي محصولات' : ($locale === 'fa' ? 'محصولات جدید' : 'New arrivals'),
        'language' => $locale === 'ps' ? 'ژبه' : ($locale === 'fa' ? 'زبان' : 'Language'),
        'danger_zone' => $locale === 'ps' ? 'حساسه برخه' : ($locale === 'fa' ? 'منطقه حساس' : 'Danger Zone'),
        'delete_account' => $locale === 'ps' ? 'زما حساب ړنګ کړئ' : ($locale === 'fa' ? 'حذف حساب من' : 'Delete My Account'),
        'delete_title' => $locale === 'ps' ? 'حساب ړنګول' : ($locale === 'fa' ? 'حذف حساب' : 'Delete Account'),
        'delete_copy' => $locale === 'ps' ? 'د حساب ړنګولو غوښتنې د ملاتړ له لارې تاییدېږي.' : ($locale === 'fa' ? 'درخواست حذف حساب از طریق پشتیبانی تأیید می‌شود.' : 'Account deletion requests are confirmed through support.'),
        'type_delete' => $locale === 'ps' ? 'د تایید لپاره DELETE ولیکئ:' : ($locale === 'fa' ? 'برای تأیید DELETE را تایپ کنید:' : 'Type DELETE to confirm:'),
        'contact_support' => $locale === 'ps' ? 'له ملاتړ سره اړیکه' : ($locale === 'fa' ? 'تماس با پشتیبانی' : 'Contact Support'),
    ];
    $initial = mb_strtoupper(mb_substr($user->name ?: $user->email, 0, 1));
@endphp

<div class="min-h-screen bg-[#FDFBF7]" x-data="{ tab: 'orders', editingProfile: false, addAddress: false, deleteDialog: false, deleteText: '' }">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:py-8">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ __('account.title') }}</h1>
                <p class="mt-1 text-gray-600">{{ $labels['subtitle'] }}</p>
            </div>
            @if($user->hasPermission('admin.access'))
                <a href="{{ route('admin.dashboard', ['locale' => $locale]) }}" class="inline-flex items-center gap-2 rounded-xl bg-[#881C27] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#72152e]">
                    <x-icon name="shield" class="h-4 w-4" />
                    {{ $labels['admin'] }}
                </a>
            @endif
        </div>

        @if(session('status'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
        @endif
        <x-form-errors />

        <div class="mb-8 grid grid-cols-2 gap-4 md:grid-cols-3">
            <a href="{{ route('orders.index', ['locale' => $locale]) }}" class="group rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                <div class="flex items-center justify-between">
                    <span class="grid h-12 w-12 place-items-center rounded-full bg-blue-100 text-blue-600"><x-icon name="bag" class="h-6 w-6" /></span>
                    <span class="text-xl text-gray-300 transition group-hover:text-[#881C27]">›</span>
                </div>
                <p class="mt-4 text-2xl font-bold">{{ $stats['orders'] }}</p>
                <p class="text-sm text-gray-500">{{ $labels['orders'] }}</p>
            </a>

            <a href="{{ route('wishlist', ['locale' => $locale]) }}" class="group rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                <div class="flex items-center justify-between">
                    <span class="grid h-12 w-12 place-items-center rounded-full bg-red-100 text-red-600"><x-icon name="heart" class="h-6 w-6" /></span>
                    <span class="text-xl text-gray-300 transition group-hover:text-[#881C27]">›</span>
                </div>
                <p class="mt-4 text-2xl font-bold">{{ $stats['wishlist'] }}</p>
                <p class="text-sm text-gray-500">{{ $labels['wishlist'] }}</p>
            </a>

            <button type="button" @click="tab = 'addresses'" class="group col-span-2 rounded-2xl border border-gray-100 bg-white p-4 text-start shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg md:col-span-1">
                <div class="flex items-center justify-between">
                    <span class="grid h-12 w-12 place-items-center rounded-full bg-green-100 text-green-600">⌖</span>
                    <span class="text-xl text-gray-300 transition group-hover:text-[#881C27]">›</span>
                </div>
                <p class="mt-4 text-2xl font-bold">{{ $stats['addresses'] }}</p>
                <p class="text-sm text-gray-500">{{ $labels['addresses'] }}</p>
            </button>
        </div>

        <div class="grid gap-8 lg:grid-cols-3">
            <aside class="self-start rounded-2xl border border-gray-100 bg-white shadow-sm lg:sticky lg:top-36">
                <div class="p-6 text-center">
                    <div class="mx-auto mb-4 grid h-24 w-24 place-items-center rounded-full bg-gradient-to-br from-[#881C27] to-[#D4AF37]">
                        <span class="text-3xl font-bold text-white">{{ $initial }}</span>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ $user->name }}</h2>
                    <p class="mt-1 break-all text-sm text-gray-500">{{ $user->email }}</p>
                    @if($user->hasPermission('admin.access'))
                        <span class="mt-3 inline-flex rounded-full bg-[#881C27] px-3 py-1 text-xs font-semibold text-white">Admin</span>
                    @endif
                </div>

                <div class="border-t border-gray-100 p-6">
                    <form x-show="editingProfile" x-cloak method="POST" action="{{ route('account.update', ['locale' => $locale]) }}" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <label class="grid gap-1.5 text-start">
                            <span class="text-sm font-medium text-gray-700">{{ __('auth.name') }}</span>
                            <input name="name" value="{{ old('name', $user->name) }}" required class="rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-[#881C27] focus:ring-2 focus:ring-[#881C27]/10">
                        </label>
                        <label class="grid gap-1.5 text-start">
                            <span class="text-sm font-medium text-gray-700">{{ __('auth.phone') }}</span>
                            <input name="phone" value="{{ old('phone', $user->phone) }}" class="rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-[#881C27] focus:ring-2 focus:ring-[#881C27]/10">
                        </label>
                        <input type="hidden" name="preferred_locale" value="{{ $user->preferredLocale() }}">
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 rounded-xl bg-[#881C27] px-4 py-2.5 text-sm font-semibold text-white">{{ $labels['save'] }}</button>
                            <button type="button" @click="editingProfile = false" class="rounded-xl border-2 border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:border-[#881C27] hover:text-[#881C27]">{{ $labels['cancel'] }}</button>
                        </div>
                    </form>

                    <button x-show="!editingProfile" type="button" @click="editingProfile = true" class="flex w-full items-center justify-center gap-2 rounded-xl border-2 border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:border-[#881C27] hover:text-[#881C27]">
                        ✎ {{ $labels['edit_profile'] }}
                    </button>

                    @unless($user->hasVerifiedEmail())
                        <form method="POST" action="{{ route('verification.send', ['locale' => $locale]) }}" class="mt-3">
                            @csrf
                            <button type="submit" class="w-full rounded-xl bg-amber-50 px-4 py-2.5 text-sm font-medium text-amber-700">{{ __('auth.resend_verification') }}</button>
                        </form>
                    @endunless

                    <div class="my-4 border-t border-gray-100"></div>
                    <form method="POST" action="{{ route('logout', ['locale' => $locale]) }}">
                        @csrf
                        <button type="submit" class="w-full rounded-xl px-4 py-2.5 text-sm font-medium text-red-500 transition hover:bg-red-50 hover:text-red-700">{{ __('auth.logout') }}</button>
                    </form>
                </div>
            </aside>

            <section class="min-w-0 lg:col-span-2">
                <div class="grid grid-cols-3 rounded-xl bg-gray-100 p-1">
                    <button type="button" @click="tab = 'orders'" class="rounded-lg px-3 py-2.5 text-sm font-medium transition" :class="tab === 'orders' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500'">{{ $labels['recent_orders'] }}</button>
                    <button type="button" @click="tab = 'addresses'" class="rounded-lg px-3 py-2.5 text-sm font-medium transition" :class="tab === 'addresses' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500'">{{ $labels['addresses'] }}</button>
                    <button type="button" @click="tab = 'settings'" class="rounded-lg px-3 py-2.5 text-sm font-medium transition" :class="tab === 'settings' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500'">{{ $labels['settings'] }}</button>
                </div>

                <div x-show="tab === 'orders'" class="mt-6 space-y-4">
                    @forelse($recentOrders as $order)
                        <a href="{{ route('orders.show', ['locale' => $locale, 'order' => $order->uuid]) }}" class="block rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition hover:shadow-md">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $order->number }}</p>
                                    <p class="mt-1 text-sm text-gray-500">{{ $order->created_at?->format('M j, Y') }}</p>
                                </div>
                                <div class="text-end">
                                    <p class="font-bold text-[#881C27]">{{ number_format($order->total_minor / 100, 2) }} {{ $order->currency }}</p>
                                    <span class="mt-1 inline-flex rounded-full border border-gray-200 px-2.5 py-1 text-xs capitalize text-gray-600">{{ str($order->status)->replace('_', ' ') }}</span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-2xl bg-white py-12 text-center shadow-sm">
                            <x-icon name="bag" class="mx-auto h-12 w-12 text-gray-300" />
                            <p class="mt-4 text-gray-500">{{ $labels['no_orders'] }}</p>
                            <a href="{{ route('shop', ['locale' => $locale]) }}" class="mt-5 inline-flex rounded-xl bg-[#881C27] px-5 py-2.5 text-sm font-semibold text-white">{{ $labels['start_shopping'] }}</a>
                        </div>
                    @endforelse

                    @if($stats['orders'] > 0)
                        <a href="{{ route('orders.index', ['locale' => $locale]) }}" class="flex w-full items-center justify-center gap-2 rounded-xl border-2 border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-700 transition hover:border-[#881C27] hover:text-[#881C27]">
                            {{ $labels['view_all_orders'] }} <span class="rtl:rotate-180">›</span>
                        </a>
                    @endif
                </div>

                <div x-show="tab === 'addresses'" x-cloak class="mt-6 space-y-4">
                    @forelse($user->addresses as $address)
                        <article @class([
                            'rounded-2xl bg-white p-4 shadow-sm border',
                            'border-[#881C27]' => $address->is_default,
                            'border-gray-100' => ! $address->is_default,
                        ])>
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="mb-2 flex flex-wrap items-center gap-2">
                                        <h3 class="font-semibold text-gray-900">{{ $address->label ?: __('account.address') }}</h3>
                                        @if($address->is_default)
                                            <span class="rounded-full bg-[#881C27] px-2.5 py-1 text-xs font-semibold text-white">{{ __('account.default') }}</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600">{{ $address->recipient_name }}</p>
                                    <p class="text-sm text-gray-600">{{ $address->address_line1 }}</p>
                                    @if($address->address_line2)<p class="text-sm text-gray-600">{{ $address->address_line2 }}</p>@endif
                                    <p class="text-sm text-gray-600">{{ $address->city }}@if($address->province), {{ $address->province }}@endif @if($address->postal_code) {{ $address->postal_code }}@endif</p>
                                    <p class="text-sm text-gray-600">{{ $address->country_code }} · {{ $address->phone }}</p>
                                </div>

                                <div class="flex shrink-0 flex-wrap justify-end gap-1">
                                    @unless($address->is_default)
                                        <form method="POST" action="{{ route('addresses.update', ['locale' => $locale, 'address' => $address->uuid]) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="label" value="{{ $address->label }}">
                                            <input type="hidden" name="recipient_name" value="{{ $address->recipient_name }}">
                                            <input type="hidden" name="phone" value="{{ $address->phone }}">
                                            <input type="hidden" name="country_code" value="{{ $address->country_code }}">
                                            <input type="hidden" name="province" value="{{ $address->province }}">
                                            <input type="hidden" name="city" value="{{ $address->city }}">
                                            <input type="hidden" name="address_line1" value="{{ $address->address_line1 }}">
                                            <input type="hidden" name="address_line2" value="{{ $address->address_line2 }}">
                                            <input type="hidden" name="postal_code" value="{{ $address->postal_code }}">
                                            <input type="hidden" name="is_default" value="1">
                                            <button type="submit" class="rounded-lg px-3 py-2 text-xs font-medium text-gray-600 transition hover:bg-gray-50 hover:text-[#881C27]">{{ $labels['set_default'] }}</button>
                                        </form>
                                    @endunless
                                    <form method="POST" action="{{ route('addresses.destroy', ['locale' => $locale, 'address' => $address->uuid]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="grid h-9 w-9 place-items-center rounded-lg text-red-500 transition hover:bg-red-50 hover:text-red-700" aria-label="{{ __('account.delete') }}"><x-icon name="trash" class="h-4 w-4" /></button>
                                    </form>
                                </div>
                            </div>

                            <details class="mt-4 border-t border-gray-100 pt-4">
                                <summary class="cursor-pointer text-sm font-semibold text-[#881C27]">{{ __('account.edit') }}</summary>
                                <form method="POST" action="{{ route('addresses.update', ['locale' => $locale, 'address' => $address->uuid]) }}" class="mt-4">
                                    @csrf
                                    @method('PUT')
                                    @include('account.partials.base44-address-fields', ['address' => $address])
                                    <button type="submit" class="mt-4 rounded-xl bg-[#881C27] px-5 py-2.5 text-sm font-semibold text-white">{{ __('account.save_address') }}</button>
                                </form>
                            </details>
                        </article>
                    @empty
                        <div class="rounded-2xl bg-white py-10 text-center text-gray-500 shadow-sm">{{ __('account.no_addresses') }}</div>
                    @endforelse

                    <button type="button" @click="addAddress = true" class="flex w-full items-center justify-center gap-2 rounded-xl border-2 border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-700 transition hover:border-[#881C27] hover:text-[#881C27]">
                        <x-icon name="plus" class="h-4 w-4" />
                        {{ $labels['add_address'] }}
                    </button>
                </div>

                <div x-show="tab === 'settings'" x-cloak class="mt-6">
                    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                        <section>
                            <h3 class="font-semibold text-gray-900">{{ $labels['email_preferences'] }}</h3>
                            <div class="mt-4 space-y-4">
                                @foreach([
                                    [$labels['order_updates'], $locale === 'ps' ? 'د فرمایش د حالت خبرتیاوې' : ($locale === 'fa' ? 'اطلاع از وضعیت سفارش' : 'Get notified about your order status')],
                                    [$labels['promotions'], $locale === 'ps' ? 'ځانګړي وړاندیزونه او تخفیفونه' : ($locale === 'fa' ? 'پیشنهادها و تخفیف‌های ویژه' : 'Receive exclusive offers and discounts')],
                                    [$labels['new_arrivals'], $locale === 'ps' ? 'له نویو محصولاتو لومړی خبر شئ' : ($locale === 'fa' ? 'از محصولات جدید زودتر باخبر شوید' : 'Be the first to know about new products')],
                                ] as [$title, $description])
                                    <div class="flex items-center justify-between gap-4">
                                        <div><p class="font-medium text-gray-900">{{ $title }}</p><p class="text-sm text-gray-500">{{ $description }}</p></div>
                                        <input type="checkbox" checked disabled class="h-5 w-5 rounded border-gray-300 text-[#881C27] opacity-70">
                                    </div>
                                @endforeach
                            </div>
                        </section>

                        <div class="my-6 border-t border-gray-100"></div>

                        <section>
                            <h3 class="font-semibold text-gray-900">{{ $labels['language'] }}</h3>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach(['en' => 'English', 'ps' => 'پښتو', 'fa' => 'دری'] as $code => $name)
                                    <a href="{{ route('account', ['locale' => $code]) }}" @class([
                                        'rounded-lg border-2 px-4 py-2 text-sm font-medium transition',
                                        'border-[#881C27] bg-[#881C27] text-white' => $locale === $code,
                                        'border-gray-300 text-gray-700 hover:border-[#881C27] hover:text-[#881C27]' => $locale !== $code,
                                    ])>{{ $name }}</a>
                                @endforeach
                            </div>
                        </section>

                        <div class="my-6 border-t border-gray-100"></div>

                        <section>
                            <h3 class="font-semibold text-red-600">{{ $labels['danger_zone'] }}</h3>
                            <p class="mt-2 text-sm leading-6 text-gray-500">{{ $labels['delete_copy'] }}</p>
                            <button type="button" @click="deleteDialog = true; deleteText = ''" class="mt-4 inline-flex items-center gap-2 rounded-xl border border-red-300 px-4 py-2.5 text-sm font-semibold text-red-600 transition hover:border-red-500 hover:bg-red-50">
                                <x-icon name="trash" class="h-4 w-4" />
                                {{ $labels['delete_account'] }}
                            </button>
                        </section>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div x-show="addAddress" x-cloak class="fixed inset-0 z-[100] grid place-items-center p-4">
        <button type="button" class="absolute inset-0 bg-black/50" @click="addAddress = false" aria-label="Close"></button>
        <div x-transition class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">{{ $labels['add_address'] }}</h2>
                <button type="button" @click="addAddress = false" class="grid h-10 w-10 place-items-center rounded-full bg-gray-100"><x-icon name="close" class="h-5 w-5" /></button>
            </div>
            <form method="POST" action="{{ route('addresses.store', ['locale' => $locale]) }}">
                @csrf
                @include('account.partials.base44-address-fields', ['address' => null])
                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" @click="addAddress = false" class="rounded-xl border-2 border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700">{{ $labels['cancel'] }}</button>
                    <button type="submit" class="rounded-xl bg-[#881C27] px-5 py-2.5 text-sm font-semibold text-white">{{ __('account.save_address') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="deleteDialog" x-cloak class="fixed inset-0 z-[100] grid place-items-center p-4" @keydown.escape.window="deleteDialog = false">
        <button type="button" class="absolute inset-0 bg-black/50" @click="deleteDialog = false" aria-label="Close"></button>
        <div x-transition class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-red-600">{{ $labels['delete_title'] }}</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-600">{{ $labels['delete_copy'] }}</p>
                </div>
                <button type="button" @click="deleteDialog = false" class="grid h-9 w-9 place-items-center rounded-full bg-gray-100"><x-icon name="close" class="h-4 w-4" /></button>
            </div>
            <label class="mt-5 grid gap-2">
                <span class="text-sm text-gray-600">{{ $labels['type_delete'] }}</span>
                <input x-model="deleteText" placeholder="DELETE" class="rounded-xl border border-red-200 px-3 py-2.5 outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100">
            </label>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" @click="deleteDialog = false" class="rounded-xl border-2 border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700">{{ $labels['cancel'] }}</button>
                <a href="mailto:{{ $contactEmail }}?subject=KabulFit%20Account%20Deletion%20Request" :class="deleteText === 'DELETE' ? '' : 'pointer-events-none opacity-40'" class="rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white">{{ $labels['contact_support'] }}</a>
            </div>
        </div>
    </div>
</div>
@endsection
