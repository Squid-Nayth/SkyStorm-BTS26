<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetCodeMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email n\'est pas valide.',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return back()
                ->withErrors(['email' => 'Aucun compte trouve avec cette adresse email.'])
                ->withInput();
        }

        $minutes = 10;
        $code = (string) random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($code),
                'created_at' => now(),
            ]
        );

        Mail::to($user->email)->send(new PasswordResetCodeMail($user, $code, $minutes));

        return redirect()
            ->route('password.code.form', ['email' => $user->email])
            ->with('status', 'Un code de reinitialisation a ete envoye par email.');
    }
}
