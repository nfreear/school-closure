#!/usr/bin/env php
<?php

/**
 * Loop through the collection of pages (scrape, extract, sleep), then output an "index.json" file.
 *
 * @copyright Nick Freear, 10-December-2017.
 */

// Environment / configuration -- this will go in a ".env" file !!
require_once __DIR__ . '/../.env.php';

// Either use commposer, either include this file:
# include_once '/path/to/libs/hquery.php';
require_once __DIR__ . '/../vendor/autoload.php';

// Optionally use namespaces (PHP >= 5.3.0 only)
use duzun\hQuery;

// Set the cache path - must be a writable folder
# hQuery::$cache_path =  "/path/to/cache";
hQuery::$cache_path = getenv( 'SCX_CACHE_DIR' );

# var_dump(getenv( 'SCX_SCRAPE_URL' ), getenv( 'SCX_LOOP_SELECTOR' ), getenv( 'SCX_ITEM_REGEX' ));

$results = [];

try {
    for ($page = getenv('SCX_MIN_PAGE'); $page <= getenv('SCX_MAX_PAGE'); $page++) {
        print_r( "$page. " );

        $scrape_url = strtr(getenv( 'SCX_SCRAPE_URL' ), [ '{PAGE}' => $page ]);

        var_dump( $scrape_url );

        // GET the document.
        $html = hQuery::fromUrl( $scrape_url, [
            'Connection' => 'Keep-Alive',
            'Accept' => 'text/html,application/xhtml+xml;q=0.9,*/*;q=0.8',
            'User-Agent' => USER_AGENT ]);

        $headings = $html->find(getenv( 'SCX_LOOP_SELECTOR' ));

        // var_dump( count( $headings ), $html->size, $html->charset );

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

        usleep(( getenv( 'SCX_SLEEP_FLOAT' ) + mt_rand(0, 1)) * 1000000 );
    }
} catch (\Exception $ex) {
    print_r( $ex->printMessage );
}

$data = [
    'build_time' => date( 'c' ),
    'location' => getenv( 'SCX_LOCATION' ),
    'home_url' => getenv( 'SCX_LINK_URL' ),
    'legal'    => LEGAL,
    'user_agent' => USER_AGENT,
    'schools' => $results,
];

$bytes = file_put_contents( INDEX_JSON, json_encode( $data, JSON_PRETTY_PRINT ));

echo "\nindex.json. Bytes written: $bytes\n";

// End.
