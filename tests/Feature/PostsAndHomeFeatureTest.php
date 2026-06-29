<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostsAndHomeFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_post_management_pages(): void
    {
        $post = Post::factory()->create();

        $this->get(route('posts.index'))->assertRedirect(route('login'));
        $this->get(route('posts.create'))->assertRedirect(route('login'));
        $this->get(route('posts.edit', $post))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_update_and_delete_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id, 'content' => 'Old content']);

        $this->actingAs($user)
            ->post(route('posts.store'), ['content' => 'Nouveau post'])
            ->assertSessionHas('status', 'Post publié !');

        $this->assertDatabaseHas('posts', [
            'user_id' => $user->id,
            'content' => 'Nouveau post',
        ]);

        $this->actingAs($user)
            ->put(route('posts.update', $post), ['content' => 'Updated content'])
            ->assertRedirect(route('posts.index'));

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'content' => 'Updated content',
        ]);

        $this->actingAs($user)
            ->delete(route('posts.destroy', $post))
            ->assertSessionHas('status', 'Post supprimé.');

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_user_cannot_update_or_delete_someone_else_post(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->put(route('posts.update', $post), ['content' => 'Hack'])
            ->assertForbidden();

        $this->actingAs($other)
            ->delete(route('posts.destroy', $post))
            ->assertForbidden();
    }

    public function test_home_feed_shows_followed_posts_and_statistics(): void
    {
        $user = User::factory()->create(['name' => 'Main User']);
        $followed = User::factory()->create(['name' => 'Followed User']);
        $other = User::factory()->create(['name' => 'Other User']);
        $follower = User::factory()->create();

        $user->following()->attach($followed->id);
        $follower->following()->attach($user->id);

        $ownPost = Post::factory()->create(['user_id' => $user->id, 'content' => 'Mon post']);
        $followedPost = Post::factory()->create(['user_id' => $followed->id, 'content' => 'Post suivi']);
        $otherPost = Post::factory()->create(['user_id' => $other->id, 'content' => 'Post autre']);

        $followedPost->likes()->create(['user_id' => $other->id]);
        $followedPost->likes()->create(['user_id' => $follower->id]);
        $ownPost->likes()->create(['user_id' => $followed->id]);

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertOk();
        $response->assertSee('Mon post');
        $response->assertSee('Post suivi');
        $response->assertDontSee('Post autre');
        $response->assertSee('Abonnés');
        $response->assertSee('1');
        $response->assertSee('Favoris enregistrés');
    }
}
