<?php

namespace Nanga\Supports;

class DisableCommentsNotifications
{

    public static function init()
    {
        remove_action('comment_post', 'wp_new_comment_notify_moderator');
        remove_action('comment_post', 'wp_new_comment_notify_postauthor');
    }
}
