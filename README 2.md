<p align="center"><img src="https://raw.githubusercontent.com/Max13/SkyStorm/master/resources/img/logo.png" width="200" alt="SkyStorm Logo"></p>

# SkyStorm

SkyStorm est une réplique allégée de Twitter/X développée en Laravel 12 dans un contexte pédagogique pour la préparation à l’épreuve E6 du BTS SIO option SLAM. Le projet est construit en direct avec la classe afin d’illustrer un cycle complet de développement : conception, modélisation, implémentation et déploiement.

## Objectifs pédagogiques

* Comprendre l’architecture MVC de Laravel
* Manipuler l’authentification avec Laravel UI (Bootstrap)
* Concevoir un MCD et le traduire en base de données relationnelle
* Gérer les relations n-n et 1-n avec Eloquent
* Implémenter des fonctionnalités sociales (posts, likes, abonnements)
* Structurer un projet Git professionnel

## Stack technique

* PHP 8.x
* Laravel 12
* Laravel UI (Bootstrap)
* MySQL / MariaDB
* Eloquent ORM
* Blade

## Fonctionnalités principales

* Inscription / connexion utilisateur
* Création, modification et suppression de posts
* Système de suivi entre utilisateurs
* Système de likes sur les posts
* Fil d’actualité simplifié

## Modélisation des données

Le modèle de données repose sur deux entités principales : `users` et `posts`, reliées par des relations sociales.

### MCD (Mocodo)

```mocodo
FOLLOW, 0N Users, 0N Users
Users: id_user, name, email, password, created_at
LIKE, 0N Users, 0N Posts

:
POST, 0N Users, 11 Posts
Posts: id_post, content, created_at
```

## Installation

```bash
git clone https://github.com/Max13/SkyStorm.git
cd SkyStorm
composer run-script setup
```

Effectuer les configurations (base de données, URL, nom de l'app, etc…) dans le fichier `.env`, puis exécuter :

```bash
php artisan migrate
php artisan serve
```

## Structure du projet

* `app/Models` : modèles Eloquent
* `app/Http/Controllers` : logique métier
* `resources/views` : templates Blade
* `database/migrations` : schéma de base de données
* `routes/web.php` : routes principales

## Licence

Projet pédagogique. Utilisation libre dans un cadre scolaire ou de formation.
