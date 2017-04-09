<?php

class Nanga_Public
{

    private $nanga;
    private $version;

    public function __construct($nanga, $version)
    {
        $this->nanga   = $nanga;
        $this->version = $version;
    }

    public function enqueue_styles()
    {
        $suffix = (defined('WP_ENV') && 'development' === WP_ENV) ? '' : '.min';
        wp_deregister_style('open-sans');
        wp_register_style('open-sans', false);
        wp_enqueue_style($this->nanga, plugin_dir_url(__FILE__) . 'css/nanga-public.css', [], $this->version, 'all');
        $inline_styles = '';
        //@todo
        //$site_logo          = get_theme_mod( 'site_logo' );
        $site_color           = get_theme_mod('site_color');
        $site_secondary_color = get_theme_mod('site_secondary_color');
        /*
        if ( $site_logo ) {
            //$site_logo_size = @getimagesize( $site_logo );
            //$inline_styles .= '#logo{background:url(' . $site_logo . ') no-repeat center center;background-size:contain;width:' . $site_logo_size[0] . 'px;height:' . $site_logo_size[1] . 'px;display:inline-block;}';
            $inline_styles .= '#logo{background:url(' . $site_logo . ') no-repeat center center;background-size:contain;display:inline-block;}';
        }
        */
        if ($site_color) {
            $inline_styles .= 'a{color:' . $site_color . ';text-decoration:none;}';
        }
        if ($site_secondary_color) {
            $inline_styles .= 'a:hover{color:' . $site_secondary_color . ';}';
        }
        wp_add_inline_style($this->nanga, $inline_styles);
    }

