<?php

namespace Database\Seeders;

use App\Models\Note;
use App\Models\User;
use Illuminate\Database\Seeder;

class NoteSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->first();

        if (!$user) {
            return;
        }

        $notes = [
            [
                'titre'   => 'Réunion du lundi',
                'contenu' => 'Préparer la présentation du projet SkyStorm pour lundi matin.',
            ],
            [
                'titre'   => 'Idées de fonctionnalités',
                'contenu' => 'Ajouter un système de tags, une recherche plein texte et un mode sombre.',
            ],
            [
                'titre'   => 'Rappel important',
                'contenu' => 'Rendre le rapport de stage avant le 30 avril.',
            ],
            [
                'titre'   => 'Liste de courses',
                'contenu' => 'Pain, lait, œufs, fromage, fruits.',
            ],
            [
                'titre'   => 'Liens utiles',
                'contenu' => 'Documentation Laravel : https://laravel.com/docs',
            ],
        ];

        foreach ($notes as $data) {
            Note::create([
                'user_id' => $user->id,
                'titre'   => $data['titre'],
                'contenu' => $data['contenu'],
            ]);
        }
    }
}
