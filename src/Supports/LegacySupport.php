<?php

namespace Nanga\Supports;

class LegacySupport
{

    public static function init()
    {
        add_action('admin_enqueue_scripts', [self::class, 'assets']);
        //$this->loader->add_action('wp_dashboard_setup', $pluginAdmin, 'support_request_widget');
    }

    public function assets($screen)
    {
        if ('index.php' === $screen) {
            wp_enqueue_script('nanga-support-request', NANGA_DIR_URL . 'assets/js/nanga-support-form.js', ['jquery'], NANGA_VERSION, true);
            wp_localize_script('nanga-support-request', 'nanga' . '_support_request', [
                'msg_success' => __('Thank you! Your request has been sent. We will get back at you as soon as possible.', 'nanga'),
                'msg_error'   => __('Oops! Something went wrong and we couldn\'t send your message.', 'nanga'),
            ]);
        }
    }

    public function support_request_widget()
    {
        add_meta_box('support_request_widget', __('Create a Support Request', 'nanga'), [$this, 'support_request_form'], 'dashboard', 'normal', 'low');
    }

    public function support_request_form()
    {
        echo '<div class="support-request-container vg-container">';
        echo '<div class="support-request-container__messages"></div>';
        echo '<form id="support-request-form" accept-charset="UTF-8" action="https://formkeep.com/f/36041913c4c7" method="POST"><input type="hidden" name="utf8" value="✓"> <p><input type="email" name="email" placeholder="' . __('Your email', 'nanga') . '" value="' . get_the_author_meta('user_email', get_current_user_id()) . '" class="widefat" required></p> <p><input type="text" name="name" placeholder="' . __('Your name', 'nanga') . '" value="' . get_the_author_meta('display_name', get_current_user_id()) . '" class="widefat" required></p> <p><textarea name="message" placeholder="' . __('Your message', 'nanga') . '" rows="10" class="widefat" required></textarea></p> <input type="hidden" name="site" value="' . home_url() . '"> <input type="submit" id="support-request-form__submit" class="button button-primary" value="' . __('Send Support Request', 'nanga') . '"> </form>';
        echo '</div>';
    }
}
