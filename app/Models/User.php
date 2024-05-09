<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
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
        'password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */


    protected $visible = [
        'id','first_name','last_name','nickname', 'clubs','biography','profile_photo_path','bg_photo_path','point','role_id', 'is_private', 'is_report', 'email', 'status', 'created_at'
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
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function getPermissionRoleNames()
    {
        $permissions = $this->permissions;

        if (method_exists($this, 'roles')) {
            $permissions = $permissions->merge($this->getPermissionsViaRoles());
        }

        return $permissions->sort()->values()->pluck('name');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function followers()
    {
        return $this->hasMany(Follow::class)->orderBy('id', 'desc');
    }

    public function following()
    {
        return $this->hasMany(Follow::class, 'follower_id')->orderBy('id', 'desc');
    }

    public function blocks()
    {
        return $this->hasMany(Block::class)->orderBy('id', 'desc');
    }

    public function blocked()
    {
        return $this->hasMany(Block::class, 'blocker_id')->orderBy('id', 'desc');
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    public function clubs()
    {
        return $this->belongsToMany(Club::class, 'favorite_clubs','user_id', 'club_id')->with('sport', 'country');
    }

    public function clubsLimited()
    {
        return $this->belongsToMany(Club::class, 'favorite_clubs','user_id', 'club_id')->limit(2);
    }

    public function getFullNameAttribute()
    {
        return !empty($this->nickname) ? $this->nickname : "{$this->first_name} {$this->last_name}";
    }

    public function getStatusNameAttribute()
    {
        return $this->status == 1 ? __('site.Active') : __('site.Inactive');
    }
}
