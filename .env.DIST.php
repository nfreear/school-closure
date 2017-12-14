<?php
/**
 * Environment / configuration -- this will go in a ".env" file !!
 */

require_once __DIR__ . '/src/CliUtilities.php';

use Nfreear\SchoolClosure\CliUtilities as Cli;

Cli::abortIfNotCli();

// IMPORTANT: Play nice!
putenv( 'SCX_SLEEP_FLOAT=4.0' );

putenv( 'SCX_LOCATION=Milton Keynes, England' );
putenv( 'SCX_LINK_URL=https://www.milton-keynes.gov.uk/closures' );
putenv( 'SCX_SCRAPE_URL=https://www.milton-keynes.gov.uk/closures?page={PAGE}#results' );
putenv( 'SCX_MIN_PAGE=1' );
putenv( 'SCX_MAX_PAGE=2' );  // '..=16';
putenv( 'SCX_LOOP_SELECTOR=.content.right article h3' );
putenv( 'SCX_ITEM_REGEX=' . '/(?P<school>[\w, \']+) +(?P<status>OPEN|CLOSED)/i' );
putenv( 'SCX_CACHE_DIR=' . __DIR__ . '/cache' );  // /Path/to/school-closure/cache

// Other configuration.
define( 'PROXY', getenv( 'http_proxy' ));
define( 'AGENT', 'SchoolClosure/1.0-beta +https://github.com/nfreear/school-closure#bot');
define( 'LEGAL', 'This data remains the property of the website publisher. Use at your own risk. We accept no liability for losses.');
define( 'INDEX_JSON', __DIR__ . '/index.json' );
define( 'MIN_PAGE', getenv( 'SCX_MIN_PAGE' ));
define( 'MAX_PAGE', getenv( 'SCX_MAX_PAGE' ));
define( 'LANG', 'en-GB' );
define( 'VERBOSE', $argv[ $argc - 1] === '-vvv' );

Cli::verbose([ getenv( 'SCX_SCRAPE_URL' ), getenv( 'SCX_LOOP_SELECTOR' ), getenv( 'SCX_ITEM_REGEX' ) ]);

// Either use commposer (or include this file):
// include_once '/path/to/libs/hquery.php';
require_once __DIR__ . '/vendor/autoload.php';

// End.
