<?php
/**
 * Environment / configuration -- this will go in a ".env" file !!
 */

// IMPORTANT: Play nice!
putenv( 'SCX_SLEEP_FLOAT=4.0' );

putenv( 'SCX_LOCATION=Milton Keynes' );
putenv( 'SCX_LINK_URL=https://www.milton-keynes.gov.uk/closures' );
putenv( 'SCX_SCRAPE_URL=https://www.milton-keynes.gov.uk/closures?page={PAGE}#results' );
putenv( 'SCX_MIN_PAGE=1'  );
putenv( 'SCX_MAX_PAGE=16' );
putenv( 'SCX_LOOP_SELECTOR=.content.right article h3' );
putenv( 'SCX_ITEM_REGEX=' . '/(?P<school>[\w ]+) +(?P<status>OPEN|CLOSED)/i' );
putenv( 'SCX_CACHE_DIR=/path/to/school-closure-scraper/cache' );

// Other configuration.
define( 'USER_AGENT', 'SchoolClosure/1.0-beta +https://github.com/nfreear#!-school-closure-scraper#bot');
define( 'LEGAL', 'This data remains the property of the website publisher. Use at your own risk. We accept no liability for losses.');
define( 'INDEX_JSON', __DIR__ . '/index.json' );

// End.
