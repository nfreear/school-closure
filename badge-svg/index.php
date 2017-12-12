<?php
/**
 *
 * @link  https://img.shields.io/badge/Middleton_Primary-closed-red.svg
 * @link  https://img.shields.io/badge/Middleton_Primaryx-closed-brightgreen.svg
 */

define( 'INDEX_JSON', __DIR__ . '/../index.json' );
define( 'SCHOOL', filter_input( INPUT_GET, 's' ));

$data = json_decode(file_get_contents( INDEX_JSON ));

header( 'X-0-school-name: s=' . SCHOOL );
header( 'X-1-index-json: count=' . count( $data->schools ));

foreach ($data->schools as $idx => $school) {
    if ($school->name === SCHOOL) {
        echo badge_svg($school->name, $school->status, $data->build_time);
        return;
    }
}

echo badge_svg('Not found', '404');

// ---------------------------------------------------------------------

function badge_svg($name = 'Middleton Primary', $stat = 'closed', $time = null) {
    header( 'Content-Type: image/svg+xml; charset=utf-8' );

    $color = 'closed' == $stat ? '#e05d44' : 'open' == $stat ? '#4c1' : '#888';

    return <<<EOT
<svg
  xmlns="http://www.w3.org/2000/svg"
  xmlns:xlink="http://www.w3.org/1999/xlink" width="156" height="20">
  <title>$name: $stat (Updated: $time)</title>
  <linearGradient id="b" x2="0" y2="100%">
    <stop offset="0" stop-color="#bbb" stop-opacity=".1"/>
    <stop offset="1" stop-opacity=".1"/>
  </linearGradient>
  <clipPath id="a">
    <rect width="156" height="20" rx="3" fill="#fff"/>
  </clipPath>
  <g clip-path="url(#a)">
    <path fill="#555" d="M0 0h111v20H0z"/>
    <path fill="$color" d="M111 0h45v20H111z"/><!--  #e05d44 -->
    <path fill="url(#b)" d="M0 0h156v20H0z"/>
  </g>
  <g fill="#fff" text-anchor="middle" font-family="DejaVu Sans,Verdana,Geneva,sans-serif" font-size="110">
    <text x="565" y="150" fill="#010101" fill-opacity=".3" transform="scale(.1)" textLength="1010"
    >$name</text>
    <text x="565" y="140" transform="scale(.1)" textLength="1010"
    >$name</text>
    <text x="1325" y="150" fill="#010101" fill-opacity=".3" transform="scale(.1)" textLength="350"
    >$stat</text>
    <text x="1325" y="140" transform="scale(.1)" textLength="350"
    >$stat</text>
  </g>
</svg>
EOT;
}
