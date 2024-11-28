<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Context;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        // 'is_admin',
    ];

    public function articles() : HasMany {
        return $this->hasMany(Article::class, 'author_id');
    }

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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            // 'is_admin' => 'boolean',
        ];
    }



    public function roles():BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }


    public function hasRole(string $role):bool
    {
        if (Auth::user()->id == $this->id && Context::hasHidden('roles')) {
            return in_array(strtolower($role), Context::getHidden('roles'));
        }


        return $this->roles->contains('auth_code', $role);
    }

    public function hasAnyRole(array $roles):bool
    {
        if (Auth::user()->id == $this->id && Context::hasHidden('roles')) {
            $matches = array_intersect(
                array_map('strtolower',$roles),
                Context::getHidden('roles')
            );

            return !empty($matches);
        }

        return $this->roles->whereIn('auth_code', $roles)->exists();
    }
}
