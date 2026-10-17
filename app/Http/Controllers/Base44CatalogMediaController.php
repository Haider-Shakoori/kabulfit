<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

class Base44CatalogMediaController extends Controller
{
    public function __invoke(string $filename): BinaryFileResponse
    {
        // Only archived, content-addressed public Base44 photos may be served.
        // The route is a fallback for cPanel servers that rewrite new static files.
        abort_unless(preg_match('/\A[a-f0-9]{20}\.(?:webp|png|jpe?g)\z/D', $filename) === 1, 404);

        $relativePath = 'images/kabulfit-base44/catalog/'.$filename;
        $path = public_path($relativePath);

        // Some cPanel releases serve the app through an alternate web document root.
        // The imported, immutable archive is kept within this Laravel release.
        if (! is_file($path)) {
            $path = base_path('public/'.$relativePath);
        }

        abort_unless(is_file($path), 404);

        return response()->file($path, [
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
