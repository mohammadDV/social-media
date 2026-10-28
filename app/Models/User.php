<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'nickname',
        'mobile',
        'biography',
        'profile_photo_path',
        'bg_photo_path',
        'national_code',
        'point',
        'role_id',
        'level',
        'status',
        'email',
        'is_private',
        'is_report',
        'google_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $visible = [
        'id', 'first_name', 'last_name', 'nickname', 'clubs', 'biography', 'profile_photo_path', 'bg_photo_path', 'point', 'role_id', 'is_private', 'is_report', 'email', 'status', 'created_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function getProfilePhotoUrlAttribute(): ?string
    {
        return $this->profile_photo_path;
    }

    public function getPermissionRoleNames()
    {
        $permissions = $this->permissions->merge($this->getPermissionsViaRoles());

        return $permissions->sort()->values()->pluck('name');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(Status::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function followers(): HasMany
    {
        return $this->hasMany(Follow::class)->orderBy('id', 'desc');
    }

    public function following(): HasMany
    {
        return $this->hasMany(Follow::class, 'follower_id')->orderBy('id', 'desc');
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class)->orderBy('id', 'desc');
    }

    public function blocked(): HasMany
    {
        return $this->hasMany(Block::class, 'blocker_id')->orderBy('id', 'desc');
    }

    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    public function clubs(): BelongsToMany
    {
        return $this->belongsToMany(Club::class, 'favorite_clubs', 'user_id', 'club_id')->with('sport', 'country');
    }

    public function clubsLimited(): BelongsToMany
    {
        return $this->belongsToMany(Club::class, 'favorite_clubs', 'user_id', 'club_id')->limit(2);
    }

    public function getFullNameAttribute()
    {
        return ! empty($this->nickname) ? $this->nickname : "{$this->first_name} {$this->last_name}";
    }

    public function getStatusNameAttribute()
    {
        return $this->status == 1 ? __('site.Active') : __('site.Inactive');
    }
}
