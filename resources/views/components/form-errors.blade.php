@if ($errors->any())
    <div class="form-alert form-alert-error" role="alert">
        <strong>{{ __('auth.please_fix') }}</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('status'))
    <div class="form-alert form-alert-success" role="status">{{ session('status') }}</div>
@endif
