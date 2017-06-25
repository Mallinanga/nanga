<?php

namespace Nanga\Features;

class Mail
{

    public static function init()
    {
        // add_filter('wp_mail_from', [self::class, 'from']);
        // add_filter('wp_mail_from_name', [self::class, 'name']);
    }

    public static function from()
    {
        $sitename = strtolower($_SERVER['SERVER_NAME']);
        if (substr($sitename, 0, 4) == 'www.') {
            $sitename = substr($sitename, 4);
        }
        $from = 'info@' . $sitename;

        return apply_filters('nanga_mail_from', $from);
    }

    public static function name()
    {
        $from_name = get_bloginfo();

        return apply_filters('nanga_mail_from_name', $from_name);
    }

    public static function ses()
    {
    }

    public static function mandrill()
    {
        add_action('phpmailer_init', function ($phpmailer) {
            $phpmailer->Host = 'smtp.mandrillapp.com';
            $phpmailer->IsSMTP();
            $phpmailer->Password   = MANDRILL_PASSWORD;
            $phpmailer->Port       = 25;
            $phpmailer->SMTPAuth   = true;
            $phpmailer->SMTPDebug  = false;
            $phpmailer->SMTPSecure = 'tls';
            $phpmailer->Username   = MANDRILL_USERNAME;
        });
    }

    public static function mailtrap()
    {
        add_action('phpmailer_init', function ($phpmailer) {
            $phpmailer->Host = 'smtp.mailtrap.io';
            $phpmailer->isSMTP();
            $phpmailer->Password = MAILTRAP_PASSWORD;
            $phpmailer->Port     = 2525;
            $phpmailer->SMTPAuth = true;
            $phpmailer->Username = MAILTRAP_USERNAME;
        });
    }
}
