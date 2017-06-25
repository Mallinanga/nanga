<?php

namespace Nanga\Supports;

class DisableWidgets
{

    public static function init()
    {
        add_action('widgets_init', [self::class, 'widgets'], 1);
    }

    public static function widgets()
    {
        //unregister_widget('WP_Nav_Menu_Widget');
        //unregister_widget('WP_Widget_Calendar');
        unregister_widget('WP_Widget_Categories');
        unregister_widget('WP_Widget_Links');
        unregister_widget('WP_Widget_Media_Audio');
        unregister_widget('WP_Widget_Media_Image');
        unregister_widget('WP_Widget_Media_Video');
        unregister_widget('WP_Widget_Meta');
        unregister_widget('WP_Widget_Pages');
        unregister_widget('WP_Widget_Recent_Comments');
        unregister_widget('WP_Widget_Recent_Posts');
        unregister_widget('WP_Widget_RSS');
        unregister_widget('WP_Widget_Search');
        unregister_widget('WP_Widget_Tag_Cloud');
    }
}