    public function enqueue_scripts()
    {
        global $wp_styles;
        global $is_IE;
        $script_options = [
            'ajax_url'     => admin_url('admin-ajax.php'),
            'locale'       => get_locale(),
            'current_user' => get_current_user_id(),
            'environment'  => WP_ENV,
            'nonce'        => wp_create_nonce(),
        ];
        if (current_theme_supports('nanga-modernizr')) {
            wp_enqueue_script('modernizr', plugin_dir_url(__FILE__) . 'js/_modernizr.js', [], null, false);
        }
        if ( ! is_admin()) {
            wp_deregister_script('jquery');
            if (current_theme_supports('nanga-cdn-assets')) {
                wp_register_script('jquery', '//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js', [], null, false);
            } else {
                wp_register_script('jquery', plugin_dir_url(__FILE__) . 'js/jquery.min.js', [], null, false);
            }
            wp_enqueue_script('jquery');
        }
        if (current_theme_supports('nanga-mobile-check')) {
            $script_options['is_mobile'] = wp_is_mobile_phone() ? 'true' : 'false';
            $script_options['is_tablet'] = wp_is_mobile() ? 'true' : 'false';
            wp_enqueue_script('wurfl', '//wurfl.io/wurfl.js', [], null, false);
        }
        wp_enqueue_script($this->nanga, plugin_dir_url(__FILE__) . 'js/nanga-public.js', ['jquery'], $this->version, true);
        wp_localize_script($this->nanga, $this->nanga, $script_options);
        if (current_theme_supports('nanga-debug-assets')) {
            if (current_theme_supports('nanga-cdn-assets')) {
                wp_enqueue_script('html-inspector', '//cdnjs.cloudflare.com/ajax/libs/html-inspector/0.8.1/html-inspector.js', [], null, true);
            } else {
                wp_enqueue_script('html-inspector', plugin_dir_url(__FILE__) . '../assets/vendor/html-inspector/html-inspector.js', [], null, true);
            }
            wp_enqueue_script($this->nanga . '-debug', plugin_dir_url(__FILE__) . 'js/nanga-debug.js', [], null, true);
        }
        if ($is_IE) {
            echo '<script type="text/javascript">/* <![CDATA[ */ !function (a, b) { function c(a, b) { var c = a.createElement("p"), d = a.getElementsByTagName("head")[0] || a.documentElement; return c.innerHTML = "x<style>" + b + "</style>", d.insertBefore(c.lastChild, d.firstChild) } function d() { var a = t.elements; return "string" == typeof a ? a.split(" ") : a } function e(a, b) { var c = t.elements; "string" != typeof c && (c = c.join(" ")), "string" != typeof a && (a = a.join(" ")), t.elements = c + " " + a, j(b) } function f(a) { var b = s[a[q]]; return b || (b = {}, r++, a[q] = r, s[r] = b), b } function g(a, c, d) { if (c || (c = b), l)return c.createElement(a); d || (d = f(c)); var e; return e = d.cache[a] ? d.cache[a].cloneNode() : p.test(a) ? (d.cache[a] = d.createElem(a)).cloneNode() : d.createElem(a), !e.canHaveChildren || o.test(a) || e.tagUrn ? e : d.frag.appendChild(e) } function h(a, c) { if (a || (a = b), l)return a.createDocumentFragment(); c = c || f(a); for (var e = c.frag.cloneNode(), g = 0, h = d(), i = h.length; i > g; g++)e.createElement(h[g]); return e } function i(a, b) { b.cache || (b.cache = {}, b.createElem = a.createElement, b.createFrag = a.createDocumentFragment, b.frag = b.createFrag()), a.createElement = function (c) { return t.shivMethods ? g(c, a, b) : b.createElem(c) }, a.createDocumentFragment = Function("h,f", "return function(){var n=f.cloneNode(),c=n.createElement;h.shivMethods&&(" + d().join().replace(/[\w\-:]+/g, function (a) { return b.createElem(a), b.frag.createElement(a), \'c("\' + a + \'")\' }) + ");return n}")(t, b.frag) } function j(a) { a || (a = b); var d = f(a); return !t.shivCSS || k || d.hasCSS || (d.hasCSS = !!c(a, "article,aside,dialog,figcaption,figure,footer,header,hgroup,main,nav,section{display:block}mark{background:#FF0;color:#000}template{display:none}")), l || i(a, d), a } var k, l, m = "3.7.2", n = a.html5 || {}, o = /^<|^(?:button|map|select|textarea|object|iframe|option|optgroup)$/i, p = /^(?:a|b|code|div|fieldset|h1|h2|h3|h4|h5|h6|i|label|li|ol|p|q|span|strong|style|table|tbody|td|th|tr|ul)$/i, q = "_html5shiv", r = 0, s = {}; !function () { try { var a = b.createElement("a"); a.innerHTML = "<xyz></xyz>", k = "hidden"in a, l = 1 == a.childNodes.length || function () { b.createElement("a"); var a = b.createDocumentFragment(); return "undefined" == typeof a.cloneNode || "undefined" == typeof a.createDocumentFragment || "undefined" == typeof a.createElement }() } catch (c) { k = !0, l = !0 } }(); var t = {elements: n.elements || "abbr article aside audio bdi canvas data datalist details dialog figcaption figure footer header hgroup main mark meter nav output picture progress section summary template time video", version: m, shivCSS: n.shivCSS !== !1, supportsUnknownElements: l, shivMethods: n.shivMethods !== !1, type: "default", shivDocument: j, createElement: g, createDocumentFragment: h, addElements: e}; a.html5 = t, j(b) }("undefined" != typeof window ? window : this, document); /* ]]> */</script>';
        }
    }

    public function asset_cachebusting()
    {
        if (current_theme_supports('nanga-asset-cachebusting')) {
            global $wp_styles, $wp_scripts;
            $wp_dir         = str_replace(home_url(), '', site_url());
            $site_root_path = str_replace($wp_dir, '', ABSPATH);
            foreach ([
                         'wp_styles',
                         'wp_scripts',
                     ] as $resource) {
                foreach ((array)$$resource->queue as $name) {
                    if (empty($$resource->registered[$name])) {
                        continue;
                    }
                    $src = $$resource->registered[$name]->src;
                    if (0 === strpos($src, '/')) {
                        $src = site_url($src);
                    }
                    if (false === strpos($src, home_url())) {
                        continue;
                    }
                    $file = str_replace(home_url('/'), $site_root_path, $src);
                    if ( ! file_exists($file)) {
                        continue;
                    }
                    $mtime = filectime($file);
                    //$$resource->registered[ $name ]->ver = $$resource->registered[ $name ]->ver . '-' . $mtime;
                    $$resource->registered[$name]->ver = $mtime;
                }
            }
        }
    }

