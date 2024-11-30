<?php

namespace App\Policies;

use App\ArticlePermissions;
use App\Models\Article;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ArticlePolicy
{

    public function manageArticles(User $user) {
        return $user->hasAnyPermission([
                ArticlePermissions::ALLOW_CREATE,
                ArticlePermissions::ALLOW_UPDATE,
                ArticlePermissions::ALLOW_DELETE,
                ArticlePermissions::ALLOW_UPDATE_ANY,
                ArticlePermissions::ALLOW_DELETE_ANY,
            ]
        );
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission([
            ArticlePermissions::ALLOW_CREATE,
            ArticlePermissions::ALLOW_UPDATE_ANY,
            'article:delete-any']
        );
    }

    /**
     * Determine whether the user can create models.
     */
    public function createArticle(User $user): Response
    {
        if ($user->hasPermission(ArticlePermissions::DENY_CREATE)) {
            return Response::denyAsNotFound();
        }

        return $user->hasPermission(ArticlePermissions::ALLOW_CREATE) ?
            Response::allow() :
            Response::denyAsNotFound();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function updateArticle(User $user, Article $article): Response
    {
        if ($user->didNotWrite($article)) {
            if ($user->hasPermission(ArticlePermissions::DENY_UPDATE_ANY)) {
                return Response::denyAsNotFound();
            }

            return $user->hasPermission(ArticlePermissions::ALLOW_UPDATE_ANY) ?
                Response::allow() :
                Response::denyAsNotFound();
        }

        return $user->hasPermission(ArticlePermissions::ALLOW_UPDATE) ?
            Response::allow() :
            Response::denyAsNotFound();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function deleteArticle(User $user, Article $article): Response
    {
        if ($user->didNotWrite($article)) {
            if ($user->hasPermission(ArticlePermissions::DENY_DELETE_ANY)) {
                return Response::denyAsNotFound();
            }

            return $user->hasPermission(ArticlePermissions::ALLOW_DELETE_ANY) ?
                Response::allow() :
                Response::denyAsNotFound();
        }

        return $user->hasPermission(ArticlePermissions::ALLOW_DELETE) ?
            Response::allow() :
            Response::denyAsNotFound();
    }
}