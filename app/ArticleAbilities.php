<?php

namespace App;

enum ArticleAbilities: string
{
    case CREATE = 'create-article';
    case UPDATE = 'update-article';
    case DELETE = 'delete-article';
}
