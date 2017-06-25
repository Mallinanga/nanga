<?php

namespace Nanga\Supports;

class DisableRevisions
{

    public static function init()
    {
        add_action('init', [self::class, 'forTypes']);
    }

    public static function forTypes()
    {
        remove_post_type_support('page', 'revisions');
        remove_post_type_support('post', 'revisions');
    }
}
