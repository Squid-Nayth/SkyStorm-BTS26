<?php

namespace Tests\Unit;

use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_following_returns_true_when_relationship_exists(): void
    {
        $user = User::factory()->create();
        $target = User::factory()->create();

        $user->following()->attach($target->id);

        $this->assertTrue($user->isFollowing($target));
    }

    public function test_unread_messages_count_counts_only_unread_received_messages(): void
    {
        $recipient = User::factory()->create();
        $sender = User::factory()->create();

        Message::create([
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'content' => 'Unread 1',
        ]);

        Message::create([
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'content' => 'Unread 2',
        ]);

        Message::create([
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'content' => 'Read',
            'read_at' => now(),
        ]);

        Message::create([
            'sender_id' => $recipient->id,
            'recipient_id' => $sender->id,
            'content' => 'Sent by recipient',
        ]);

        $this->assertSame(2, $recipient->unreadMessagesCount());
    }

    public function test_avatar_url_returns_null_when_no_avatar_exists(): void
    {
        $user = User::factory()->create(['avatar_path' => null]);

        $this->assertNull($user->avatarUrl());
    }

    public function test_avatar_url_returns_public_storage_path_when_avatar_exists(): void
    {
        $user = User::factory()->create(['avatar_path' => 'avatars/test-avatar.png']);

        $this->assertStringContainsString('/storage/avatars/test-avatar.png', $user->avatarUrl());
    }
}
