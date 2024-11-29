<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Article;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Gate::define('access-admin', function(User $user) {

            return $user->hasRole('admin') || $user->hasRole('editor') ||$user->hasRole('author') ;
            // return $user->is_admin;
        });

        Gate::define('manage-articles', function(User $user, Article $article) {
            return ($user->hasRole('admin') || $user->hasRole('editor')) ||
            ($user->hasRole('author') && $user->id === $article->author_id) ;
        });

        Gate::define('manage-users',function(User $user){
            return $user->hasAnyPermission(['user:create','permission:create']);
            return $user->hasRole('admin');
        });

        Blade::directive('role', function ($expression) {
            return "<?php if(auth()->user()->hasAnyRole([$expression])) : ?>";
        });

        Blade::directive('endrole', function ($expression) {
            return "<?php endif; ?>";
        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
