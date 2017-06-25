<?php

namespace Nanga\ThirdParty;

class WordPressSocialLogin
{

    public static function init()
    {
        if ( ! function_exists('wsl_add_stylesheets')) {
            return;
        }
        remove_action('wp_enqueue_scripts', 'wsl_add_stylesheets');
        remove_action('login_enqueue_scripts', 'wsl_add_stylesheets');
    }
}
