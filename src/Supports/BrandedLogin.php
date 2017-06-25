<?php

namespace Nanga\Supports;

class BrandedLogin
{

    public static function init()
    {
        add_action('wp_login_failed', [self::class, 'unauthorized']);
        add_action('login_init', [self::class, 'styles']);
        add_action('login_head', [self::class, 'logo'], 11);
        add_action('login_enqueue_scripts', [self::class, 'assets']);
    }

    public static function styles()
    {
        //wp_deregister_style('forms');
        //wp_deregister_style('l10n');
        //wp_deregister_style('login');
        wp_deregister_style('buttons');
        wp_deregister_style('dashicons');
        wp_deregister_style('open-sans');
        wp_register_style('buttons', false);
        wp_register_style('dashicons', false);
        wp_register_style('open-sans', false);
    }

    public static function assets()
    {
        $css = apply_filters('nanga_login_css', NANGA_DIR_URL . 'assets/css/nanga-login.css');
        wp_register_style('nanga-login', $css, ['login'], NANGA_VERSION, 'all');
        wp_print_styles('nanga-login');
    }

    public static function logo()
    {
        echo '<style>.login h1{display:block;}.login h1 a{background-image:none,url(' . self::logoSource() . ');width:100%;height:125px;background-size:contain;}</style>';
    }

    public static function logoSource()
    {
        //$siteLogo = url_to_postid(get_theme_mod('site_logo'));
        $logo     = NANGA_DIR_URL . 'assets/img/logo.svg';
        $siteIcon = wp_get_attachment_url(get_option('site_icon'));
        if ($siteIcon) {
            $logo = $siteIcon;
        }
        if (file_exists(get_stylesheet_directory() . '/assets/img/logo.png')) {
            $logo = get_stylesheet_directory_uri() . '/assets/img/logo.png';
        }

        return apply_filters('nanga_login_logo', $logo);
    }

    public static function unauthorized($username)
    {
        status_header(401);
    }
}
