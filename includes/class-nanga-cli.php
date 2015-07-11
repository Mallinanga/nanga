<?php

/**
 * Nanga wp-cli integration.
 */
class Nanga_CLI extends WP_CLI_Command {
    /**
     * Prints a greeting.
     *
     * ## OPTIONS
     *
     * <name>
     * : The name of the person to greet.
     *
     * ## EXAMPLES
     *
     * wp nanga-deploy hello Newman
     *
     * @synopsis <name>
     *
     * @param $args
     * @param $assoc_args
     */
    function hello( $args, $assoc_args ) {
        list( $name ) = $args;
        WP_CLI::success( "Hello, $name!" );
    }

    /**
     * Do something.
     *
     * ## OPTIONS
     *
     * <something>
     * : Something you want to do
     *
     * ## EXAMPLES
     *
     * wp nanga-deploy chore something
     *
     * @synopsis <something>
     *
     * @param $args
     * @param $assoc_args
     */
    function chore( $args, $assoc_args ) {
        //wp_cache_flush();
        //if ( class_exists( 'TimberLoader' ) ) {
        //TimberCommand::clear_cache_timber();
        //TimberCommand::clear_cache_twig();
        //}
        //WP_CLI::line( 'Line' );
        //WP_CLI::log( 'Log' );
        //WP_CLI::confirm( 'Are you really sure you wanna do this?' );
        $command = WP_CLI::launch( 'git push origin master', true, true );
        WP_CLI::line( $command );
        //write_log( shell_exec( 'git lg' ) );
        //write_log( $args );
        //write_log( $assoc_args );
        //WP_CLI::success( "Code deployed." );
    }
}

WP_CLI::add_command( 'nanga', 'Nanga_CLI' );
