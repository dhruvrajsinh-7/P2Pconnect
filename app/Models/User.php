<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function images(): HasMany
    {
        return $this->hasMany(UserImage::class);
    }
    public function coverImage(): HasOne
    {
        return $this->hasOne(UserImage::class)->orderByDesc('id')->where('location', 'cover')->withDefault(function ($userImage) {
            $userImage->path = 'user-images/coverimage.jpg';
        });
    }
    public function profileImage(): HasOne
    {
        return $this->hasOne(UserImage::class)->orderByDesc('id')->where('location', 'profile')->withDefault(function ($userImage) {
            $userImage->path = 'user-images/profile-pic.png';
        });
    }
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
    public function friends(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friends', 'friend_id', 'user_id');
    }
    public function likedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'likes', 'user_id', 'post_id');
    }
}
