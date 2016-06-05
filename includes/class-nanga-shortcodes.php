<?php

class Nanga_Shortcodes
{

    private $nanga;
    private $version;

    public function __construct($nanga, $version)
    {
        $this->nanga   = $nanga;
        $this->version = $version;
        add_shortcode('antispambot', [$this, 'antispambot']);
        add_shortcode('childpages', [$this, 'childpages']);
        add_shortcode('paypal', [$this, 'paypal']);
    }

    private function paypal($atts)
    {
        $atts = shortcode_atts([
            'account'  => false,
            'amount'   => '50',
            'currency' => 'EUR',
            'size'     => 'LG',
        ], $atts);
        extract($atts);

        return '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" class="paypal-button">
                <input type="hidden" name="cmd" value="_xclick">
                <input type="hidden" name="business" value="' . $account . '">
                <input type="hidden" name="amount" value="' . $amount . '">
                <input type="hidden" name="rm" value="0">
                <input type="hidden" name="currency_code" value="' . $currency . '">
                <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_buynow_' . $size . '.gif" name="submit" alt="">
                <img src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" alt="">
            </form>';
    }

    private function antispambot($atts)
    {
        // Usage: [antispambot email="email@address.com"]
        extract(shortcode_atts(['email' => ''], $atts));

        return antispambot($email);
    }

    private function childpages()
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
}
