<?php

namespace Nanga\Features;

class Shortcodes
{

    public static function init()
    {
        add_shortcode('antispambot', [self::class, 'antispambot']);
        add_shortcode('childpages', [self::class, 'childpages']);
        add_shortcode('paypal', [self::class, 'paypal']);
    }

    public static function antispambot($atts)
    {
        extract(shortcode_atts(['email' => ''], $atts));

        return antispambot($email);
    }

    public static function childpages()
    {
        global $post;
        if (is_page() && $post->post_parent) {
            $childpages = wp_list_pages('sort_column=menu_order&title_li=&child_of=' . $post->post_parent . '&echo=0');
        } else {
            $childpages = wp_list_pages('sort_column=menu_order&title_li=&child_of=' . $post->ID . '&echo=0');
        }
        if ($childpages) {
            $string = '<ul>' . $childpages . '</ul>';
        }

        return $string;
    }

    public static function paypal($atts)
    {
        $atts = shortcode_atts([
            'account'  => false,
            'amount'   => '50',
            'currency' => 'EUR',
            'size'     => 'LG',
        ], $atts);
        extract($atts);

        return '
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" class="paypal-button">
        <input type="hidden" name="cmd" value="_xclick">
        <input type="hidden" name="business" value="' . $account . '">
        <input type="hidden" name="amount" value="' . $amount . '">
        <input type="hidden" name="rm" value="0">
        <input type="hidden" name="currency_code" value="' . $currency . '">
        <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_buynow_' . $size . '.gif" name="submit" alt="">
        <img src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" alt="">
        </form>';
    }
}
