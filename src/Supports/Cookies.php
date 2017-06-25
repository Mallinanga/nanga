<?php

namespace Nanga\Supports;

class Cookies
{

    public static function init()
    {
        //add_action('wp_enqueue_scripts', 'assets');
        //add_action('wp_footer', 'template');
    }

    public function assets()
    {
        if (current_theme_supports('nanga-cookies')) {
            wp_enqueue_script('nanga-cookies', 'https://unpkg.com/jquery.cookie', ['jquery'], null, true);
            wp_add_inline_script('nanga-cookies', 'if (!jQuery.cookie("cookie_notice")){jQuery(".cookies").show();}jQuery(".cookies__close").click(function(){jQuery(".cookies").fadeOut();jQuery.cookie("cookie_notice", 1, { expires : 10, path : "/" });});');
        }
    }

    public function template()
    {
        if (current_theme_supports('nanga-cookies')) {
            $cookiesText       = apply_filters('nanga_cookies_text', 'To help personalize content, tailor and measure ads, and provide a safer experience, we use cookies. By clicking or navigating the site, you agree to allow our collection of information through cookies.');
            $cookiesButtonText = apply_filters('nanga_cookies_button_text', 'I agree');
            echo '<div class="cookies" style="display:none;"><div class="cookies__container"><div class="cookies__text">' . $cookiesText . '</div><a href="#!" class="cookies__close">' . $cookiesButtonText . '</a></div></div>';
        }
    }
}
