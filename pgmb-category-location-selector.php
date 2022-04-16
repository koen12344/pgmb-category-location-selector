<?php
/**
 * Plugin Name:     PGMB Category Location Selector
 * Plugin URI:      https://tycoonmedia.net
 * Description:     Choose to which GMB location your post should be published based on which category the post or item is in.
 * Author:          Koen Reus
 * Author URI:      https://koenreus.com
 * Text Domain:     pgmb-category-location-selector
 * Domain Path:     /languages
 * Version:         0.1.3
 *
 * @package         Pgmb_Category_Location_Selector
 */

// Your code starts here.


function pgmb_add_category_location_selector($taxonomies){
	require_once(__DIR__.'/src/LocationTaxonomyField.php');

	if(class_exists('PGMB_LocationTaxonomyField')) {
		$field = PGMB_LocationTaxonomyField::init(
			'category',
			'pgmb_term_location',
			'business_selector',
			__('Category Location', 'post-to-google-my-business'),
			__('Choose which GMB location posts will be published to when they are in this category')
		);
	}


	return $taxonomies;
}
add_filter('mbp_taxonomies', 'pgmb_add_category_location_selector');


function pgmb_change_cat_on_save($post_id, $post, $update){
	$parent_id = get_post_parent($post_id);
	$categories = get_the_category($parent_id);
	if(empty($categories)){
		return;
	}

	$category = $categories[0];

	$category_locations = get_term_meta($category->term_id, 'pgmb_term_location', true);

	if(empty($category_locations)){
		return;
	}

	$form_fields = get_post_meta($post_id, 'mbp_form_fields', true);
	$category_locations = is_array($category_locations)? $category_locations : [$category_locations];

	//Upgrade if old format
	if(isset($category_locations[0]) && !is_array($category_locations[0])){
		preg_match('/\d+/', $category_locations[0], $matches);

		if(!isset($matches[0])){ return ; }

		$user_id = $matches[0];
		$category_locations = [$user_id => $category_locations];
	}

	$form_fields['mbp_selected_location'] = $category_locations;

	update_post_meta($post_id, 'mbp_form_fields', $form_fields);
}


add_filter('save_post_mbp-google-subposts', 'pgmb_change_cat_on_save', 9, 3);

function pgmb_skip_on_no_categories($subpost){
	$post_id = $subpost->get_parent();
	$categories = get_the_category($post_id);
	if(empty($categories)){
		return false;
	}
	$category = $categories[0];

	$category_location = get_term_meta($category->term_id, 'pgmb_term_location', true);

	if(empty($category_location)){
		return false;
	}
	return $subpost;
}

add_filter('mbp_autopost_before_insert_subpost', 'pgmb_skip_on_no_categories');
