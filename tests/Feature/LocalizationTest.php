<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_root_redirects_to_default_locale(): void
    {
        $this->get('/')->assertRedirect('/en');
    }

    public function test_english_is_ltr(): void
    {
        $this->get('/en')->assertOk()->assertSee('<html lang="en" dir="ltr">', false);
    }

    public function test_dari_and_pashto_are_rtl(): void
    {
        $this->get('/fa')->assertOk()->assertSee('<html lang="fa" dir="rtl">', false);
        $this->get('/ps')->assertOk()->assertSee('<html lang="ps" dir="rtl">', false);
    }

    public function test_unsupported_locale_returns_404(): void
    {
        $this->get('/de')->assertNotFound();
    }
}
