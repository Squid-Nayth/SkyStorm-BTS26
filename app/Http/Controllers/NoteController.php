<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Affiche toutes les notes de l'utilisateur connecté
    public function index()
    {
        $notes = auth()->user()->notes()->latest()->get();
        return view('notes.index', compact('notes'));
    }

    // Affiche le formulaire de création
    public function create()
    {
        return view('notes.create');
    }

    // Enregistre une nouvelle note
    public function store(Request $request)
    {
        $request->validate([
            'titre'   => 'required|max:255',
            'contenu' => 'required',
        ]);

        auth()->user()->notes()->create($request->only('titre', 'contenu'));

        return redirect()->route('notes.index')->with('status', 'Note créée avec succès !');
    }

    // Affiche le formulaire d'édition
    public function edit(Note $note)
    {
        if ($note->user_id !== auth()->id()) {
            abort(403);
        }
        return view('notes.edit', compact('note'));
    }

    // Met à jour une note existante
    public function update(Request $request, Note $note)
    {
        if ($note->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'titre'   => 'required|max:255',
            'contenu' => 'required',
        ]);

        $note->update($request->only('titre', 'contenu'));

        return redirect()->route('notes.index')->with('status', 'Note modifiée avec succès !');
    }

    // Supprime une note
    public function destroy(Note $note)
    {
        if ($note->user_id !== auth()->id()) {
            abort(403);
        }
        $note->delete();

        return redirect()->route('notes.index')->with('status', 'Note supprimée.');
    }
}
