<?php

namespace Tests\Feature;

use App\Mail\PasswordResetCodeMail;
use App\Mail\RegistrationVerificationCodeMail;
use App\Models\PendingRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_email_code_and_first_user_becomes_admin(): void
    {
        Mail::fake();

        $response = $this->post(route('register'), [
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('register.verify.form', ['email' => 'alice@example.com']));
        $this->assertGuest();
        $this->assertDatabaseHas('pending_registrations', [
            'email' => 'alice@example.com',
            'name' => 'Alice',
        ]);

        $sentCode = null;

        Mail::assertSent(RegistrationVerificationCodeMail::class, function ($mail) use (&$sentCode) {
            $sentCode = $mail->code;

            return $mail->email === 'alice@example.com';
        });

        $verifyResponse = $this->post(route('register.verify.store'), [
            'email' => 'alice@example.com',
            'code' => $sentCode,
        ]);

        $verifyResponse->assertRedirect('/home');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'alice@example.com',
            'is_admin' => true,
        ]);
        $this->assertDatabaseMissing('pending_registrations', [
            'email' => 'alice@example.com',
        ]);
    }

    public function test_user_can_login_and_logout(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $loginResponse = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $loginResponse->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);

        $logoutResponse = $this->post(route('logout'));

        $logoutResponse->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_password_reset_request_fails_when_email_is_unknown(): void
    {
        Mail::fake();

        $response = $this->from(route('password.request'))
            ->post(route('password.email'), [
                'email' => 'unknown@example.com',
            ]);

        $response->assertRedirect(route('password.request'));
        $response->assertSessionHasErrors([
            'email' => 'Aucun compte trouve avec cette adresse email.',
        ]);

        Mail::assertNothingSent();
    }

    public function test_user_can_reset_password_with_email_code(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'alice@example.com',
            'password' => bcrypt('oldpassword'),
        ]);

        $requestResponse = $this->post(route('password.email'), [
            'email' => $user->email,
        ]);

        $requestResponse->assertRedirect(route('password.code.form', ['email' => $user->email]));

        $sentCode = null;

        Mail::assertSent(PasswordResetCodeMail::class, function ($mail) use (&$sentCode, $user) {
            $sentCode = $mail->code;

            return $mail->user->is($user);
        });

        $resetResponse = $this->post(route('password.code.update'), [
            'email' => $user->email,
            'code' => $sentCode,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $resetResponse->assertRedirect(route('login'));

        $user->refresh();

        $this->assertTrue(Hash::check('newpassword123', $user->password));
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $user->email,
        ]);
    }
}
