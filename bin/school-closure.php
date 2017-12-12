#!/usr/bin/env php
<?php

/**
 * CLI. Loop through the collection of pages (scrape, extract, sleep).
 *      Then output the "index.json" file.
 *
 * @copyright © Nick Freear, 10-December-2017.
 * @license   MIT
 */

// Environment / configuration -- this will go in a ".env" file !!
require_once __DIR__ . '/../.env.php';

use duzun\hQuery;

// Set the cache path - must be a writable folder.
hQuery::$cache_path = getenv( 'SCX_CACHE_DIR' );

$results = [];

try {
    for ($page = MIN_PAGE; $page <= MAX_PAGE; $page++) {

        $scrape_url = strtr(getenv( 'SCX_SCRAPE_URL' ), [ '{PAGE}' => $page ]);

        print_r( "$page. $scrape_url\n" );

        // GET the document.
        $http_context = stream_context_create([ 'http' => [
            'method' => 'GET', 'user_agent' => AGENT, 'proxy' => PROXY, 'header' => [
                'X-sleep-seconds: ' . getenv( 'SCX_SLEEP_FLOAT' ),
        ] ] ]);

        $htmldoc = hQuery::fromFile( $scrape_url, false, $http_context );

        $headings = $htmldoc->find(getenv( 'SCX_LOOP_SELECTOR' ));

        _verbose([ count( $headings ), $htmldoc->size, $htmldoc->charset ]);

        if ($headings) {

            foreach ($headings as $idx => $heading) {
                preg_match(getenv( 'SCX_ITEM_REGEX' ), $heading->text(), $matches );

                $results[] = [
                    'name'   => _clean($matches[ 'school' ]),
                    'abbr'   => _abbreviate($matches[ 'school' ]),
                    'status' => strtolower($matches[ 'status' ]),
                    'page'   => (int) $page,
                ];
            }
        }

        usleep( getenv( 'SCX_SLEEP_FLOAT' ) * 1000000 );
    }
} catch (\Exception $ex) {
    print_r( $ex->getMessage() );
}

$data = [
    'lang'     => LANG,
    'build_time' => date( 'c' ),
    'location' => getenv( 'SCX_LOCATION' ),
    'home_url' => getenv( 'SCX_LINK_URL' ),
    'legal'    => LEGAL,
    'user_agent' => AGENT,
    'sleep_secs' => (float) getenv( 'SCX_SLEEP_FLOAT' ),
    'pages'   => (int) MAX_PAGE,
    'count'   => count( $results ),
    'schools' => $results,
];

$bytes = file_put_contents( INDEX_JSON, json_encode( $data, JSON_PRETTY_PRINT ));

echo "\nindex.json. Bytes written: $bytes\n";


// ------------------------------------------------------------

// https://stackoverflow.com/questions/9706429/get-first-letter-of-each-word
function _abbreviate( $text ) {
    $text = trim(str_replace( 'The', '', _clean( $text )));
    $words = explode(' ', $text); // "Community College District"
    $acronym = '';

    foreach ($words as $wd) {
        $acronym .= $wd[ 0 ];
    }
    return count( $words ) > 1 ? $acronym : $text;
}

function _clean( $text ) {
    return preg_replace( '/ {2,}/', ' ', trim( $text ));
}

// End.
