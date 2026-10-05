@extends('layouts.app')

@section('content')
@php($translation = $collection->translation())
@include('catalog._base44-results', [
    'action' => route('collections.show', ['locale' => app()->getLocale(), 'slug' => $translation?->slug]),
    'lockedCategory' => null,
    'lockedCollection' => $translation?->slug,
    'pageTitle' => $translation?->name ?? __('site.shop'),
    'pageDescription' => $translation?->description ?: __('site.shop_description'),
    'pageBadge' => __('site.collection'),
])
@endsection
