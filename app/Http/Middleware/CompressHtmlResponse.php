<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CompressHtmlResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if (! str_contains((string) $request->header('Accept-Encoding'), 'gzip')
            || $request->isMethod('HEAD')
            || $response instanceof BinaryFileResponse
            || $response instanceof StreamedResponse
            || $response->headers->has('Content-Encoding')
            || ! str_starts_with((string) $response->headers->get('Content-Type'), 'text/html')
            || $response->getStatusCode() !== 200) {
            return $response;
        }

        $content = $response->getContent();

        if (! is_string($content) || strlen($content) < 1024) {
            return $response;
        }

        $compressed = gzencode($content, 6);

        if ($compressed === false || strlen($compressed) >= strlen($content)) {
            return $response;
        }

        $response->setContent($compressed);
        $response->headers->set('Content-Encoding', 'gzip');
        $response->headers->set('Content-Length', (string) strlen($compressed));
        $response->headers->set('Vary', 'Accept-Encoding', false);

        return $response;
    }
}
