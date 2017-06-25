<?php

namespace Nanga\ThirdParty;

class WooCommerce
{

    public static function init()
    {
        if ( ! class_exists('WooCommerce')) {
            return;
        }
        remove_action('wp_head', 'wc_generator_tag');
        add_action('init', [self::class, 'supports'], 100);
        add_action('admin_menu', [self::class, 'menu'], 999);
    }

    public static function supports()
    {
        remove_post_type_support('product', 'custom-fields');
    }

    public static function menu()
    {
        if ( ! current_user_can('manage_woocommerce')) {
            remove_menu_page('woocommerce');
        }
    }
}
