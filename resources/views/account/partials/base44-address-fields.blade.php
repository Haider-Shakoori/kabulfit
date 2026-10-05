@props(['address' => null])

<div class="grid gap-4 sm:grid-cols-2">
    <label class="grid gap-1.5">
        <span class="text-sm font-medium text-gray-700">{{ __('account.label') }}</span>
        <input name="label" value="{{ old('label', $address?->label) }}" placeholder="{{ __('account.label_example') }}" class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-[#881C27] focus:ring-2 focus:ring-[#881C27]/10">
    </label>
    <label class="grid gap-1.5">
        <span class="text-sm font-medium text-gray-700">{{ __('account.recipient_name') }}</span>
        <input name="recipient_name" value="{{ old('recipient_name', $address?->recipient_name ?? auth()->user()->name) }}" required class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-[#881C27] focus:ring-2 focus:ring-[#881C27]/10">
    </label>
    <label class="grid gap-1.5">
        <span class="text-sm font-medium text-gray-700">{{ __('auth.phone') }}</span>
        <input name="phone" value="{{ old('phone', $address?->phone ?? auth()->user()->phone) }}" required class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-[#881C27] focus:ring-2 focus:ring-[#881C27]/10">
    </label>
    <label class="grid gap-1.5">
        <span class="text-sm font-medium text-gray-700">{{ __('account.country_code') }}</span>
        <input name="country_code" value="{{ old('country_code', $address?->country_code ?? 'AF') }}" maxlength="2" required class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm uppercase outline-none transition focus:border-[#881C27] focus:ring-2 focus:ring-[#881C27]/10">
    </label>
    <label class="grid gap-1.5">
        <span class="text-sm font-medium text-gray-700">{{ __('account.province') }}</span>
        <input name="province" value="{{ old('province', $address?->province) }}" class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-[#881C27] focus:ring-2 focus:ring-[#881C27]/10">
    </label>
    <label class="grid gap-1.5">
        <span class="text-sm font-medium text-gray-700">{{ __('account.city') }}</span>
        <input name="city" value="{{ old('city', $address?->city) }}" required class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-[#881C27] focus:ring-2 focus:ring-[#881C27]/10">
    </label>
    <label class="grid gap-1.5 sm:col-span-2">
        <span class="text-sm font-medium text-gray-700">{{ __('account.address_line1') }}</span>
        <input name="address_line1" value="{{ old('address_line1', $address?->address_line1) }}" required class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-[#881C27] focus:ring-2 focus:ring-[#881C27]/10">
    </label>
    <label class="grid gap-1.5 sm:col-span-2">
        <span class="text-sm font-medium text-gray-700">{{ __('account.address_line2') }}</span>
        <input name="address_line2" value="{{ old('address_line2', $address?->address_line2) }}" class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-[#881C27] focus:ring-2 focus:ring-[#881C27]/10">
    </label>
    <label class="grid gap-1.5">
        <span class="text-sm font-medium text-gray-700">{{ __('account.postal_code') }}</span>
        <input name="postal_code" value="{{ old('postal_code', $address?->postal_code) }}" class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-[#881C27] focus:ring-2 focus:ring-[#881C27]/10">
    </label>
    <label class="flex items-center gap-3 self-end rounded-xl bg-gray-50 px-4 py-3">
        <input type="checkbox" name="is_default" value="1" @checked(old('is_default', $address?->is_default)) class="h-4 w-4 rounded border-gray-300 text-[#881C27] focus:ring-[#881C27]">
        <span class="text-sm font-medium text-gray-700">{{ __('account.make_default') }}</span>
    </label>
</div>
