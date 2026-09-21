@extends('layouts.app')

@section('content')
@php($translation = $category->translation())
<section class="page-hero"><div class="container"><nav class="breadcrumbs" aria-label="{{ __('site.breadcrumbs') }}"><a href="{{ route('home', ['locale' => app()->getLocale()]) }}">{{ __('site.home') }}</a><span>/</span><a href="{{ route('shop', ['locale' => app()->getLocale()]) }}">{{ __('site.shop') }}</a><span>/</span><span aria-current="page">{{ $translation?->name }}</span></nav><h1>{{ $translation?->name }}</h1><p>{{ $translation?->description }}</p></div></section>
<section class="section"><div class="container"><div class="product-grid">@foreach ($category->products as $product)<x-product-card :product="$product" />@endforeach</div></div></section>
@endsection
