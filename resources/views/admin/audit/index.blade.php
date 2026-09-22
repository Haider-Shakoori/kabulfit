@extends('layouts.app')
@section('content')
<section class="page-hero"><div class="container"><p class="eyebrow">Governance</p><h1>Immutable audit log</h1></div></section>
<section class="section"><div class="container account-grid">@include('admin._nav')
<div class="account-main">@foreach($logs as $log)<article class="account-panel"><h2>{{ $log->action }}</h2><p>{{ $log->created_at }} · {{ $log->actor?->email ?? 'system' }} · {{ $log->uuid }}</p>@if($log->metadata)<pre>{{ json_encode($log->metadata, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>@endif</article>@endforeach {{ $logs->links() }}</div></div></section>
@endsection
