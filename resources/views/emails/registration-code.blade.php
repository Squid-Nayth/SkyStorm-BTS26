<div style="font-family: Arial, sans-serif; color: #1f2937;">
    <h2>Verification de votre compte</h2>

    <p>Bonjour {{ $name }},</p>

    <p>Voici votre code de verification :</p>

    <p style="font-size: 28px; font-weight: bold; letter-spacing: 4px;">
        {{ $code }}
    </p>

    <p>Ce code est valable pendant {{ $minutes }} minutes.</p>

    <p>Entrez ce code dans l'application pour finaliser la creation du compte.</p>
</div>
