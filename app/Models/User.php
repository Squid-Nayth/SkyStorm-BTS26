<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'favorite_posts_public',
        'bio',
        'location',
        'website',
        'birthdate',
        'avatar_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'favorite_posts_public' => 'boolean',
            'birthdate' => 'date',
        ];
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_likes')->withTimestamps();
    }

    public function favoritePosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'favorite_posts')->withTimestamps();
    }

    public function postReports(): HasMany
    {
        return $this->hasMany(PostReport::class);
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }

    // Les utilisateurs que JE suis
    public function following(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'followers',   // table pivot
            'follower_id', // clé étrangère de CE modèle
            'following_id' // clé étrangère du modèle cible
        );
    }

    // Les utilisateurs qui ME suivent
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'followers',   // table pivot
            'following_id', // clé étrangère de CE modèle
            'follower_id'   // clé étrangère du modèle cible
        );
    }

    public function isFollowing(User $user): bool
    {
        return $this->following()->where('following_id', $user->id)->exists();
    }

    public function unreadMessagesCount(): int
    {
        return $this->receivedMessages()->whereNull('read_at')->count();
    }

    public function avatarUrl(): ?string
    {
        if (!$this->avatar_path) {
            return null;
        }

        return asset('storage/' . $this->avatar_path);
    }
}