    public function the_password_form()
    {
        $output = '<form action="' . esc_url(site_url('wp-login.php?action=postpass', 'login_post')) . '" class="post-password-form pure-form pure-form-stacked" method="post">';
        $output .= '<input name="post_password" type="password" class="pure-input-1">';
        $output .= '<input type="submit" class="pure-button pure-input-1" value="' . __('View', $this->nanga) . '">';
        $output .= '</form>';

        return $output;
    }

    public function remove_self_closing_tags($input)
    {
        return str_replace(' />', '>', $input);
    }

    public function analytics()
    {
        if ( ! current_user_can('manage_options')) {
            $google_analytics_ua = get_field('vg_google_analytics', 'options');
            if ( ! empty($google_analytics_ua) && get_option('blog_public')) {
                echo '<script type="text/javascript">var _gaq = _gaq || []; _gaq.push([\'_setAccount\', \'' . $google_analytics_ua . '\']); _gaq.push([\'_trackPageview\']); (function () { var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true; ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\'; var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s); })();</script>';
            }
            if (get_field('vg_google_analytics_events', 'options', true)) {
                echo '<script type="text/javascript">(function (tos) { window.setInterval(function () { tos = (function (t) { return t[0] == 50 ? (parseInt(t[1]) + 1) + \':00\' : (t[1] || \'0\') + \':\' + (parseInt(t[0]) + 10); })(tos.split(\':\').reverse()); window.pageTracker ? pageTracker._trackEvent(\'Time\', \'Log\', tos) : _gaq.push([\'_trackEvent\', \'Time\', \'Log\', tos]); }, 10000); })(\'00\');</script>';
            }
        }
    }

    public function body_class($classes)
    {
        global $wp_query;
        $no_classes = [];
        if (is_page()) {
            $page_id      = $wp_query->get_queried_object_id();
            $no_classes[] = 'page-id-' . $page_id;
            $ancestors    = get_ancestors(get_queried_object_id(), 'page');
            if ( ! empty ($ancestors)) {
                foreach ($ancestors as $ancestor) {
                    $no_classes[] = 'parent-pageid-' . $ancestor;
                }
            }
            $classes[] = str_replace('.php', '', basename(get_page_template()));
        }
        if (is_single()) {
            $post_id      = $wp_query->get_queried_object_id();
            $no_classes[] = 'postid-' . $post_id;
        }
        if (is_author()) {
            $author_id    = $wp_query->get_queried_object_id();
            $no_classes[] = 'author-' . $author_id;
        }
        if (is_category()) {
            $cat_id       = $wp_query->get_queried_object_id();
            $no_classes[] = 'category-' . $cat_id;
        }
        if (is_tax()) {
            $ancestors = get_ancestors(get_queried_object_id(), get_queried_object()->taxonomy);
            if ( ! empty($ancestors)) {
                foreach ($ancestors as $ancestor) {
                    $term      = get_term($ancestor, get_queried_object()->taxonomy);
                    $classes[] = esc_attr("parent-$term->taxonomy-$term->term_id");
                }
            }
        }
        if (is_single() || is_page() && ! is_front_page()) {
            $classes[] = 'slug-' . basename(get_permalink());
        }
        $no_classes[] = 'page-template-default';
        $no_classes[] = 'page-id-' . get_option('page_on_front');
        $classes      = array_diff($classes, $no_classes);

        return $classes;
    }

    public function attachment_class($classes, $image_id, $align, $size)
    {
        $classes = str_replace(' wp-image-' . $image_id, '', $classes);
        $classes = $classes . ' image-in-content';

        return $classes;
    }

    public function post_class($classes, $class, $post_id)
    {
        global $wp_query;
        $remove_classes = [
            'page',
            'post',
            'post-' . $post_id,
            'status-publish',
        ];
        $classes        = array_diff($classes, $remove_classes);
        if (0 == $wp_query->current_post) {
            $classes[] = 'first-post';
        }

        return $classes;
    }

    public function nice_search()
    {
        global $wp_rewrite;
        if ( ! isset($wp_rewrite) || ! is_object($wp_rewrite) || ! $wp_rewrite->using_permalinks()) {
            return;
        }
        $search_base = $wp_rewrite->search_base;
        if (is_search() && ! is_admin() && false === strpos($_SERVER['REQUEST_URI'], "/{$search_base}/")) {
            wp_redirect(home_url("/{$search_base}/" . urlencode(get_query_var('s'))));
            exit();
        }
    }

