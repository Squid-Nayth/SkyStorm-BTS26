<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        ];
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    // Les utilisateurs que JE suis
    public function following()
    {
        return $this->belongsToMany(
            User::class,
            'followers',   // table pivot
            'follower_id', // clé étrangère de CE modèle
            'following_id' // clé étrangère du modèle cible
        );
    }

    // Les utilisateurs qui ME suivent
    public function followers()
    {
        return $this->belongsToMany(
            User::class,
            'followers',   // table pivot
            'following_id', // clé étrangère de CE modèle
            'follower_id'   // clé étrangère du modèle cible
        );
    }
}
