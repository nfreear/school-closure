#!/usr/bin/env php
<?php

/**
 * Loop through the collection of pages (scrape, extract, sleep), then output an "index.json" file.
 *
 * @copyright Nick Freear, 10-December-2017.
 */

// Environment / configuration -- this will go in a ".env" file !!
require_once __DIR__ . '/../.env.php';

// Optionally use namespaces (PHP >= 5.3.0 only)
use duzun\hQuery;

// Set the cache path - must be a writable folder
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

        /* $htmldoc = hQuery::fromUrl( $scrape_url, [
            'Connection' => 'Keep-Alive',
            'Accept' => 'text/html,application/xhtml+xml;q=0.9,*-/*;q=0.8',
            'User-Agent' => USER_AGENT ]); */

        $headings = $htmldoc->find(getenv( 'SCX_LOOP_SELECTOR' ));

        // var_dump( count( $headings ), $htmldoc->size, $htmldoc->charset );

        if ($headings) {

            foreach ($headings as $idx => $heading) {
                // print_r( $heading->text() . "\n" );

                preg_match(getenv( 'SCX_ITEM_REGEX' ), $heading->text(), $matches );

                $results[] = [
                    'name'   => trim($matches[ 'school' ]),
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

// End.