    public function maintenance_mode()
    {
        if (get_option('nanga_maintenance_mode') && ! current_user_can('manage_options')) {
            header('HTTP/1.1 503 Service Unavailable', true, 503);
            header('Retry-After: 600');
            require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/nanga-maintenance-mode.php';
            die();
        }
    }

    public function relative_urls($input)
    {
        if ( ! (is_admin() || preg_match('/sitemap(_index)?\.xml/', $_SERVER['REQUEST_URI']) || in_array($GLOBALS['pagenow'], ['wp-login.php', 'wp-register.php']))) {
            preg_match('|https?://([^/]+)(/.*)|i', $input, $matches);
            if ( ! isset($matches[1]) || ! isset($matches[2])) {
                return $input;
            } elseif (($matches[1] === $_SERVER['SERVER_NAME']) || $matches[1] === $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT']) {
                return wp_make_link_relative($input);
            } else {
                return $input;
            }
        }
    }

    public function comment_form_default_fields($fields)
    {
        unset($fields['url']);

        return $fields;
    }

    function comment_form_defaults($defaults)
    {
        $defaults['comment_field']        = '<textarea id="comment" class="pure-input-1" name="comment" rows="10" placeholder="' . __('Your comment', $this->nanga) . '" aria-required="true" required></textarea>';
        $defaults['title_reply']          = false;
        $defaults['title_reply_to']       = false;
        $defaults['logged_in_as']         = false;
        $defaults['comment_notes_before'] = false;
        $defaults['comment_notes_after']  = false;
        $defaults['cancel_reply_link']    = __('Cancel', $this->nanga);

        return $defaults;
    }

    public function js_to_footer()
    {
        if (current_theme_supports('nanga-js-to-footer')) {
            remove_action('wp_head', 'wp_print_scripts');
            remove_action('wp_head', 'wp_print_head_scripts', 9);
            remove_action('wp_head', 'wp_enqueue_scripts', 1);
        }
    }

    public function disable_adminbar()
    {
        if ( ! current_user_can('manage_options')) {
            show_admin_bar(false);
        }
    }

    public function random_post_rewrite()
    {
        add_rewrite_rule('random/?$', 'index.php?random=1', 'top');
    }

    public function random_post_query_var($public_query_vars)
    {
        $public_query_vars[] = 'random';

        return $public_query_vars;
    }

    public function random_post_redirect()
    {
        if (1 == get_query_var('random')) {
            $random_posts = get_posts('post_type=post&orderby=rand&numberposts=1');
            if ($random_posts) {
                foreach ($random_posts as $random_post) {
                    $random_post_link = get_permalink($random_post);
                }
                wp_redirect($random_post_link, 307);
                exit;
            }
        }
    }

    public function remove_paragraphs_from_images($content)
    {
        return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
    }

    public function change_locale_on_the_fly($locale)
    {
        if ( ! is_admin()) {
            if (isset($_GET['l'])) {
                return $_GET['l'];
            } else {
                return $locale;
            }
        }

        return $locale;
    }

    public function cookiesAssets()
    {
        if (current_theme_supports('nanga-cookies')) {
            wp_enqueue_script('nanga-cookies', 'https://unpkg.com/jquery.cookie', ['jquery'], null, true);
            wp_add_inline_script('nanga-cookies', 'if (!jQuery.cookie("cookie_notice")){jQuery(".cookies").show();}jQuery(".cookies__close").click(function(){jQuery(".cookies").fadeOut();jQuery.cookie("cookie_notice", 1, { expires : 10, path : "/" });});');
        }
    }

    public function cookies()
    {
        if (current_theme_supports('nanga-cookies')) {
            $cookiesText       = apply_filters('nanga_cookies_text', 'To help personalize content, tailor and measure ads, and provide a safer experience, we use cookies. By clicking or navigating the site, you agree to allow our collection of information through cookies.');
            $cookiesButtonText = apply_filters('nanga_cookies_button_text', 'I agree');
            echo '<div class="cookies" style="display:none;"><div class="cookies__container"><div class="cookies__text">' . $cookiesText . '</div><a href="#!" class="cookies__close">' . $cookiesButtonText . '</a></div></div>';
        }
    }
}
