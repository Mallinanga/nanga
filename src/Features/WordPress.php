<?php

namespace Nanga\Features;

class WordPress
{

    public static function init()
    {
        add_action('init', [self::class, 'supports'], 1);
        add_filter('request', [self::class, 'search']);
        add_filter('editable_roles', [self::class, 'roles']);
        add_filter('upload_mimes', [self::class, 'mimes']);
        // add_filter('http_request_args', [self::class, 'request'], 100, 1);
        // add_action('http_api_curl', [self::class, 'curl'], 100, 1);
    }

    public static function actions()
    {
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
        remove_action('wp_head', 'index_rel_link');
        remove_action('wp_head', 'parent_post_rel_link', 10);
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'start_post_rel_link', 10);
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);

        remove_action('post_updated', 'wp_check_for_changed_slugs', 12, 3);
        remove_action('attachment_updated', 'wp_check_for_changed_slugs', 12, 3);
    }

    public static function filters()
    {
    }

    public static function supports()
    {
        remove_post_type_support('page', 'comments');
        remove_post_type_support('page', 'custom-fields');
        remove_post_type_support('post', 'custom-fields');
    }

    public static function search($query)
    {
        if (isset($_GET['s']) && empty($_GET['s'])) {
            $query['s'] = 'empty';
        }

        return $query;
    }

    public static function roles($roles)
    {
        unset($roles['contributor']);

        return $roles;
    }

    public static function mimes($mimes)
    {
        $mimes['mp4'] = 'video/mp4';
        $mimes['ogg'] = 'video/ogg';
        $mimes['ogv'] = 'video/ogv';
        $mimes['svg'] = 'image/svg+xml';
        unset($mimes['bmp']);

        return $mimes;
    }

    public static function request($r)
    {
        $r['timeout'] = 600;

        return $r;
    }

    public static function curl($handle)
    {
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 600);
        curl_setopt($handle, CURLOPT_TIMEOUT, 600);
    }
}
