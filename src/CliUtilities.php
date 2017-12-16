<?php namespace Nfreear\SchoolClosure;

/**
 * CLI utilities class.
 *
 * @copyright © Nick Freear, 13-December-2017.
 * @license   MIT
 */

class CliUtilities {

    protected static function ifAnyCli() {
        return 'cli' === php_sapi_name() || (getenv( 'SHELL' ) && getenv( 'MAILTO' ));
    }

    public static function abortIfNotCli() {
        /* header( 'X-SAPI: ' . php_sapi_name() );
        header( 'X-getenv-sh: ' . getenv( 'SHELL' ));
        header( 'X-env-sh: ' . $_ENV[ 'SHELL' ]); */

        if ( ! self::ifAnyCli() ) {
            ob_end_clean();
            header( 'HTTP/1.1 404' );
            header( 'X-sapi: ' . php_sapi_name() );
            die( 'Not found (404.9)' );
        }
    }

    public static function isVerbose() {
        return ( isset( $argv ) && $argv[ $argc - 1 ] === '-vvv' )
            || filter_input( INPUT_GET, '-vvv' );
    }

    public static function verbose( $obj ) {
        if ( self::isVerbose() ) {
            echo json_encode( $obj, JSON_PRETTY_PRINT ) . "\n";
        }
    }

    /**
     * Create a HTTP context.
     * @return resource  A stream context.
     */
    public static function createHttpContext() {
        return stream_context_create([
            'http' => [
                'method' => 'GET',
                'user_agent' => AGENT,
                'proxy' => PROXY,
                'header' => [
                    'X-sleep-seconds: ' . getenv( 'SCX_SLEEP_FLOAT' ),
                ]
            ]
        ]);
    }

    /**
     * @param  string $name  Name to abbreviate.
     * @return string
     * @link   https://stackoverflow.com/questions/9706429/get-first-letter-of-each-word
     */
    public static function abbreviate( $name ) {
        $name = trim(str_replace( 'The', '', self::clean( $name )));
        $words = explode( ' ', $name ); // "Community College District"
        $acronym = '';

        foreach ($words as $wd) {
            $acronym .= $wd[ 0 ];
        }
        return count( $words ) > 1 ? $acronym : $name;
    }

    /**
     * @param  string $text  Text to clean.
     * @return string
     */
    public static function clean( $text ) {
        return preg_replace( '/ {2,}/', ' ', trim( $text ));
    }

    /**
     * @param  float  $fraction
     * @return string
     */
    public static function percent( $fraction ) {
        return ( 100 * $fraction ) . '%';
    }

    public static function floatSleep() {
        return usleep( getenv( 'SCX_SLEEP_FLOAT' ) * 1000000 );
    }

    /**
     * Write JSON to a file.
     * @param string       $filename
     * @param object|array $data
     * @return int|bool    Bytes written, or FALSE.
     */
    public static function fileWriteJson( $filename, $data ) {
        return file_put_contents( $filename, json_encode( $data, JSON_PRETTY_PRINT ));
    }
}

// End.
