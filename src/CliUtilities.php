<?php namespace Nfreear\SchoolClosure;

/**
 * Commandline (CLI) utilities class.
 *
 * @copyright © Nick Freear, 13-December-2017.
 * @license   MIT
 */

class CliUtilities {

    const CRON_LIST_CMD = 'crontab -l | grep "%s"';
    const CRON_SECURE_REGEX = '/\/(var|home|Users)\/[\w]+/';
    const CRON_COMMENT = '# min hr  dom mon day command.';

    /**
     * @return bool  Is this a commandline request (cPanel or conventional CLI)?
     */
    protected static function ifAnyCli() {
        return 'cli' === php_sapi_name() || (getenv( 'SHELL' ) && getenv( 'MAILTO' ));
    }

    /** Security - kill WEB-based requests!
    */
    public static function abortIfNotCli() {
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
     * @return resource  Return a HTTP stream context.
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
     * @param string       $filename  Write JSON data to this file.
     * @param object|array $data
     * @return int|bool    Bytes written, or FALSE on error.
     */
    public static function fileWriteJson( $filename, $data ) {
        return file_put_contents( $filename, json_encode( $data, JSON_PRETTY_PRINT ));
    }

    /**
     * @return array  Return "cron -l" output, securely. (19 Dec.)
     */
    public static function getCronList( $grep = 'school-closure/b', $secure_replace = null ) {
        $secure_replace = $secure_replace ? $secure_replace : self::CRON_SECURE_REGEX;
        $command = sprintf( self::CRON_LIST_CMD, $grep );  // 'crontab -l | grep ' . $keyword;

        $output = $return_var = null;
        $result[] = self::CRON_COMMENT;

        $last_line = exec( $command, $output, $return_var );

        foreach ($output as $idx => $line) {
            $result[] = preg_replace( $secure_replace, '[command]', $line );
        }
        return $result;
    }

    public static function initExampleSchool() {
        $result = [];
        if ((int) getenv( 'SCX_EXAMPLE' )) {
            $result[] = [
                'name' => 'An Example School',
                'abbr' => 'EX_0',
                'status' => 'closed',
                'page' => 0,
                'is_test' => true,
            ];
        }
        return $result;
    }
}

// End.
