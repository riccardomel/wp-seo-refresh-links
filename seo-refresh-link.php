<?php
/**
 * Plugin Name: Seo Refresh Link
 * Plugin URI: https://riccardomel.com
 * Description: Seo Refresh Link
 * Author: Riccardo Mel
 * Author URI: https://riccardomel.com
 * Version: 1.0.4
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

	if ( is_admin() ) {
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
}
// Hook scripts function into block editor hook
add_action( 'enqueue_block_assets', 'hello_gutenberg_scripts' );


/**
 * Cron Settings for scheduling the articles
 */
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
	if(!isset($schedules["4h"])){
        $schedules["4h"] = array(
            'interval' => 14400,
            'display' => __('Every 4 hours'));
	}
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


/**
 * Cron Functions
 */
register_activation_hook(__FILE__, 'seorefresh_activation');

add_action('seorefresh_event', 'seorefreshlink_function');

function seorefresh_activation() {
    $first_time = time(); // you probably want this to be shortly after midnight
    $recurrence = '30min';
    wp_schedule_event($first_time, $recurrence, 'seorefresh_event');
}

function seorefreshlink_function() {

	//Set user id as not bot
	//Fix permission with iframe or content inside post.
	wp_set_current_user(1); 

	//Seo Refresh
	date_default_timezone_set('Europe/Rome');
	$today = date( 'Y-m-d H:i:s' );
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

			if(new DateTime($meta_date) > new DateTime($today) ){
				//echo "Data e ora salvata maggiore di oggi";
			}else{

				global $allowedposttags;
				$allowedposttags['div'] = array('align' => array (), 'class' => array (), 'id' => array (), 'dir' => array (), 'lang' => array(), 'style' => array (), 'xml:lang' => array() );
				$allowedposttags['iframe'] = array('src' => array () );

				//echo "Data e ora salvata minore o uguale di oggi";
				wp_update_post(
					array (
						'ID'            => $postid, // ID of the post to update
						'post_date'     => $meta_date,
						'post_date_gmt'  => get_gmt_from_date($meta_date), 
					)
				);
				//Azzero status una volta eseguito
				 update_post_meta( $postid, '_seorefresh_link_field_checker', 'No' );
			}
		  
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

//Ovveride <pubDate> = Created Date - inside FEED RSS
add_filter( 'get_post_time',          'wpseo_refresh_link_feed_time_override', 10, 3 ); 

add_filter( 'get_post_modified_time', 'wpseo_refresh_link_feed_time_override', 10, 3 ); 

function wpseo_refresh_link_feed_time_override( $time, $d, $gmt )
{
    global $post;

    //If feed ovverride created date with new one
    if(  is_feed() ):
        $time = $post->post_date;
    endif;

    //else - return time
    return $time;
}
//END Ovveride <pubDate> = Created Date - inside FEED RSS





// this function initializes the iframe elements
function not_strip_iframe($initArray) {
    $initArray['extended_valid_elements'] = "iframe[id|class|title|style|align|frameborder|height|longdesc|marginheight|marginwidth|name|scrolling|src|width|sandbox]";
    return $initArray;
}

// this function alters the way the WordPress editor filters your code
add_filter('tiny_mce_before_init', 'not_strip_iframe');

/**
 * Add iFrame to allowed wp_kses_post tags
 *
 * @param string $tags Allowed tags, attributes, and/or entities.
 * @param string $context Context to judge allowed tags by. Allowed values are 'post',
 *
 * @return mixed
 */
add_filter( 'wp_kses_allowed_html', 'allow_iframe_in_editor', 10, 2 );
function allow_iframe_in_editor( $tags, $context ) {
    if( 'post' === $context ) {
        $tags['iframe'] = array(
            'allowfullscreen' => TRUE,
            'frameborder' => TRUE,
            'marginwidth'=> TRUE,
            'marginheight'=> TRUE,
            'sandbox' => TRUE,
            'scrolling' => TRUE,
            'height' => TRUE,
            'src' => TRUE,
            'style' => TRUE,
            'width' => TRUE,
        );
    }
    return $tags;
}
