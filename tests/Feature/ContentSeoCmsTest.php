<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\ContentPage;
use App\Models\LegacyUrl;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ContentSeoCmsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_localized_content_page_has_canonical_hreflang_and_webpage_schema(): void
    {
        $html = $this->get('/en/about')->assertOk()->getContent();

        $this->assertStringContainsString('<title>About KabulFit | KabulFit</title>', $html);
        $this->assertStringContainsString('<link rel="canonical" href="http://localhost/en/about">', $html);
        $this->assertStringContainsString('hreflang="fa"', $html);
        $this->assertStringContainsString('hreflang="ps"', $html);
        $this->assertStringContainsString('"@type":"WebPage"', $html);
        $this->assertSame(1, preg_match_all('/<h1(?:\s[^>]*)?>/i', $html));
    }

    public function test_blog_index_and_article_emit_public_editorial_seo(): void
    {
        $this->get('/fa/blog')
            ->assertOk()
            ->assertSee('مجله');

        $html = $this->get('/en/blog/how-to-measure-for-perahan-tunban')
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('"@type":"BlogPosting"', $html);
        $this->assertStringContainsString('How to Measure for a Perahan Tunban', $html);
        $this->assertStringContainsString('hreflang="fa"', $html);
    }

    public function test_unpublished_pages_and_posts_are_not_public(): void
    {
        $page = ContentPage::query()->where('page_key', 'about')->firstOrFail();
        $page->update(['is_published' => false]);

        $this->get('/en/about')->assertNotFound();

        $post = BlogPost::query()->firstOrFail();
        $slug = $post->translations()->where('locale', 'en')->value('slug');
        $post->update(['is_published' => false, 'published_at' => null]);

        $this->get('/en/blog/'.$slug)->assertNotFound();
    }

    public function test_sitemap_index_separates_catalog_and_content_urls(): void
    {
        $index = $this->get('/sitemap.xml')->assertOk()->getContent();
        $this->assertStringContainsString('<sitemapindex', $index);
        $this->assertStringContainsString('http://localhost/sitemaps/catalog.xml', $index);
        $this->assertStringContainsString('http://localhost/sitemaps/content.xml', $index);

        $catalog = $this->get('/sitemaps/catalog.xml')->assertOk()->getContent();
        $this->assertStringContainsString('http://localhost/en/products/', $catalog);
        $this->assertStringNotContainsString('/en/blog/', $catalog);

        $content = $this->get('/sitemaps/content.xml')->assertOk()->getContent();
        $this->assertStringContainsString('http://localhost/en/about', $content);
        $this->assertStringContainsString('http://localhost/en/blog/how-to-measure-for-perahan-tunban', $content);
        $this->assertStringNotContainsString('/admin', $content);
    }

    public function test_verified_legacy_public_routes_redirect_permanently_to_clean_urls(): void
    {
        $this->get('/About')->assertStatus(301)->assertRedirect('/en/about');
        $this->get('/MeasurementGuide')->assertStatus(301)->assertRedirect('/en/measurement-guide');
        $this->get('/Shop')->assertStatus(301)->assertRedirect('/en/shop');

        $entry = LegacyUrl::query()->where('legacy_path', '/ProductDetail')->firstOrFail();
        $this->assertSame('manual_product', $entry->disposition);
        $this->assertNotNull($entry->verified_at);
    }

    public function test_content_manager_can_edit_page_and_action_is_audited(): void
    {
        $manager = $this->userWithRole('catalog-manager');
        $page = ContentPage::query()->where('page_key', 'about')->with('translations')->firstOrFail();

        $translations = $page->translations->mapWithKeys(fn ($translation) => [
            $translation->locale => [
                'title' => $translation->locale === 'en' ? 'About KabulFit Updated' : $translation->title,
                'slug' => $translation->slug,
                'excerpt' => $translation->excerpt,
                'body' => $translation->body,
                'seo_title' => $translation->seo_title,
                'seo_description' => $translation->seo_description,
            ],
        ])->all();

        $this->actingAs($manager)
            ->put('/en/admin/content/pages/'.$page->uuid, [
                'is_published' => '1',
                'translations' => $translations,
            ])
            ->assertRedirect();

        $this->assertSame(
            'About KabulFit Updated',
            $page->fresh('translations')->translations->firstWhere('locale', 'en')->title,
        );

        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $manager->id,
            'action' => 'content.page_updated',
        ]);
    }

    public function test_redirect_inventory_requires_dedicated_permission(): void
    {
        $manager = $this->userWithRole('catalog-manager');
        $admin = $this->userWithRole('administrator');

        $this->actingAs($manager)
            ->get('/en/admin/legacy-urls')
            ->assertForbidden();

        $this->actingAs($admin)
            ->get('/en/admin/legacy-urls')
            ->assertOk();
    }

    public function test_customer_api_auth_does_not_grant_staff_content_access(): void
    {
        $customer = User::factory()->create();
        Sanctum::actingAs($customer);

        $this->get('/en/admin/content')->assertForbidden();
    }

    private function userWithRole(string $slug): User
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::query()->where('slug', $slug)->firstOrFail());

        return $user;
    }
}
