<?php

namespace Nanga\Supports;

class Support
{

    public static function init()
    {
        add_action('admin_bar_menu', [self::class, 'nodes'], 100);
        // add_action('admin_footer', [self::class, 'helpscout']);
    }

    public static function nodes($wp_admin_bar)
    {
        if ( ! current_user_can('edit_pages')) {
            return;
        }
        if ( ! nanga_site_is_external()) {
            $email = apply_filters('nanga_support_email', 'info@vgwebthings.com');
            $wp_admin_bar->add_node([
                'href'   => 'mailto:' . $email . '?subject=' . __('Support Request', 'nanga'),
                'id'     => 'nanga-support',
                'parent' => 'top-secondary',
                'title'  => __('Get Support', 'nanga'),
            ]);
        }
    }

    public static function helpscout()
    {
        if (apply_filters('nanga_support_helpscout', false)) {
            return;
        }
        if (current_user_can('edit_pages')) {
            global $display_name, $user_email;
            echo '<script>!function(e,o,n){window.HSCW=o,window.HS=n,n.beacon=n.beacon||{};var t=n.beacon;t.userConfig={},t.readyQueue=[],t.config=function(e){this.userConfig=e},t.ready=function(e){this.readyQueue.push(e)},o.config={docs:{enabled:!1,baseUrl:""},contact:{enabled:!0,formId:"31d7234d-0180-11e7-b148-0ab63ef01522"}};var r=e.getElementsByTagName("script")[0],c=e.createElement("script");c.type="text/javascript",c.async=!0,c.src="https://djtflbt20bdde.cloudfront.net/",r.parentNode.insertBefore(c,r)}(document,window.HSCW||{},window.HS||{});</script>';
            echo "<script>HS.beacon.config({color:'#0098ed',attachment:true,instructions:'',icon:'question',poweredBy:false});</script>";
            echo "<script>HS.beacon.ready(function(){HS.beacon.identify({name: '" . $display_name . "',email: '" . $user_email . "'});});</script>";
        }
    }
}
