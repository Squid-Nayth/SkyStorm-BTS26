<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    protected $redirectTo = '/home';

    public function showCodeForm(Request $request)
    {
        return view('auth.passwords.code', [
            'email' => (string) $request->query('email', ''),
        ]);
    }

    public function resetWithCode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'digits:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'code.digits' => 'Le code doit contenir 6 chiffres.',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return back()
                ->withErrors(['email' => 'Aucun compte trouve avec cette adresse email.'])
                ->withInput();
        }

        $resetRow = DB::table('password_reset_tokens')->where('email', $validated['email'])->first();

        if (!$resetRow) {
            return back()
                ->withErrors(['code' => 'Aucun code de reinitialisation n\'a ete trouve pour cette adresse email.'])
                ->withInput();
        }

        if (Carbon::parse($resetRow->created_at)->addMinutes(10)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

            return back()
                ->withErrors(['code' => 'Le code a expire. Merci de refaire une demande.'])
                ->withInput();
        }

        if (!Hash::check($validated['code'], $resetRow->token)) {
            return back()
                ->withErrors(['code' => 'Le code de reinitialisation est incorrect.'])
                ->withInput();
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        return redirect()->route('login')->with('status', 'Votre mot de passe a ete modifie avec succes.');
    }
}
