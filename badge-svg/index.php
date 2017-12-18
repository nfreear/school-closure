<?php
/**
 * WEB. Output an SVG status badge.
 *
 * @link  http://localhost:8000/badge-svg/?abbr=MP
 *
 * @copyright © Nick Freear, 12-December-2017.
 * @license   MIT
 *
 * @link  https://img.shields.io/badge/Middleton_Primary-closed-red.svg
 * @link  https://img.shields.io/badge/Middleton_PrimaryMM-closed-brightgreen.svg
 */

define( 'ABBR', filter_input( INPUT_GET, 'abbr' ));
define( 'SCHOOL', filter_input( INPUT_GET, 's' ));
define( 'INDEX_JSON', __DIR__ . '/../index.json' );
define( 'CL_GREEN', '#4c1' );
define( 'CL_RED',  '#e05d44' );
define( 'CL_GREY', '#999' );

$data = json_decode(file_get_contents( INDEX_JSON ));

header( 'X-0-school-name: s=' . SCHOOL );
header( 'X-1-abbr: abbr=' . ABBR );
header( 'X-2-index-json: count=' . count( $data->schools ));

foreach ($data->schools as $idx => $school) {
    if ($school->abbr === ABBR || $school->name === SCHOOL) {
        echo badge_svg( $school->name, $school->status, $data->build_time );
        return;
    }
}

echo badge_svg( 'Not found', '404' );

// ---------------------------------------------------------------------

function badge_svg( $name = 'Middleton Primary', $stat = 'closed', $time = null ) {
    header( 'Content-Type: image/svg+xml; charset=utf-8' );

    $color = 'closed' == $stat ? CL_RED : ( 'open' == $stat ? CL_GREEN : CL_GREY );

    return <<<EOT
<svg
  xml:lang="en"
  xmlns="http://www.w3.org/2000/svg"
  xmlns:xlink="http://www.w3.org/1999/xlink" width="174" height="20"
  >
  <title>$name: $stat (Updated: $time)</title>

  <linearGradient id="b" x2="0" y2="100%">
    <stop offset="0" stop-color="#bbb" stop-opacity=".1"/>
    <stop offset="1" stop-opacity=".1"/>
  </linearGradient>

  <clipPath id="a">
    <rect width="174" height="20" rx="3" fill="#fff"/>
  </clipPath>

  <g clip-path="url(#a)">
    <path fill="#555" d="M0 0h129v20H0z"/>
    <path fill="$color" d="M129 0h45v20H129z"/><!--  #e05d44 -->
    <path fill="url(#b)" d="M0 0h174v20H0z"/>
  </g>

  <g fill="#fff" text-anchor="middle" font-family="DejaVu Sans,Verdana,Geneva,sans-serif" font-size="110">
    <text x="655" y="150" fill="#010101" fill-opacity=".3" transform="scale(.1)" textLength="1190"
    >$name</text>
    <text x="655" y="140" transform="scale(.1)" textLength="1190"
    >$name</text>
    <text x="1505" y="150" fill="#010101" fill-opacity=".3" transform="scale(.1)" textLength="350"
    >$stat</text>
    <text x="1505" y="140" transform="scale(.1)" textLength="350"
    >$stat</text>
  </g>
</svg>
EOT;
}
