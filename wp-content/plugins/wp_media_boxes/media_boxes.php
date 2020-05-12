<?php
/**
 *
 * 	@package Media Boxes by casltecode
 *  @author  casltecode
 *
 *
 *	Plugin Name: 	Media Boxes
 *	Plugin URI:  	http://codecanyon.net/user/castlecode/portfolio
 *	Description: 	A perfect plugin for creating amazing portfolios
 *	Author: 		casltecode
 *	Version: 		1.1.1
 *	Author URI: 	http://codecanyon.net/user/castlecode
 *
 */

/* If this file is called directly, abort. */

if ( ! defined( 'ABSPATH' ) ) {
    die();
}

/* directories */

define( 'MEDIA_BOXES_DIR', plugin_dir_path( __FILE__ ) );
define( 'MEDIA_BOXES_URI', plugin_dir_url( __FILE__ ) );
define( 'MEDIA_BOXES_ADMIN', MEDIA_BOXES_DIR . trailingslashit( 'admin' ) );

/* global vars */

define( 'MEDIA_BOXES_VERSION', '1.1.1');
define( 'MEDIA_BOXES_PREFIX', 'media_boxes');

/* import files */

require_once( MEDIA_BOXES_DIR . 'class_media_boxes.php' );
require_once( MEDIA_BOXES_DIR . 'class_media_boxes_shortcode.php' );

/* init classes */

new Media_Boxes();
new Media_Boxes_Shortcode();



