@extends('layouts.app')

@section('content')
@include('catalog._base44-results', [
    'action' => route('shop', ['locale' => app()->getLocale()]),
    'lockedCategory' => null,
    'lockedCollection' => null,
    'pageTitle' => __('site.shop'),
    'pageDescription' => __('site.shop_description'),
    'pageBadge' => null,
])
@endsection
