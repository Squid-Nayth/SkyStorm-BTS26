@extends('layouts.app')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div class="d-flex align-items-center gap-3 mb-4">
        @include('users._avatar', ['user' => $user, 'size' => 72])
        <div>
            <h2 class="mb-1 ss-icon-label"><i class="bi bi-pencil-square"></i>Modifier mon profil</h2>
            <p class="text-muted mb-0">Informations personnelles, photo, bio et mot de passe.</p>
        </div>
    </div>

    <div class="ss-card p-4">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nom</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ville / localisation</label>
                    <input type="text" name="location" value="{{ old('location', $user->location) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Site web</label>
                    <input type="url" name="website" value="{{ old('website', $user->website) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Date de naissance</label>
                    <input type="date" name="birthdate" value="{{ old('birthdate', optional($user->birthdate)->format('Y-m-d')) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Photo de profil</label>
                    <input type="file" name="avatar" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                    @if($user->avatar_path)
                        <div class="form-check mt-2">
                            <input type="checkbox" name="remove_avatar" value="1" class="form-check-input" id="remove_avatar">
                            <label for="remove_avatar" class="form-check-label">Supprimer la photo actuelle</label>
                        </div>
                    @endif
                </div>
                <div class="col-12">
                    <label class="form-label">Bio</label>
                    <textarea name="bio" rows="4" maxlength="500" class="form-control">{{ old('bio', $user->bio) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nouveau mot de passe</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirmation du mot de passe</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary ss-icon-label"><i class="bi bi-save"></i>Enregistrer</button>
                <a href="{{ route('users.show', $user) }}" class="btn btn-outline-secondary ss-icon-label"><i class="bi bi-person-vcard"></i>Voir mon profil</a>
            </div>
        </form>
    </div>
</div>
@endsection
