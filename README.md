# SkyStorm

Projet Laravel de reseau social realise dans un contexte BTS SIO / SLAM.

## Lancer le projet

Installer les dependances une premiere fois :

```bash
composer install
npm install
php artisan migrate
```

Puis au quotidien :

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Dans un autre terminal :

```bash
npm run dev -- --host 127.0.0.1 --port 5173
```

Application :

- `http://127.0.0.1:8000`

## Lancer les tests

```bash
php artisan test
```

## Email en local

Le projet gere l'envoi des emails pour :

- le code de reinitialisation du mot de passe
- le code de verification lors de l'inscription

Par defaut, le projet est configure en mode `log` dans `.env` :

```env
MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

Dans ce mode, aucun vrai email n'est envoye : Laravel ecrit simplement le contenu dans le fichier de log.

Pour lire les emails generes :

```bash
tail -f storage/logs/laravel.log
```

## Documentation courte

Une documentation plus claire du projet est disponible ici :

- [docs/PROJET.md](/Users/nkombelasseninathanmichel/Documents/GitHub/SkyStorm-BTS26/docs/PROJET.md)
