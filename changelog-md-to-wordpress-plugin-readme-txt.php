<?php

$file_changelog_md = 'CHANGELOG.md';
$file_readme_txt   = 'readme.txt';

$content_changelog_md = \file_get_contents( __DIR__ . '/' . $file_changelog_md );
$content_readme_txt   = \file_get_contents( __DIR__ . '/' . $file_readme_txt );

$marker_start = '<!-- Start changelog -->';
$marker_end   = '<!-- End changelog -->';

$md_changelog_marker_start_position = \strpos( $content_changelog_md, $marker_start );
$md_changelog_marker_end_position   = \strpos( $content_changelog_md, $marker_end );

$txt_changelog_marker_start_position = \strpos( $content_readme_txt, $marker_start );
$txt_changelog_marker_end_position  = \strpos( $content_readme_txt, $marker_end );

if ( false === $md_changelog_marker_start_position ) {
	echo 'No changelog start marker found in CHANGELOG.md file.';

	exit( 1 );
}

if ( false === $md_changelog_marker_end_position ) {
	echo 'No changelog end marker found in CHANGELOG.md file.';

	exit( 1 );
}

if ( false === $txt_changelog_marker_start_position ) {
	echo 'No changelog start marker found in readme.txt file.';

	exit( 1 );
}

if ( false === $txt_changelog_marker_end_position ) {
	echo 'No changelog end marker found in readme.txt file.';

	exit( 1 );
}

$md_changelog_marker_start_position += \strlen( $marker_start );

$changelog_content = \substr(
	$content_changelog_md,
	$md_changelog_marker_start_position,
	$md_changelog_marker_end_position - $md_changelog_marker_start_position
);

$changelog_content_updated = \preg_replace_callback(
    '/^(#{1,6})\s/m',
    function ( $matches ) {
        return \str_repeat( '#', \strlen( $matches[1] ) + 1 ) . ' ';
    },
    $changelog_content
);

$txt_changelog_marker_start_position += \strlen( $marker_start );

$content_readme_txt_updated = \substr( $content_readme_txt, 0, $txt_changelog_marker_start_position ) . $changelog_content_updated . \substr( $content_readme_txt, $txt_changelog_marker_end_position );

\file_put_contents( __DIR__ . '/' . $file_readme_txt, $content_readme_txt_updated );
