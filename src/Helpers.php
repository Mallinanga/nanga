<?php

namespace Nanga;

class Helpers
{

    /**
     * This will generate a line of CSS for use in header output. If the setting
     * ($mod_name) has no defined value, the CSS will not be output.
     *
     * @uses get_theme_mod()
     *
     * @param string $selector CSS selector
     * @param string $style    The name of the CSS *property* to modify
     * @param string $modName  The name of the 'theme_mod' option to fetch
     * @param string $prefix   Optional. Anything that needs to be output before the CSS property
     * @param string $postfix  Optional. Anything that needs to be output after the CSS property
     * @param bool   $echo     Optional. Whether to print directly to the page (default: true).
     *
     * @return string Returns a single line of CSS with selectors and a property.
     */
    public static function injectCSS($selector, $style, $modName, $prefix = '', $postfix = '', $echo = true)
    {
        $return = '';
        $mod    = get_theme_mod($modName);
        if ( ! empty($mod)) {
            $return = sprintf('%s { %s:%s; }', $selector, $style, $prefix . $mod . $postfix);
            if ($echo) {
                echo $return;
            }
        }

        return $return;
    }
}
