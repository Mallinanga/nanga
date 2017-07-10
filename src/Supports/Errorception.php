<?php

namespace Nanga\Supports;

class Errorception
{

    public static function init()
    {
        if (nanga_site_in_development() || nanga_site_is_external()) {
            return;
        }
        // add_action('wp_head', [self::class, 'errorception'], 5);
        // add_action('wp_head', [self::class, 'sentry'], 5);
        // add_filter('wp_resource_hints', [self::class, 'hints'], 10, 2);
    }

    public static function errorception()
    {
        // https://stackoverflow.com/questions/951791/javascript-global-error-handling
        ?>
        <script>
            (function (_, e, rr, s) {
                var b;
                var c = _.onerror;
                _errs = [s];
                _.onerror = function () {
                    var a = arguments;
                    _errs.push(a);
                    //console.dir(a);
                    console.dir(_errs);
                    c && c.apply(this, a)
                };
                b = function () {
                    var c = e.createElement(rr), b = e.getElementsByTagName(rr)[0];
                    c.src = "//playground.app/wp-content/mu-plugins/" + s + ".js";
                    c.async = !0;
                    b.parentNode.insertBefore(c, b)
                };
                _.addEventListener ? _.addEventListener('load', b, !1) : _.attachEvent('onload', b)
            })
            (window, document, "script", "nanga-errorception");
        </script>
        <?php
    }

    public static function sentry()
    {
        ?>
        <script src="https://cdn.ravenjs.com/3.14.2/raven.min.js"></script>
        <script>Raven.config('https://8314a1bb77e645a49829d28fe1aa95a3@sentry.io/163320').install()</script>
        <?php
    }

    public static function hints($hints, $type)
    {
        if ('dns-prefetch' === $type) {
            $hints[] = '//cdn.ravenjs.com';
        }

        return $hints;
    }
}
