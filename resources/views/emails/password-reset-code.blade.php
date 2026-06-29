<div style="font-family: Arial, sans-serif; color: #1f2937;">
    <h2>Reinitialisation du mot de passe</h2>

    <p>Bonjour {{ $user->name }},</p>

    <p>Voici votre code de reinitialisation :</p>

    <p style="font-size: 28px; font-weight: bold; letter-spacing: 4px;">
        {{ $code }}
    </p>

    <p>Ce code est valable pendant {{ $minutes }} minutes.</p>

    <p>Si vous n'etes pas a l'origine de cette demande, vous pouvez ignorer cet email.</p>
</div>
