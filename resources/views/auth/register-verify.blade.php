@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Verification du compte</div>

                <div class="card-body">
                    <p class="text-muted">
                        Un code a ete envoye a votre adresse email. Entrez-le ci-dessous pour finaliser la creation du compte.
                        Le code reste valable 10 minutes.
                    </p>

                    <form method="POST" action="{{ route('register.verify.store') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">Adresse email</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                       name="email" value="{{ old('email', $email) }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="code" class="col-md-4 col-form-label text-md-end">Code de verification</label>

                            <div class="col-md-6">
                                <input id="code" type="text" class="form-control @error('code') is-invalid @enderror"
                                       name="code" value="{{ old('code') }}" required maxlength="6" inputmode="numeric">

                                @error('code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4 d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary">
                                    Verifier et creer le compte
                                </button>
                                <button type="submit" formaction="{{ route('register.verify.resend') }}" class="btn btn-outline-secondary">
                                    Renvoyer le code
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
