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

use Nfreear\SchoolClosure\CliUtilities as Cli;
use duzun\hQuery;

// Set the cache path - must be a writable folder.
hQuery::$cache_path = getenv( 'SCX_CACHE_DIR' );

$results = [];
$count_open = 0;

try {
    for ($page = MIN_PAGE; $page <= MAX_PAGE; $page++) {

        $scrape_url = strtr(getenv( 'SCX_SCRAPE_URL' ), [ '{PAGE}' => $page ]);

        print_r( "$page. $scrape_url\n" );

        // GET the document.
        $htmldoc = hQuery::fromFile( $scrape_url, false, Cli::createHttpContext() );

        $headings = $htmldoc->find(getenv( 'SCX_LOOP_SELECTOR' ));

        Cli::verbose([ count( $headings ), $htmldoc->size, $htmldoc->charset ]);

        if ($headings) {

            foreach ($headings as $idx => $heading) {
                preg_match(getenv( 'SCX_ITEM_REGEX' ), $heading->text(), $matches );

                $status = strtolower($matches[ 'status' ]);
                $results[] = [
                    'name'   => Cli::clean($matches[ 'school' ]),
                    'abbr'   => Cli::abbreviate($matches[ 'school' ]),
                    'status' => $status,
                    'page'   => (int) $page,
                ];

                $count_open += (int) ( 'open' === $status );
            }
        }

        Cli::floatSleep();
    }
} catch (\Exception $ex) {
    print_r( $ex->getMessage() );
}

$total = count( $results );
$data = [
    'lang'     => LANG,
    'build_time' => gmdate( 'c' ),
    'location' => getenv( 'SCX_LOCATION' ),
    'home_url' => getenv( 'SCX_LINK_URL' ),
    'legal'    => LEGAL,
    'user_agent' => AGENT,
    'sleep_secs' => (float) getenv( 'SCX_SLEEP_FLOAT' ),
    'count'  => [
        'total' => $total,
        'open'  => $count_open,
        'percent' => Cli::percent( $count_open / $total ),
        'pages' => (int) MAX_PAGE,
    ],
    'schools' => $results,
];

$bytes = Cli::fileWriteJson( INDEX_JSON, $data );

echo "\nindex.json. Bytes written: $bytes\n";

// End.
