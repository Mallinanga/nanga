<?php

namespace Nanga\Features;

class Comments
{

    public static function init()
    {
        remove_action('do_pings', 'do_all_pings', 10, 1);
        remove_action('set_comment_cookies', 'wp_set_comment_cookies');
        add_filter('comment_form_default_fields', [self::class, 'fields']);
        add_filter('comment_form_defaults', [self::class, 'defaults']);
    }

    public static function fields($fields)
    {
        unset($fields['url']);

        return $fields;
    }

    public static function defaults($defaults)
    {
        $defaults['cancel_reply_link']    = __('Cancel reply');
        $defaults['comment_field']        = '<textarea id="comment" name="comment" rows="10" placeholder="' . __('Comment') . '" aria-required="true" required></textarea>';
        $defaults['comment_notes_after']  = false;
        $defaults['comment_notes_before'] = false;
        $defaults['logged_in_as']         = false;
        $defaults['title_reply']          = false;
        $defaults['title_reply_to']       = false;

        return $defaults;
    }
}
