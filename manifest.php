<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$manifest = [];

$manifest['name']        = __( 'Builder Sync', 'fw' );
$manifest['slug']        = 'unysonplus-builder-sync';
$manifest['description'] = __(
	'Keeps the site design in step between Unyson+ Theme Settings and another page builder installed on the same site — colors, fonts, heading styles, links, container width, background and the primary button. Edit either side and the other follows.',
	'fw'
);

$manifest['version']     = '1.0.0';
$manifest['display']     = true;
$manifest['standalone']  = true;

// Repository Info
$manifest['github_update'] = 'UnysonPlus/UnysonPlus-Builder-Sync-Extension';
$manifest['github_repo']   = 'https://github.com/UnysonPlus/UnysonPlus-Builder-Sync-Extension';
$manifest['github_branch'] = 'main';

// Author Info
$manifest['author']     = 'UnysonPlus';
$manifest['author_uri'] = 'https://www.lastimosa.com.ph/unysonplus';

// Meta
$manifest['license']      = 'GPL-2.0-or-later';
$manifest['text_domain']  = 'fw';
$manifest['requires_php'] = '7.4';
$manifest['requires_wp']  = '5.8';

/**
 * Changelog
 * -----------------------------------------------------------------------------
 * 1.0.0 - Initial release. Two-way design sync between Unyson+ Theme Settings
 *         and a page builder's global settings, through one builder-neutral
 *         "design" model so each builder is a small provider class. Ships the
 *         first provider: color presets, heading/body fonts, H1–H6, links,
 *         container width, site background, the primary button and Text Styles
 *         map onto the builder's global colors, global fonts and theme style.
 *         Saving either side pushes to the other; a stored identity map keeps
 *         renamed colors attached to the same global color. Providers register
 *         through the `fw_builder_sync_providers` filter.
 */
