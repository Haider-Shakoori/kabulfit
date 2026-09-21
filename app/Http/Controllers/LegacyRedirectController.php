<?php

namespace App\Http\Controllers;

use App\Models\LegacyProductRedirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LegacyRedirectController extends Controller
{
    public function product(Request $request): RedirectResponse
    {
        $legacyKey = trim((string) $request->query('id'));
        abort_if($legacyKey === '', 404);

        $mapping = LegacyProductRedirect::query()
            ->where('legacy_key', $legacyKey)
            ->with('product.translations')
            ->firstOrFail();

        $translation = $mapping->product->translation(config('kabulfit.default_locale'));
        abort_unless($translation, 404);

        return redirect()->route('products.show', [
            'locale' => config('kabulfit.default_locale'),
            'slug' => $translation->slug,
        ], 301);
    }
}
