@extends('layouts.app')

@section('content')
<section class="page-hero">
    <div class="container">
        <h1>{{ $translation->title }}</h1>
        @if($translation->excerpt)<p>{{ $translation->excerpt }}</p>@endif
    </div>
</section>

<section class="section">
    <div class="container">
        <article class="account-panel">
            @foreach(preg_split('/\R{2,}/u', trim($translation->body)) as $paragraph)
                <p>{{ $paragraph }}</p>
            @endforeach
        </article>
    </div>
</section>
@endsection
