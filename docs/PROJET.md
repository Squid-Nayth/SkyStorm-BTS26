# Documentation SkyStorm

## 1. Vue d'ensemble

SkyStorm est un mini reseau social developpe en **Laravel 12** avec une architecture **MVC** :

- **Model** : representation des donnees et relations Eloquent
- **View** : pages Blade affichees dans le navigateur
- **Controller** : logique metier, validation, redirections

Le projet a ete pense pour rester lisible et explicable dans un contexte scolaire.

## 2. Stack technique

- PHP 8.2+
- Laravel 12
- Blade
- Eloquent ORM
- SQLite
- Vite
- Bootstrap + Bootstrap Icons (CDN)

## 3. Structure importante

- `app/Models` : models (`User`, `Post`, `Comment`, `Message`, etc.)
- `app/Http/Controllers` : logique des pages et actions
- `resources/views` : vues Blade
- `routes/web.php` : routes web
- `database/migrations` : structure de la base
- `tests/Feature` et `tests/Unit` : tests

## 4. Fonctionnalites principales

### Authentification

- connexion / deconnexion
- inscription avec **code de verification email**
- mot de passe oublie avec **code email**

### Reseau social

- publier un post
- modifier / supprimer son post
- liker / retirer un like
- commenter
- suivre / ne plus suivre
- annuaire des membres
- page profil
- favoris publics ou prives
- messagerie privee
- signalement de publications
- interface admin de gestion des signalements

## 5. Base de donnees

Tables principales :

- `users`
- `posts`
- `notes`
- `followers`
- `post_likes`
- `favorite_posts`
- `comments`
- `post_reports`
- `messages`
- `pending_registrations`
- `password_reset_tokens`

## 6. Lancement du projet

Premiere installation :

```bash
composer install
npm install
php artisan migrate
```

Lancement :

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Puis dans un autre terminal :

```bash
npm run dev -- --host 127.0.0.1 --port 5173
```

Adresse locale :

- `http://127.0.0.1:8000`

## 7. Tests

Lancer tous les tests :

```bash
php artisan test
```

Etat actuel verifie :

- **37 tests passes**
- **152 assertions**

## 8. Emails en mode simple

Le projet est laisse volontairement en mode simple pour les emails.

Configuration actuelle dans `.env` :

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

Dans ce mode :

- aucun vrai email n'est envoye
- le contenu des emails est ecrit dans le log Laravel

Pour consulter les emails :

```bash
tail -f storage/logs/laravel.log
```

## 9. Routes utiles

- `/login`
- `/register`
- `/register/verify`
- `/password/reset`
- `/password/code`
- `/home`
- `/explore`
- `/members`
- `/favorites`
- `/messages`
- `/users/{user}`

## 10. Fichiers utiles a connaitre

- [routes/web.php](/Users/nkombelasseninathanmichel/Documents/GitHub/SkyStorm-BTS26/routes/web.php)
- [app/Http/Controllers/Auth/RegisterController.php](/Users/nkombelasseninathanmichel/Documents/GitHub/SkyStorm-BTS26/app/Http/Controllers/Auth/RegisterController.php)
- [app/Http/Controllers/Auth/ForgotPasswordController.php](/Users/nkombelasseninathanmichel/Documents/GitHub/SkyStorm-BTS26/app/Http/Controllers/Auth/ForgotPasswordController.php)
- [app/Http/Controllers/Auth/ResetPasswordController.php](/Users/nkombelasseninathanmichel/Documents/GitHub/SkyStorm-BTS26/app/Http/Controllers/Auth/ResetPasswordController.php)
- [app/Http/Controllers/ProfileController.php](/Users/nkombelasseninathanmichel/Documents/GitHub/SkyStorm-BTS26/app/Http/Controllers/ProfileController.php)
- [app/Http/Controllers/MessageController.php](/Users/nkombelasseninathanmichel/Documents/GitHub/SkyStorm-BTS26/app/Http/Controllers/MessageController.php)
- [app/Http/Controllers/FavoriteController.php](/Users/nkombelasseninathanmichel/Documents/GitHub/SkyStorm-BTS26/app/Http/Controllers/FavoriteController.php)

## 11. Resume simple

SkyStorm est maintenant un projet Laravel de reseau social avec :

- UI plus vivante
- systeme social complet niveau etudiant
- verification par email pour l'inscription
- reinitialisation du mot de passe par code
- tests automatiques

Le projet reste volontairement simple pour pouvoir etre explique facilement a un examinateur.
