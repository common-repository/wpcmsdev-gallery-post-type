<?php
/*
Plugin Name: wpCMSdev Gallery Post Type
Plugin URI:  http://wpcmsdev.com/plugins/gallery-post-type/
Description: Registers a "Galleries" custom post type.
Author:      wpCMSdev
Author URI:  http://wpcmsdev.com
Version:     1.0
Text Domain: wpcmsdev-gallery-post-type
Domain Path: /languages
License:     GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


Copyright (C) 2014  wpCMSdev

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/


/**
 * Registers the "gallery" post type.
 */
function wpcmsdev_galleries_post_type_register() {

	$labels = array(
		'name'               => __( 'Galleries',                    'wpcmsdev-gallery-post-type' ),
		'singular_name'      => __( 'Gallery',                      'wpcmsdev-gallery-post-type' ),
		'all_items'          => __( 'All Galleries',                'wpcmsdev-gallery-post-type' ),
		'add_new'            => _x( 'Add New', 'gallery',           'wpcmsdev-gallery-post-type' ),
		'add_new_item'       => __( 'Add New Gallery',              'wpcmsdev-gallery-post-type' ),
		'edit_item'          => __( 'Edit Gallery',                 'wpcmsdev-gallery-post-type' ),
		'new_item'           => __( 'New Gallery',                  'wpcmsdev-gallery-post-type' ),
		'view_item'          => __( 'View Gallery',                 'wpcmsdev-gallery-post-type' ),
		'search_items'       => __( 'Search Galleries',             'wpcmsdev-gallery-post-type' ),
		'not_found'          => __( 'No galleries found.',          'wpcmsdev-gallery-post-type' ),
		'not_found_in_trash' => __( 'No galleries found in Trash.', 'wpcmsdev-gallery-post-type' ),
	);

	$args = array(
		'labels'        => $labels,
		'menu_icon'     => 'dashicons-format-gallery',
		'menu_position' => 5,
		'public'        => true,
		'has_archive'   => false,
		'rewrite'       => array( 'slug' => _x( 'gallery', 'gallery single post url slug', 'wpcmsdev-gallery-post-type' ) ),
		'supports'      => array(
			'author',
			'custom-fields',
			'excerpt',
			'editor',
			'page-attributes',
			'revisions',
			'thumbnail',
			'title',
		),
	);

	$args = apply_filters( 'wpcmsdev_galleries_post_type_args', $args );

	register_post_type( 'gallery_page', $args );

}
add_action( 'init', 'wpcmsdev_galleries_post_type_register' );


/**
 * Flushes the site's rewrite rules.
 */
function wpcmsdev_galleries_rewrite_flush() {

	wpcmsdev_galleries_post_type_register();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'wpcmsdev_galleries_rewrite_flush' );


/**
 * Loads the translation files.
 */
function wpcmsdev_galleries_load_translations() {

	load_plugin_textdomain( 'wpcmsdev-gallery-post-type', false, dirname( plugin_basename( __FILE__ ) ) ) . '/languages/';
}
add_action( 'plugins_loaded', 'wpcmsdev_galleries_load_translations' );


/**
 * Initializes additional functionality when used with a theme that declares support for the plugin.
 */
function wpmcsdev_galleries_additional_functionality_init() {

	if ( current_theme_supports( 'wpcmsdev-gallery-post-type' ) ) {
		add_action( 'admin_enqueue_scripts',                   'wpcmsdev_galleries_manage_posts_css' );
		add_action( 'manage_gallery_page_posts_custom_column', 'wpcmsdev_galleries_manage_posts_columm_content' );
		add_filter( 'manage_edit-gallery_page_columns',        'wpcmsdev_galleries_manage_posts_columns' );
	}
}
add_action( 'after_setup_theme', 'wpmcsdev_galleries_additional_functionality_init', 11 );


/**
 * Registers custom columns for the Manage Galleries admin page.
 */
function wpcmsdev_galleries_manage_posts_columns( $columns ) {

	$column_order     = array( 'order'     => __( 'Order', 'wpcmsdev-gallery-post-type' ) );
	$column_thumbnail = array( 'thumbnail' => __( 'Thumbnail', 'wpcmsdev-gallery-post-type' ) );

	$columns = array_slice( $columns, 0, 2, true ) + $column_thumbnail + $column_order + array_slice( $columns, 2, null, true );

	return $columns;
}


/**
 * Outputs the custom column content for the Manage Galleries admin page.
 */
function wpcmsdev_galleries_manage_posts_columm_content( $column ) {

	global $post;

	switch( $column ) {

		case 'order':
			$order = $post->menu_order;
			if ( 0 === $order ) {
				echo '<span class="default-value">' . $order . '</span>';
			} else {
				echo $order;
			}
			break;

		case 'thumbnail':
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( 'thumbnail' );
			} else {
				echo '&#8212;';
			}
			break;
	}
}


/**
 * Outputs the custom columns CSS used on the Manage Galleries admin page.
 */
function wpcmsdev_galleries_manage_posts_css() {

	global $pagenow, $typenow;
	if ( ! ( 'edit.php' == $pagenow && 'gallery_page' == $typenow ) ) {
		return;
	}

?>
<style>
	.edit-php .posts .column-order,
	.edit-php .posts .column-thumbnail {
		width: 10%;
	}
	.edit-php .posts .column-thumbnail img {
		width: 50px;
		height: auto;
	}
	.edit-php .posts .column-order .default-value {
		color: #bbb;
	}
</style>
<?php
}
