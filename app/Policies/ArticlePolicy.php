<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ArticlePolicy
{

    public function manageArticles(User $user)
    {
        return $user->hasAnyPermission([
            'article:create',
            'article:update',
            'article:delete',
            'article:update-any',
            'article:delete-any',
        ]);
        // return $user->hasAnyRole(['admin', 'editor','author']);
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission([
            'article:create',
            'article:update-any',
            'article:delete-any',
        ]);
        return $user->hasAnyRole(['admin', 'editor']) ;
    }


    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        if ($user->hasPermission('article:create:deny')) {
            return Response::denyAsNotFound();
        }

        return $user->hasPermission('article:create')?
            Response::allow():
            Response::denyAsNotFound();
        // return $user->hasAnyRole(['admin', 'author']) ?
        //     Response::allow():
        //     Response::denyAsNotFound();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Article $article):Response
    {
        if ($user->didNotWrite($article)) {
            if ($user->hasPermission('article:update-any:deny')){
                return Response::denyAsNotFound();
            }

            return $user->hasPermission('article:update-any')?
                Response::allow() :
                Response::denyAsNotFound();

        }

        return $user->hasPermission('article:update') ?
            Response::allow() :
            Response::denyAsNotFound();

        //return $user->hasPermission('article:create')?
        // if ($user->hasAnyRole(['admin', 'editor'])) {
        // if ($user->hasPermission('article:update-any')) {
        //     // return true;
        //     return Response::allow();
        // }

        // // return $user->hasRole('author') && $user->id === $article->author_id ?
        // return $user->hasPermission('article:update') && $user->id === $article->author_id ?
        //     Response::allow() :
        //     Response::denyAsNotFound();
        //     // Response::deny('You do not own this article');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Article $article): Response
    {

        if ($user->didNotWrite($article)) {
            if ($user->hasPermission('article:delete-any:deny')){
                return Response::denyAsNotFound();
            }

            return $user->hasPermission('article:delete-any')?
                Response::allow() :
                Response::denyAsNotFound();

        }

        // if ($user->hasPermission('article:delete-any:deny')){
        //     return Response::denyAsNotFound();
        // }

        // if ($user->hasPermission('article:delete-any')) {
        //     // return true;
        //     return Response::allow();
        // }

        // return $user->hasRole('author') && $user->id === $article->author_id ?
        return $user->hasPermission('article:delete') && $user->id === $article->author_id ?
            Response::allow() :
            Response::denyAsNotFound();
    }

}
