<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\Post;
use App\Models\PostReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExploreAndMessagingFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_search_explore_page(): void
    {
        $author = User::factory()->create(['name' => 'Claire']);
        Post::factory()->create([
            'user_id' => $author->id,
            'content' => 'Laravel pour BTS',
        ]);
        Post::factory()->create([
            'user_id' => $author->id,
            'content' => 'Autre contenu',
        ]);

        $response = $this->get(route('explore', ['q' => 'Laravel']));

        $response->assertOk();
        $response->assertSee('Laravel pour BTS');
        $response->assertDontSee('Autre contenu');
    }

    public function test_guest_explore_hides_posts_with_pending_or_accepted_reports(): void
    {
        $author = User::factory()->create();

        $visiblePost = Post::factory()->create([
            'user_id' => $author->id,
            'content' => 'Post visible',
        ]);

        $pendingPost = Post::factory()->create([
            'user_id' => $author->id,
            'content' => 'Post masque',
        ]);

        $acceptedPost = Post::factory()->create([
            'user_id' => $author->id,
            'content' => 'Post accepte donc masque',
        ]);

        PostReport::create([
            'user_id' => $author->id,
            'post_id' => $pendingPost->id,
            'reason' => 'Contenu suspect',
            'status' => 'pending',
        ]);

        PostReport::create([
            'user_id' => $author->id,
            'post_id' => $acceptedPost->id,
            'reason' => 'Spam',
            'status' => 'accepted',
        ]);

        $response = $this->get(route('explore'));

        $response->assertOk();
        $response->assertSee('Post visible');
        $response->assertDontSee('Post masque');
        $response->assertDontSee('Post accepte donc masque');
    }

    public function test_public_favorites_hide_reported_posts_for_guests(): void
    {
        $owner = User::factory()->create(['favorite_posts_public' => true]);
        $author = User::factory()->create();

        $safePost = Post::factory()->create(['user_id' => $author->id, 'content' => 'Favori visible']);
        $reportedPost = Post::factory()->create(['user_id' => $author->id, 'content' => 'Favori masque']);

        $owner->favoritePosts()->attach([$safePost->id, $reportedPost->id]);

        PostReport::create([
            'user_id' => $owner->id,
            'post_id' => $reportedPost->id,
            'reason' => 'Probleme',
            'status' => 'pending',
        ]);

        $response = $this->get(route('favorites.show', $owner));

        $response->assertOk();
        $response->assertSee('Favori visible');
        $response->assertDontSee('Favori masque');
    }

    public function test_user_can_open_messages_index_and_send_private_message(): void
    {
        $sender = User::factory()->create(['name' => 'Sender']);
        $recipient = User::factory()->create(['name' => 'Recipient']);

        $this->actingAs($sender)->get(route('messages.index'))
            ->assertOk()
            ->assertSee('Recipient');

        $response = $this->actingAs($sender)->post(route('messages.store', $recipient), [
            'content' => 'Bonjour, ceci est un test.',
        ]);

        $response->assertRedirect(route('messages.show', $recipient));

        $this->assertDatabaseHas('messages', [
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'content' => 'Bonjour, ceci est un test.',
        ]);
    }

    public function test_opening_conversation_marks_received_messages_as_read(): void
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        Message::create([
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'content' => 'Message non lu',
        ]);

        $this->assertSame(1, $recipient->unreadMessagesCount());

        $this->actingAs($recipient)
            ->get(route('messages.show', $sender))
            ->assertOk();

        $this->assertSame(0, $recipient->fresh()->unreadMessagesCount());
        $this->assertDatabaseMissing('messages', [
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'read_at' => null,
        ]);
    }

    public function test_user_cannot_message_self(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('messages.show', $user))->assertForbidden();
        $this->actingAs($user)->post(route('messages.store', $user), [
            'content' => 'Auto message',
        ])->assertForbidden();
    }
}
