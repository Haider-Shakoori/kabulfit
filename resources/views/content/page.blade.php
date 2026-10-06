@extends('layouts.app')

@section('content')
@php
    $pageKey = $translation->page->page_key;
    $paragraphs = collect(preg_split('/\R{2,}/u', trim($translation->body ?? '')))->filter()->values();
    $locale = app()->getLocale();
    $contactEmail = app(\App\Services\Settings\SiteSettings::class)->get('site.contact_email', 'info@kabulfit.com');
@endphp

@if ($pageKey === 'about')
    @include('content.pages.about')
@elseif ($pageKey === 'faq')
    @include('content.pages.faq')
@elseif ($pageKey === 'measurement-guide')
    @include('content.pages.measurement-guide')
@elseif ($pageKey === 'contact')
    @include('content.pages.contact')
@else
    @include('content.pages.policy')
@endif
@endsection
