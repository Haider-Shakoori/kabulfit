<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class DefaultLocaleRedirectController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        return redirect('/'.config('kabulfit.default_locale'));
    }
}
