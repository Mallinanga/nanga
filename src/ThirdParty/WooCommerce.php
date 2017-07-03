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
        add_filter('user_has_cap', [self::class, 'capabilities']);
        if ( ! has_filter('robots_txt', 'Nanga\VIP\Features\robots')) {
            add_filter('robots_txt', [self::class, 'robots'], 10, 2);
        }
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

    public static function capabilities($caps)
    {
        if (empty($caps['manage_options'])) {
            $caps['manage_woocommerce'] = false;
        }

        return $caps;
    }

    public static function robots($output, $public)
    {
        $output .= 'Disallow: /*add-to-cart=*' . PHP_EOL;
        $output .= 'Disallow: /cart/' . PHP_EOL;
        $output .= 'Disallow: /checkout/' . PHP_EOL;
        $output .= 'Disallow: /my-account/' . PHP_EOL;

        return $output;
    }
}
