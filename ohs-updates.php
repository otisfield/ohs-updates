<?php
/*
Plugin Name: OHS Updates Plugin
Description: Updates Plugin
Author: Derek Dorr
Version: 0.1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

register_activation_hook( __FILE__, 'ohsupdates_activate' );

function ohsupdates_activate() {
	flush_rewrite_rules();
}

add_action('init', 'updates_type');

function updates_type() {

	$labels = array(
		'name' => _x('Updates', 'post type general name'),
		'singular_name' => _x('Update', 'post type singular name'),
		'add_new' => _x('Add New', 'updates'),
		'add_new_item' => __('Add New Update'),
		'edit_item' => __('Edit Update'),
		'new_item' => __('New Update'),
		'view_item' => __('View Update'),
		'search_items' => __('Search Updates'),
		'not_found' =>  __('No updates found'),
		'not_found_in_trash' => __('No updates found in Trash'), 
		'parent_item_colon' => '',
		'menu_name' => _x('Updates', 'menu name')
	);
	
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 3,
		'rewrite' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'supports' => array('title','editor','thumbnail'),
		'has_archive' => true,
		'query_var' => true,
		'show_in_rest' => true,
		'rest_base' => 'updates',
		'rest_controller_class' => 'WP_REST_Posts_Controller'
	);
	
	register_post_type('updates',$args);
	
}

/**
 * Wordpress API 
 */
 
add_action( 'rest_api_init', 'ohsupdates_register_api_hooks' );

function ohsupdates_register_api_hooks() {
	
	/**
	 * Add Fields to Updates API
	 */
	 
	register_rest_field( 'updates', 'media', array(
		'get_callback' => 'updates_register_media',
		'update_callback' => null,
		'schema' => null
	) );
	
}

function updates_register_media($object, $field_name, $request) {
	$featuredImageId = get_post_thumbnail_id($object['id']);
	
	$media = null;
	
	if ($featuredImageId) {
		$fullSizeFeatured = wp_get_attachment_image_src( $featuredImageId, 'full', false);
		$largeFeatured = wp_get_attachment_image_src( $featuredImageId, 'large', false);
		$mediumFeatured = wp_get_attachment_image_src( $featuredImageId, 'medium', false);
		$thumbnailFeatured = wp_get_attachment_image_src( $featuredImageId, 'thumbnail', false);
		
		$media = array(
			'id' => $featuredImageId,
			'url' => $fullSizeFeatured[0],
			'width' => $fullSizeFeatured[1],
			'height' => $fullSizeFeatured[2],
			'thumbnail' => array(
				'url' => $thumbnailFeatured[0],
				'width' => $thumbnailFeatured[1],
				'height' => $thumbnailFeatured[2]
			),
			'medium' => array(
				'url' => $mediumFeatured[0],
				'width' => $mediumFeatured[1],
				'height' => $mediumFeatured[2]
			),
			'large' => array(
				'url' => $largeFeatured[0],
				'width' => $largeFeatured[1],
				'height' => $largeFeatured[2]			
			)
		);
	}
	
	return $media;
}
?>
