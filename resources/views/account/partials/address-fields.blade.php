@php($prefix = $address?->uuid ?? 'new')
<div class="form-grid">
    <label><span>{{ __('account.label') }}</span><input name="label" value="{{ old('label', $address?->label) }}" placeholder="{{ __('account.label_example') }}"></label>
    <label><span>{{ __('account.recipient_name') }}</span><input name="recipient_name" value="{{ old('recipient_name', $address?->recipient_name ?? auth()->user()->name) }}" required></label>
    <label><span>{{ __('auth.phone') }}</span><input name="phone" value="{{ old('phone', $address?->phone ?? auth()->user()->phone) }}" required></label>
    <label><span>{{ __('account.country_code') }}</span><input name="country_code" value="{{ old('country_code', $address?->country_code ?? 'AF') }}" maxlength="2" required></label>
    <label><span>{{ __('account.province') }}</span><input name="province" value="{{ old('province', $address?->province) }}"></label>
    <label><span>{{ __('account.city') }}</span><input name="city" value="{{ old('city', $address?->city) }}" required></label>
    <label class="field-wide"><span>{{ __('account.address_line1') }}</span><input name="address_line1" value="{{ old('address_line1', $address?->address_line1) }}" required></label>
    <label class="field-wide"><span>{{ __('account.address_line2') }}</span><input name="address_line2" value="{{ old('address_line2', $address?->address_line2) }}"></label>
    <label><span>{{ __('account.postal_code') }}</span><input name="postal_code" value="{{ old('postal_code', $address?->postal_code) }}"></label>
    <label class="check-row"><input type="checkbox" name="is_default" value="1" @checked(old('is_default', $address?->is_default))><span>{{ __('account.make_default') }}</span></label>
</div>
