<?php
/**
 * Plugin Name: Decapitate WordPress
 * Plugin URI: https://github.com/CalebBarnes
 * Description: Tweaks for Headless WP
 * Version: 1.0.0
 * Author: Caleb Barnes
 * Author URI: https://github.com/CalebBarnes
 */

register_activation_hook( __FILE__, 'as8765da6sd_require_acf_plugin' );
function as8765da6sd_require_acf_plugin(){

	if (!is_plugin_active('advanced-custom-fields-pro/acf.php') && !is_plugin_active('advanced-custom-fields/acf.php') && current_user_can( 'activate_plugins' )) {
        wp_die('Decapitate WordPress requires ACF to be installed and active. <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
	}

}

if( function_exists('acf_add_options_sub_page') ) {
    acf_add_options_sub_page([
        'page_title' => "Decapitate WordPress",
        'menu_title' => "Decapitate WordPress",
        'menu_slug'  => "wp-headless-tweaks",
        'parent_slug' => "options-general.php",
        'show_in_graphql' => true,
    ]);

}

if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	'key' => 'group_600a215c32748',
	'title' => 'Settings',
	'fields' => array(
		array(
			'key' => 'field_600a216be0e77',
			'label' => 'Decapitate WordPress',
			'name' => 'decapitate_wordpress',
			'type' => 'true_false',
			'instructions' => 'Redirects the original WP front end to /wp-admin, essentially removing the WP front end.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'message' => '',
			'default_value' => 1,
			'ui' => 0,
			'ui_on_text' => '',
			'ui_off_text' => '',
		),
		array(
			'key' => 'field_600a21b56cd71',
			'label' => 'Frontend URL',
			'name' => 'frontend_url',
			'type' => 'url',
			'instructions' => 'Replaces the "Visit site" link in the admin menu and the "View page" links.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'wp-headless-tweaks',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
));

endif;


add_action('admin_bar_menu', 'asd8i7f6a87sdf_customize_my_wp_admin_bar', 80);
function asd8i7f6a87sdf_customize_my_wp_admin_bar($wp_admin_bar)
{
	if ( function_exists('get_field')) {
		
		$frontend_url = get_field('frontend_url', 'option');

		if ($frontend_url) {
			//Get a reference to the view-site node to modify.
			$view_site_node = $wp_admin_bar->get_node('view-site');
			if ($view_site_node) {
				
				$view_site_node->href = $frontend_url;
				//Change target
				$view_site_node->meta['target'] = '_blank';
				//Update Node.
				$wp_admin_bar->add_node($view_site_node);
			}
			
			//Get a reference to the site-name node to modify.
			$site_name_node = $wp_admin_bar->get_node('site-name');
			if ($site_name_node) {
				$site_name_node->href = $frontend_url;
				//Change target
				$site_name_node->meta['target'] = '_blank';
				//Update Node.
				$wp_admin_bar->add_node($site_name_node);
			}

		}
	}
}

// redirect to admin page unless preview
add_action("template_redirect", function() {
   if ( function_exists('get_field')) {
	    $decapitate_wordpress = get_field('decapitate_wordpress', 'option');
		if (!isset($_GET['preview']) && $decapitate_wordpress) {
			wp_redirect('/wp-admin');
		}
   }
});

function custom_frontend_url_link( $permalink, $post ) { 
	if ( function_exists('get_field')) {
		$frontend_url = get_field('frontend_url', 'option');
		$custom_permalink = str_replace( home_url(), $frontend_url,  $permalink );

		return $custom_permalink; 
	} else {
		return $permalink;
	}
}; 
			
add_filter('init', 'add_frontend_url_filters');

function add_frontend_url_filters() {
	$post_types = get_post_types();

	$filtered_post_types = array_filter($post_types, function ($item) {
		$excluded_post_types = [
			'attachment', 
			'revision', 
			'nav_menu_item', 
			'custom_css', 
			'customize_changeset', 
			'oembed_cache',
			'user_request',
			'wp_block',
			'acf-field-group',
			'acf-field'
		];

		if (!in_array($item, $excluded_post_types)) {
			return $item;
		}
	});

	foreach ($filtered_post_types as $post_type) {
		add_filter($post_type . '_link', 'custom_frontend_url_link', 10, 2);
	}
}