<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Models\PostReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_visible_for_visitors_scope_hides_pending_and_accepted_reports(): void
    {
        $author = User::factory()->create();

        $visiblePost = Post::factory()->create(['user_id' => $author->id]);
        $pendingPost = Post::factory()->create(['user_id' => $author->id]);
        $acceptedPost = Post::factory()->create(['user_id' => $author->id]);
        $rejectedPost = Post::factory()->create(['user_id' => $author->id]);

        PostReport::create([
            'user_id' => $author->id,
            'post_id' => $pendingPost->id,
            'reason' => 'Pending',
            'status' => 'pending',
        ]);

        PostReport::create([
            'user_id' => $author->id,
            'post_id' => $acceptedPost->id,
            'reason' => 'Accepted',
            'status' => 'accepted',
        ]);

        PostReport::create([
            'user_id' => $author->id,
            'post_id' => $rejectedPost->id,
            'reason' => 'Rejected',
            'status' => 'rejected',
        ]);

        $visibleIds = Post::query()->visibleForVisitors()->pluck('id')->all();

        $this->assertContains($visiblePost->id, $visibleIds);
        $this->assertContains($rejectedPost->id, $visibleIds);
        $this->assertNotContains($pendingPost->id, $visibleIds);
        $this->assertNotContains($acceptedPost->id, $visibleIds);
    }
}
