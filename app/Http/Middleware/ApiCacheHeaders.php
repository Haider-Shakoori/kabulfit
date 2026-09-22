<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiCacheHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if (! in_array($request->method(), ['GET', 'HEAD'], true)) {
            $response->headers->set('Cache-Control', 'no-store');

            return $response;
        }

        if ($response->getStatusCode() !== 200) {
            return $response;
        }

        $private = filled($request->bearerToken()) || $request->user() !== null;
        $response->headers->set(
            'Cache-Control',
            $private
                ? 'private, no-cache'
                : 'public, max-age=60, stale-while-revalidate=300',
        );
        $response->headers->set('Vary', 'Accept, Accept-Language, Authorization');

        $content = $response->getContent();

        if (! is_string($content)) {
            return $response;
        }

        $etag = '"'.sha1($content).'"';
        $response->headers->set('ETag', $etag);

        if (trim((string) $request->headers->get('If-None-Match')) === $etag) {
            $response->setNotModified();
        }

        return $response;
    }
}
