<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_members_directory_can_filter_users(): void
    {
        $alice = User::factory()->create(['name' => 'Alice Martin', 'location' => 'Paris']);
        $bob = User::factory()->create(['name' => 'Bob Dupont', 'location' => 'Lyon']);

        $response = $this->get(route('members.index', ['q' => 'Paris']));

        $response->assertOk();
        $response->assertSee('Alice Martin');
        $response->assertDontSee('Bob Dupont');
    }

    public function test_profile_page_displays_posts_and_public_favorites_link(): void
    {
        $user = User::factory()->create([
            'favorite_posts_public' => true,
            'bio' => 'Bio de profil',
            'location' => 'Paris',
        ]);

        Post::factory()->create([
            'user_id' => $user->id,
            'content' => 'Post visible sur le profil',
        ]);

        $response = $this->get(route('users.show', $user));

        $response->assertOk();
        $response->assertSee('Bio de profil');
        $response->assertSee('Post visible sur le profil');
        $response->assertSee('Voir ses favoris publics');
    }

    public function test_followers_and_following_pages_are_accessible(): void
    {
        $user = User::factory()->create(['name' => 'Owner']);
        $follower = User::factory()->create(['name' => 'Follower']);
        $following = User::factory()->create(['name' => 'Following']);

        $follower->following()->attach($user->id);
        $user->following()->attach($following->id);

        $this->get(route('users.followers', $user))
            ->assertOk()
            ->assertSee('Follower');

        $this->get(route('users.following', $user))
            ->assertOk()
            ->assertSee('Following');
    }

    public function test_user_can_update_profile_information_and_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('oldpassword'),
        ]);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'New Name',
            'email' => 'new@example.com',
            'bio' => 'Nouvelle bio',
            'location' => 'Marseille',
            'website' => 'https://example.com',
            'birthdate' => '2000-01-01',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('profile.edit'));

        $user->refresh();
        $this->assertSame('New Name', $user->name);
        $this->assertSame('new@example.com', $user->email);
        $this->assertSame('Nouvelle bio', $user->bio);
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    public function test_user_can_upload_and_remove_avatar(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $uploadResponse = $this->actingAs($user)->put(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => UploadedFile::fake()->image('avatar.png'),
        ]);

        $uploadResponse->assertRedirect(route('profile.edit'));

        $user->refresh();
        $this->assertNotNull($user->avatar_path);
        Storage::disk('public')->assertExists($user->avatar_path);

        $path = $user->avatar_path;

        $removeResponse = $this->actingAs($user)->put(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'remove_avatar' => '1',
        ]);

        $removeResponse->assertRedirect(route('profile.edit'));

        $user->refresh();
        $this->assertNull($user->avatar_path);
        Storage::disk('public')->assertMissing($path);
    }
}
