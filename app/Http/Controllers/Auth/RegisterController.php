<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RegistrationVerificationCodeMail;
use App\Models\PendingRegistration;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.unique' => 'Cette adresse email est deja utilisee.',
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $this->validator($request->all())->validate();

        $minutes = 10;
        $code = (string) random_int(100000, 999999);

        PendingRegistration::updateOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'password' => Hash::make($validated['password']),
                'verification_code' => Hash::make($code),
                'expires_at' => now()->addMinutes($minutes),
            ]
        );

        Mail::to($validated['email'])->send(
            new RegistrationVerificationCodeMail(
                $validated['name'],
                $validated['email'],
                $code,
                $minutes
            )
        );

        return redirect()
            ->route('register.verify.form', ['email' => $validated['email']])
            ->with('status', 'Un code de verification a ete envoye par email.');
    }

    public function showVerificationForm(Request $request)
    {
        return view('auth.register-verify', [
            'email' => (string) $request->query('email', ''),
        ]);
    }

    public function verifyCode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'digits:6'],
        ], [
            'code.digits' => 'Le code doit contenir 6 chiffres.',
        ]);

        $pending = PendingRegistration::where('email', $validated['email'])->first();

        if (!$pending) {
            return back()
                ->withErrors(['email' => 'Aucune inscription en attente pour cette adresse email.'])
                ->withInput();
        }

        if ($pending->expires_at->isPast()) {
            $pending->delete();

            return back()
                ->withErrors(['code' => 'Le code a expire. Merci de recommencer l\'inscription.'])
                ->withInput();
        }

        if (!Hash::check($validated['code'], $pending->verification_code)) {
            return back()
                ->withErrors(['code' => 'Le code de verification est incorrect.'])
                ->withInput();
        }

        $user = User::create([
            'name' => $pending->name,
            'email' => $pending->email,
            'password' => $pending->password,
            'is_admin' => User::count() === 0,
        ]);

        $pending->delete();

        event(new Registered($user));
        Auth::login($user);

        return redirect($this->redirectTo)->with('status', 'Compte cree avec succes.');
    }

    public function resendCode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $pending = PendingRegistration::where('email', $validated['email'])->first();

        if (!$pending) {
            return back()
                ->withErrors(['email' => 'Aucune inscription en attente pour cette adresse email.'])
                ->withInput();
        }

        $minutes = 10;
        $code = (string) random_int(100000, 999999);

        $pending->update([
            'verification_code' => Hash::make($code),
            'expires_at' => now()->addMinutes($minutes),
        ]);

        Mail::to($pending->email)->send(
            new RegistrationVerificationCodeMail(
                $pending->name,
                $pending->email,
                $code,
                $minutes
            )
        );

        return redirect()
            ->route('register.verify.form', ['email' => $pending->email])
            ->with('status', 'Un nouveau code de verification a ete envoye.');
    }
}
