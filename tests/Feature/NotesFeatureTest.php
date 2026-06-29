<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotesFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_crud_notes(): void
    {
        $user = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->get(route('notes.index'))->assertOk();
        $this->actingAs($user)->get(route('notes.create'))->assertOk();

        $this->actingAs($user)
            ->post(route('notes.store'), [
                'titre' => 'Ma note',
                'contenu' => 'Contenu de la note',
            ])
            ->assertRedirect(route('notes.index'));

        $this->assertDatabaseHas('notes', [
            'user_id' => $user->id,
            'titre' => 'Ma note',
        ]);

        $this->actingAs($user)
            ->put(route('notes.update', $note), [
                'titre' => 'Titre modifie',
                'contenu' => 'Nouveau contenu',
            ])
            ->assertRedirect(route('notes.index'));

        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'titre' => 'Titre modifie',
        ]);

        $this->actingAs($user)
            ->delete(route('notes.destroy', $note))
            ->assertRedirect(route('notes.index'));

        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }

    public function test_user_cannot_edit_or_delete_someone_else_note(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)->get(route('notes.edit', $note))->assertForbidden();
        $this->actingAs($other)->put(route('notes.update', $note), [
            'titre' => 'Hack',
            'contenu' => 'Hack',
        ])->assertForbidden();
        $this->actingAs($other)->delete(route('notes.destroy', $note))->assertForbidden();
    }
}
