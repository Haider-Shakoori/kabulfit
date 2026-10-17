@extends('layouts.app')

@section('content')
@php($translation = $category->translation())
@include('catalog._base44-results', [
    'action' => route('categories.show', ['locale' => app()->getLocale(), 'slug' => $translation?->slug]),
    'lockedCategory' => $translation?->slug,
    'lockedCollection' => null,
    'pageTitle' => $translation?->name ?? __('site.shop'),
    'pageDescription' => $translation?->description ?: __('site.shop_description'),
    'pageBadge' => $translation?->name,
])
@endsection
