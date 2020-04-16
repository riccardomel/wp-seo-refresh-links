<?php
/**
 * Save Post Data Old Style Fallbacks
 */

function seorefreshlink_save_postdata( $post_id ) {
	if ( array_key_exists( 'seorefresh_link_field', $_POST ) ) {
		update_post_meta( $post_id, '_seorefresh_link_field', $_POST['seorefresh_link_field'] );
	}
	if ( array_key_exists( 'seorefresh_link_field_checker', $_POST ) ) {
		update_post_meta( $post_id, '_seorefresh_link_field_checker', $_POST['seorefresh_link_field_checker'] );
	}
}
add_action( 'save_post', 'seorefreshlink_save_postdata', 5);

/**
 * Register  Meta Field to Rest API
 */
function seorefreshlink_register_meta() {
	register_meta(
		'post', '_seorefresh_link_field', array(
			'type'			=> 'string',
			'single'		=> true,
			'show_in_rest'	=> true,
			'public' => true,
			'rest_base' => 'custom',
			'auth_callback' => function () {
				return current_user_can('edit_posts');
			  }
		)
	);
	register_meta(
		'post', '_seorefresh_link_field_checker', array(
			'type'			=> 'string',
			'single'		=> true,
			'show_in_rest'	=> true,
			'public' => true,
			'rest_base' => 'custom',
			'auth_callback' => function () {
				return current_user_can('edit_posts');
			  }
		)
	);
}
add_action( 'init', 'seorefreshlink_register_meta' );

/**
 * Register Custom Save Route API
 */
function seorefreshlink_api_posts_meta_field() {
	register_rest_route(
		'seorefreshlink-gutenberg/v1', '/update-meta', array(
			'methods'  => 'POST',
			'callback' => 'seorefreshlink_update_callback',
			'args'     => array(
				'id' => array(
					'sanitize_callback' => 'absint',
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'seorefreshlink_api_posts_meta_field',15 );

/**
 * Callback for save the post Meta
 */
function seorefreshlink_update_callback( $data ) {
	return	update_post_meta( $data['id'], $data['key'], $data['value'] );
}