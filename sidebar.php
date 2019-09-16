<?php
/*
// Load Sidebar Functions in the old way
//Display Metabox without sidebar
function hello_gutenberg_add_meta_box() {
	add_meta_box( 
		'hello_gutenberg_meta_box', 
		__( 'Hello Gutenberg Meta Box', 'seorefreshlink-gutenberg' ), 
		'hello_gutenberg_metabox_callback',
		null,
		'side',
		'low',
		'post',
		array(
			'__back_compat_meta_box' => false,
		)
	);
}
add_action( 'add_meta_boxes', 'hello_gutenberg_add_meta_box' );

function hello_gutenberg_metabox_callback( $post ) {
	$value = get_post_meta( $post->ID, '_seorefresh_link_field', true );
	?>
	<label for="seorefresh_link_field"><?php _e( 'What\'s your name?', 'seorefreshlink-gutenberg' ) ?></label>
	<input type="text" name="seorefresh_link_field" id="seorefresh_link_field" value="<?php echo $value ?>" />
	<?php
}
*/

/**
 * Save Hello Gutenberg Metabox
 */
function seorefreshlink_save_postdata( $post_id ) {
	if ( array_key_exists( 'seorefresh_link_field', $_POST ) ) {
		update_post_meta( $post_id, '_seorefresh_link_field', $_POST['seorefresh_link_field'] );
	}
	if ( array_key_exists( 'seorefresh_link_field_checker', $_POST ) ) {
		update_post_meta( $post_id, '_seorefresh_link_field_checker', $_POST['seorefresh_link_field_checker'] );
	}
}
add_action( 'save_post', 'seorefreshlink_save_postdata' );

/**
 * Register Hello Gutenberg Meta Field to Rest API
 */
function seorefreshlink_register_meta() {
	register_meta(
		'post', '_seorefresh_link_field', array(
			'type'			=> 'string',
			'single'		=> true,
			'show_in_rest'	=> true,
		)
	);
	register_meta(
		'post', '_seorefresh_link_field_checker', array(
			'type'			=> 'string',
			'single'		=> true,
			'show_in_rest'	=> true,
		)
	);
}
add_action( 'init', 'seorefreshlink_register_meta' );


/**
 * Register Hello Gutenberg Metabox to Rest API
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
add_action( 'rest_api_init', 'seorefreshlink_api_posts_meta_field' );

/**
 * Hello Gutenberg REST API Callback for Gutenberg
 */
function seorefreshlink_update_callback( $data ) {
	return update_post_meta( $data['id'], $data['key'], $data['value'] );
}