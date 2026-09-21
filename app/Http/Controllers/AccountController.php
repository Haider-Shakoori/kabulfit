<?php

namespace App\Http\Controllers;

use App\Support\Seo\PrivatePageSeo;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __invoke(Request $request, string $locale): View
    {
        $user = $request->user()->load([
            'addresses' => fn ($query) => $query->orderByDesc('is_default')->orderBy('id'),
            'devices' => fn ($query) => $query->orderByDesc('last_seen_at'),
        ]);

        $seo = PrivatePageSeo::make(__('account.title'), route('account', ['locale' => $locale]));

        return view('account.index', compact('user', 'seo'));
    }
}
