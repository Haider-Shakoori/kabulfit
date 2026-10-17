<?php

namespace Tests\Feature;

use Tests\TestCase;

class Base44CatalogMediaTest extends TestCase
{
    private string $fixturePath;

    protected function setUp(): void
    {
        parent::setUp();

        $folder = public_path('images/kabulfit-base44/catalog');
        if (! is_dir($folder)) {
            mkdir($folder, 0755, true);
        }

        $this->fixturePath = $folder.'/'.str_repeat('a', 20).'.png';
        file_put_contents($this->fixturePath, base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/Jd8AAAAASUVORK5CYII='
        ));
    }

    protected function tearDown(): void
    {
        @unlink($this->fixturePath);
        parent::tearDown();
    }

    public function test_serves_only_archived_original_catalog_images_with_cache_headers(): void
    {
        $this->get('/images/kabulfit-base44/catalog/'.str_repeat('a', 20).'.png')
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png')
            ->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_uses_release_archive_if_host_overrides_public_path(): void
    {
        $originalPublicPath = public_path();

        try {
            // A cPanel alias may point public_path() away from the release archive.
            app()->usePublicPath(sys_get_temp_dir().'/kabulfit-unused-public-path');

            $this->get('/images/kabulfit-base44/catalog/'.str_repeat('a', 20).'.png')
                ->assertOk()
                ->assertHeader('Content-Type', 'image/png');
        } finally {
            app()->usePublicPath($originalPublicPath);
        }
    }

    public function test_missing_and_unapproved_file_names_are_not_served(): void
    {
        $this->get('/images/kabulfit-base44/catalog/'.str_repeat('b', 20).'.webp')
            ->assertNotFound();

        $this->get('/images/kabulfit-base44/catalog/not-a-content-hash.png')
            ->assertNotFound();
    }
}
