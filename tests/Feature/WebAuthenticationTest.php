<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class WebAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register_and_receives_verification_notification(): void
    {
        Notification::fake();

        $response = $this->post('/fa/register', [
            'name' => 'KabulFit Customer',
            'email' => 'Customer@Example.com',
            'phone' => '+93 700 000 000',
            'preferred_locale' => 'fa',
            'password' => 'Secure1234',
            'password_confirmation' => 'Secure1234',
        ]);

        $response->assertRedirect('/fa/verify-email');
        $this->assertAuthenticated();

        $user = User::query()->where('email', 'customer@example.com')->firstOrFail();
        $this->assertSame('fa', $user->preferred_locale);
        $this->assertNull($user->email_verified_at);
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_active_customer_can_login_and_inactive_customer_cannot(): void
    {
        $active = User::factory()->create([
            'email' => 'active@example.com',
            'password' => Hash::make('Secure1234'),
        ]);

        $this->post('/en/login', [
            'email' => 'ACTIVE@example.com',
            'password' => 'Secure1234',
        ])->assertRedirect('/en/account');

        $this->assertAuthenticatedAs($active);
        $this->post('/en/logout')->assertRedirect('/en');

        User::factory()->create([
            'email' => 'inactive@example.com',
            'password' => Hash::make('Secure1234'),
            'is_active' => false,
        ]);

        $this->post('/en/login', [
            'email' => 'inactive@example.com',
            'password' => 'Secure1234',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_guest_account_redirect_and_private_pages_are_noindex(): void
    {
        $this->get('/ps/account')->assertRedirect('/ps/login');

        $this->get('/en/login')
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex,nofollow">', false);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/en/account')
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex,nofollow">', false);
    }

    public function test_signed_verification_link_marks_email_verified(): void
    {
        $user = User::factory()->create([
            'preferred_locale' => 'en',
            'email_verified_at' => null,
        ]);

        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(10), [
            'locale' => 'en',
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]);

        $this->actingAs($user)
            ->get($url)
            ->assertRedirect('/en/account');

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_password_reset_changes_password_and_revokes_api_tokens(): void
    {
        $user = User::factory()->create([
            'email' => 'reset@example.com',
            'password' => Hash::make('OldPassword1'),
        ]);

        $user->createToken('mobile:test-device');
        $token = Password::broker()->createToken($user);

        $this->post('/en/reset-password', [
            'token' => $token,
            'email' => 'reset@example.com',
            'password' => 'NewPassword2',
            'password_confirmation' => 'NewPassword2',
        ])->assertRedirect('/en/login');

        $user->refresh();
        $this->assertTrue(Hash::check('NewPassword2', $user->password));
        $this->assertCount(0, $user->tokens);
    }

    public function test_forgot_password_response_does_not_disclose_account_existence(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'known@example.com']);

        $known = $this->post('/en/forgot-password', ['email' => 'known@example.com']);
        $unknown = $this->post('/en/forgot-password', ['email' => 'unknown@example.com']);

        $known->assertSessionHas('status');
        $unknown->assertSessionHas('status');
        $this->assertSame($known->getSession()->get('status'), $unknown->getSession()->get('status'));
        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }
}
