<?php

namespace App\Http\Controllers;

use App\Models\LegacyUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LegacyPageRedirectController extends Controller
{
    public function __invoke(Request $request, string $legacy): RedirectResponse|Response
    {
        $path = '/'.$legacy;

        $entry = LegacyUrl::query()
            ->where('legacy_path', $path)
            ->where('is_active', true)
            ->where('query_key', '')
            ->where('query_value', '')
            ->firstOrFail();

        if ($entry->disposition === 'redirect' && filled($entry->target_path)) {
            return redirect($entry->target_path, 301);
        }

        abort_if($entry->disposition === 'private', 404);
        abort(410);
    }
}
