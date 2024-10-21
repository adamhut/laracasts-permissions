<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\User;
use App\Policies\ArticlePolicy;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
//        Gate::define('manage-articles', function(User $user, Article $article) {
//            return ($user->hasRole('admin') || $user->hasRole('editor'))
//                || ($user->hasRole('author') && $user->id === $article->author_id);
//        });

//        Gate::policy(Article::class, ArticlePolicy::class);


        Blade::directive('role', function($expression) {
            return "<?php if (Auth::user()->hasAnyRole([$expression])): ?>";
        });

        Blade::directive('endrole', function($expression) {
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
