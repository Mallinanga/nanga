<?php

/*
Usage:
$frag = new Nanga_Cache( 'unique-key', 3600 );
if ( ! $frag->output() ) { // NOTE, testing for a return of false
    functions_that_do_stuff_live();
    these_should_echo();
    // IMPORTANT
    $frag->store();
    // YOU CANNOT FORGET THIS. If you do, the site will break.
}
*/

class Nanga_Cache
{

    const GROUP = 'nanga-cache';
    var $key;
    var $ttl;

    public function __construct($key, $ttl)
    {
        $this->key = $key;
        $this->ttl = $ttl;
    }

    public function output()
    {
        $output = wp_cache_get($this->key, self::GROUP);
        if ( ! empty($output)) {
            echo $output;

            return true;
        } else {
            ob_start();

            return false;
        }
    }

    public function store()
    {
        $output = ob_get_flush();
        wp_cache_add($this->key, $output, self::GROUP, $this->ttl);
    }
}
