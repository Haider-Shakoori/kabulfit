<?php

namespace Tests\Feature;

use App\Models\NewsletterSubscriber;
use App\Services\Payments\PayPalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class Base44OptionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_contact_form_persists_message_even_when_email_delivery_is_external(): void
    {
        Mail::fake();

        $this->post('/en/contact', [
            'name' => 'KabulFit Customer',
            'email' => 'customer@example.com',
            'subject' => 'Sizing question',
            'message' => 'Please help me confirm the correct custom size.',
            'website' => '',
        ])->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'customer@example.com',
            'subject' => 'Sizing question',
            'status' => 'new',
        ]);
    }

    public function test_newsletter_subscription_can_be_cancelled_with_signed_link(): void
    {
        Mail::fake();

        $this->post('/fa/newsletter/subscribe', [
            'email' => 'subscriber@example.com',
            'website' => '',
        ])->assertRedirect()
            ->assertSessionHas('newsletter_status');

        $subscriber = NewsletterSubscriber::query()
            ->where('email', 'subscriber@example.com')
            ->firstOrFail();

        $this->assertTrue($subscriber->is_active);
        $this->assertSame('fa', $subscriber->locale);

        $url = URL::temporarySignedRoute(
            'newsletter.unsubscribe',
            now()->addMinute(),
            ['locale' => 'fa', 'subscriber' => $subscriber],
        );

        $this->get($url)
            ->assertRedirect(route('home', ['locale' => 'fa']))
            ->assertSessionHas('newsletter_status');

        $this->assertFalse($subscriber->fresh()->is_active);
        $this->assertNotNull($subscriber->fresh()->unsubscribed_at);
    }

    public function test_paypal_is_only_enabled_for_explicitly_configured_currencies(): void
    {
        config([
            'services.paypal.client_id' => 'client-test',
            'services.paypal.secret' => 'secret-test',
            'services.paypal.supported_currencies' => ['USD', 'EUR'],
        ]);

        $paypal = app(PayPalService::class);

        $this->assertTrue($paypal->enabledForCurrency('USD'));
        $this->assertTrue($paypal->enabledForCurrency('eur'));
        $this->assertFalse($paypal->enabledForCurrency('AFN'));
    }
}
