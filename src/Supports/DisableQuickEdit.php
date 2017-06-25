<?php

namespace Nanga\Supports;

class DisableQuickEdit
{

    public static function init()
    {
        add_filter('page_row_actions', [self::class, 'actions'], 10, 2);
        add_filter('post_row_actions', [self::class, 'actions'], 10, 2);
        // TODO remove edit from bulk actions dropdown
    }

    public static function actions($actions, $post)
    {
        unset($actions['inline hide-if-no-js']);

        return $actions;
    }
}
