<?php
/**
 * Plugin Name: Seo Refresh Link
 * Plugin URI: https://riccardomel.com
 * Description: Seo Refresh Link
 * Author: Riccardo Mel
 * Author URI: https://riccardomel.com
 * Version: 1.0.1
 * License: GPL2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package seo-refresh-link
 */

//  Exit if accessed directly.
defined('ABSPATH') || exit;

// Load Sidebar Functions 
require_once( plugin_dir_path( __FILE__ ) . 'sidebar.php' );

/**
 * Enqueue front end and editor JavaScript and CSS
 */
function hello_gutenberg_scripts() {
	$blockPath = '/dist/block.js';
	$stylePath = '/dist/block.css';

	// Enqueue the bundled block JS file
	wp_enqueue_script(
		'seorefreshlink-gutenberg-block-js',
		plugins_url( $blockPath, __FILE__ ),
		[ 'wp-i18n', 'wp-blocks', 'wp-edit-post', 'wp-element', 'wp-editor', 'wp-components', 'wp-data', 'wp-plugins', 'wp-edit-post', 'wp-api' ],
		filemtime( plugin_dir_path(__FILE__) . $blockPath )
	);

	// Enqueue frontend and editor block styles
	wp_enqueue_style(
		'seorefreshlink-gutenberg-block-css',
		plugins_url( $stylePath, __FILE__ ),
		'',
		filemtime( plugin_dir_path(__FILE__) . $stylePath )
	);

}

// Hook scripts function into block editor hook
add_action( 'enqueue_block_assets', 'hello_gutenberg_scripts' );



//CRON SETTING
function seorefreshlink_cron_schedules($schedules){
    if(!isset($schedules["5min"])){
        $schedules["5min"] = array(
            'interval' => 5*60,
            'display' => __('Once every 5 minutes'));
    }
    if(!isset($schedules["30min"])){
        $schedules["30min"] = array(
            'interval' => 30*60,
            'display' => __('Once every 30 minutes'));
	}
	//Defaults
	if(!isset($schedules["6h"])){
        $schedules["6h"] = array(
            'interval' => 21600,
            'display' => __('Every 6 hours'));
	}
	if(!isset($schedules["9h"])){
        $schedules["9h"] = array(
            'interval' => 32400,
            'display' => __('Every 9 hours'));
	}
	if(!isset($schedules["12h"])){
        $schedules["12h"] = array(
            'interval' => 43200,
            'display' => __('Every 12 hours'));
    }
    return $schedules;
}
add_filter('cron_schedules','seorefreshlink_cron_schedules');


//CRON FUNCTIONS
register_activation_hook(__FILE__, 'seorefresh_activation');
add_action('seorefresh_event', 'seorefreshlink_function');

function seorefresh_activation() {
    $first_time = time(); // you probably want this to be shortly after midnight
    $recurrence = '6h';
    wp_schedule_event($first_time, $recurrence, 'seorefresh_event');
}

function seorefreshlink_function() {

	$today = date( 'Y-m-d' );
	$args = array(
		'meta_key' => '_seorefresh_link_field_checker',
		'meta_value' => 'Si',
		'post_status' => 'publish',
		'post_type'  => 'post',
		'meta_query' => array(
			array(
				'key' => '_seorefresh_link_field',
				'value' => $today,
				'compare' => '<=',
				'type' => 'DATE'
			)
		)
	);
	$the_query = new WP_Query( $args );
	if ( $the_query->have_posts() ) : 
 
	   while ( $the_query->have_posts() ) : 
		$the_query->the_post(); 
   
	   $postid= get_the_ID();
	   $meta = get_post_meta( $postid ); 
	   $meta_date = $meta['_seorefresh_link_field'][0]; 
   
	   if($meta['_seorefresh_link_field_checker'][0] == "Si"):
		   wp_update_post(
			   array (
				   'ID'            => $postid, // ID of the post to update
				   'post_date'     => $meta_date
			   )
		   );
		   //Azzero status una volta eseguito
		   update_post_meta( $postid, '_seorefresh_link_field_checker', 'No' );
	   endif;//meta_value

 	 endwhile;
	 wp_reset_postdata();
	else : 
 	endif; 

}//seorefreshlink_function

/* Do not register
register_deactivation_hook(__FILE__, 'my_deactivation');
function my_deactivation() {
	wp_clear_scheduled_hook('your_daily_event');
}*/