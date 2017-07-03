<?php

namespace Nanga\Features;

use WP_Customize_Color_Control;
use WP_Customize_Image_Control;

class Customizer
{

    public static function init()
    {
        remove_action('wp_head', 'wp_custom_css_cb', 101);
        add_filter('user_has_cap', [self::class, 'capabilities']);
        add_action('customize_preview_init', [self::class, 'assets'], 11);
        add_action('customize_register', [self::class, 'sections'], 11);
        // add_action('wp_head', [self::class, 'styles']);
    }

    public static function capabilities($caps)
    {
        if ( ! empty($caps['edit_pages'])) {
            $caps['edit_theme_options'] = true;
        }

        return $caps;
    }

    public static function assets()
    {
        wp_enqueue_script('nanga-customizer', NANGA_DIR_URL . 'assets/js/nanga-customizer.js', ['customize-preview'], null, true);
    }

    public static function sections($wp_customize)
    {
        $wp_customize->remove_section('custom_css');
        $wp_customize->remove_section('static_front_page');
        $wp_customize->add_section('nanga', [
            'title'    => __('Current Theme Settings', 'nanga'),
            'priority' => 100,
        ]);
        /*
        $wp_customize->add_setting('site_logo', [
            'transport' => 'postMessage',
        ]);
        $wp_customize->add_setting('site_color', [
            'default'   => '#0098ED',
            'transport' => 'postMessage',
        ]);
        $wp_customize->add_setting('site_secondary_color', [
            'default'   => '#E1E1E1',
            'transport' => 'postMessage',
        ]);
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'site_logo', [
            'label'    => __('Logo', 'nanga'),
            'section'  => 'vg_customizer_section',
            'settings' => 'site_logo',
        ]));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'site_color', [
            'label'    => __('Main Color', 'nanga'),
            'section'  => 'vg_customizer_section',
            'settings' => 'site_color',
        ]));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'site_secondary_color', [
            'label'    => __('Secondary Color', 'nanga'),
            'section'  => 'vg_customizer_section',
            'settings' => 'site_secondary_color',
        ]));
        */
    }

    public static function styles()
    {
        $styles         = '';
        $color          = get_theme_mod('site_color');
        $secondaryColor = get_theme_mod('site_secondary_color');
        if ($color) {
            $styles .= 'a{color:' . $color . ';text-decoration:none;}';
        }
        if ($secondaryColor) {
            $styles .= 'a:hover{color:' . $secondaryColor . ';}';
        }
        wp_add_inline_style('nanga', $styles);
    }
}
