<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\PostReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialInteractionsFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_follow_and_unfollow_someone(): void
    {
        $user = User::factory()->create();
        $target = User::factory()->create();

        $this->actingAs($user)
            ->post(route('users.follow', $target))
            ->assertSessionHas('status', 'Utilisateur suivi.');

        $this->assertDatabaseHas('followers', [
            'follower_id' => $user->id,
            'following_id' => $target->id,
        ]);

        $this->actingAs($user)
            ->delete(route('users.unfollow', $target))
            ->assertSessionHas('status', 'Utilisateur retiré de vos abonnements.');

        $this->assertDatabaseMissing('followers', [
            'follower_id' => $user->id,
            'following_id' => $target->id,
        ]);
    }

    public function test_user_cannot_follow_self(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('users.follow', $user))
            ->assertSessionHas('status', 'Vous ne pouvez pas vous suivre vous-même.');
    }

    public function test_user_can_like_and_unlike_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)->post(route('posts.likes.store', $post))
            ->assertSessionHas('status', 'Like ajouté.');

        $this->assertDatabaseHas('post_likes', [
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        $this->actingAs($user)->delete(route('posts.likes.destroy', $post))
            ->assertSessionHas('status', 'Like retiré.');

        $this->assertDatabaseMissing('post_likes', [
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }

    public function test_user_can_comment_and_post_owner_can_delete_comment(): void
    {
        $author = User::factory()->create();
        $commenter = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);

        $this->actingAs($commenter)
            ->post(route('posts.comments.store', $post), ['content' => 'Super post'])
            ->assertSessionHas('status', 'Commentaire ajouté.');

        $comment = $post->comments()->first();
        $this->assertNotNull($comment);

        $this->actingAs($author)
            ->delete(route('comments.destroy', $comment))
            ->assertSessionHas('status', 'Commentaire supprimé.');

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_unrelated_user_cannot_delete_comment(): void
    {
        $author = User::factory()->create();
        $commenter = User::factory()->create();
        $stranger = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);
        $comment = $post->comments()->create([
            'user_id' => $commenter->id,
            'content' => 'Commentaire',
        ]);

        $this->actingAs($stranger)
            ->delete(route('comments.destroy', $comment))
            ->assertForbidden();
    }

    public function test_user_can_add_remove_and_publish_favorites(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)
            ->post(route('posts.favorites.store', $post))
            ->assertSessionHas('status', 'Post ajouté aux favoris.');

        $this->assertDatabaseHas('favorite_posts', [
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        $this->actingAs($user)
            ->post(route('favorites.visibility'), ['favorite_posts_public' => '1'])
            ->assertSessionHas('status', 'Visibilité des favoris mise à jour.');

        $this->assertTrue($user->fresh()->favorite_posts_public);

        $this->actingAs($user)
            ->delete(route('posts.favorites.destroy', $post))
            ->assertSessionHas('status', 'Post retiré des favoris.');

        $this->assertDatabaseMissing('favorite_posts', [
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }

    public function test_private_favorites_are_hidden_but_owner_can_see_them(): void
    {
        $user = User::factory()->create(['favorite_posts_public' => false]);
        $other = User::factory()->create();

        $this->get(route('favorites.show', $user))->assertForbidden();
        $this->actingAs($other)->get(route('favorites.show', $user))->assertForbidden();
        $this->actingAs($user)->get(route('favorites.show', $user))->assertOk();
    }

    public function test_user_cannot_have_more_than_fifty_favorites(): void
    {
        $user = User::factory()->create();
        $author = User::factory()->create();

        $existingFavorites = Post::factory()->count(50)->create([
            'user_id' => $author->id,
        ]);

        $user->favoritePosts()->attach($existingFavorites->pluck('id'));

        $extraPost = Post::factory()->create([
            'user_id' => $author->id,
            'content' => 'Post en plus',
        ]);

        $response = $this->actingAs($user)->post(route('posts.favorites.store', $extraPost));

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Vous avez déjà 50 favoris.');
        $this->assertDatabaseMissing('favorite_posts', [
            'user_id' => $user->id,
            'post_id' => $extraPost->id,
        ]);
    }

    public function test_user_can_report_post_only_once_while_pending(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)
            ->post(route('posts.reports.store', $post), ['reason' => 'Spam'])
            ->assertSessionHas('status', 'Signalement envoyé.');

        $this->actingAs($user)
            ->post(route('posts.reports.store', $post), ['reason' => 'Spam encore'])
            ->assertSessionHas('status', 'Vous avez déjà signalé ce post.');

        $this->assertDatabaseCount('post_reports', 1);
    }

    public function test_non_admin_cannot_access_admin_report_screen(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->get(route('admin.reports.index'))->assertForbidden();
    }

    public function test_admin_can_review_a_report(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $reporter = User::factory()->create();
        $author = User::factory()->create();

        $post = Post::factory()->create([
            'user_id' => $author->id,
            'content' => 'Post a moderer',
        ]);

        $report = PostReport::create([
            'user_id' => $reporter->id,
            'post_id' => $post->id,
            'reason' => 'Message insultant',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.reports.update', $report), [
            'status' => 'accepted',
            'admin_note' => 'Vu pendant le test',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('post_reports', [
            'id' => $report->id,
            'status' => 'accepted',
            'admin_note' => 'Vu pendant le test',
        ]);
    }
}
