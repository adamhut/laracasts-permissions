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
    protected $guarded = [];


    protected $attributes = [
        'permissions' => '[]'
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
            'permissions' => 'array',
            // 'is_admin' => 'boolean',
        ];
    }

    // public function roles():BelongsToMany
    // {
    //     return $this->belongsToMany(Role::class)->withTimestamps();
    // }


    // public function hasRole(string $role):bool
    // {
    //     if (Auth::user()->id == $this->id && Context::hasHidden('roles')) {
    //         return in_array(strtolower($role), Context::getHidden('roles'));
    //     }


    //     return $this->roles->contains('auth_code', $role);
    // }

    // public function hasAnyRole(array $roles):bool
    // {
    //     if (Auth::user()->id == $this->id && Context::hasHidden('roles')) {
    //         $matches = array_intersect(
    //             array_map('strtolower',$roles),
    //             Context::getHidden('roles')
    //         );

    //         return !empty($matches);
    //     }

    //     return $this->roles->whereIn('auth_code', $roles)->exists();
    // }


    public function hasPermission(string $permission):bool
    {
        return $this->getAllPermissions()->contains(strtolower($permission));


        return in_array(strtolower($permission), $this->permissions);
    }

    public function getAllPermissions()
    {
        if (Auth::user()->id == $this->id && Context::hasHidden('permissions')) {
            return Context::getHidden('permissions');
        }

        $groupPermssions = $this
            ->groups()
            ->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->pluck('auth_code');

        $permissions = collect($this->permissions);

        return $groupPermssions->merge($permissions)->unique()->map(function($permission){
            return strtolower($permission);
        });
    }


    public function hasAnyPermission(array $permissions):bool
    {
        $perms = array_map('strtolower', $permissions);

        return $this->getAllPermissions()->intersect($perms)->isNotEmpty();

        // $matches = array_intersect(
        //         array_map('strtolower',$permissions),
        //         $this->permissions
        //     );
        // return !empty($matches);
    }


    public function groups():BelongsToMany
    {
        return $this->belongsToMany(Group::class);
    }

}
