<?php
/*
Plugin Name: All 404 Pages Redirect to Homepage

Description: a plugin to redirect all 404 pages to home page or any custom page

Author: Geek Code Lab

Version: 2.0

Author URI: https://geekcodelab.com/

Text Domain : all-404-pages-redirect-to-homepage
*/

if ( ! defined( 'ABSPATH' ) ) exit;
if (!defined("AEPRH_PLUGIN_DIR_PATH"))

	define("AEPRH_PLUGIN_DIR_PATH", plugin_dir_path(__FILE__));

if (!defined("AEPRH_PLUGIN_DIR_PATH"))

	define("AEPRH_PLUGIN_DIR_PATH", plugin_dir_path(__FILE__));

if (!defined("AEPRH_PLUGIN_DIR"))
	define("AEPRH_PLUGIN_DIR", plugin_basename(__DIR__));

if (!defined("AEPRH_PLUGIN_BASENAME"))
	define("AEPRH_PLUGIN_BASENAME", plugin_basename(__FILE__));


require_once( plugin_dir_path( __FILE__ ) . 'functions.php' );

define( 'AEPRH_VERSION', '2.0' );
require(AEPRH_PLUGIN_DIR_PATH . 'updater/updater.php');


add_action('admin_menu', 'aeprh_admin_menu_404r');

add_action('wp', 'aeprh_redirect_404r');

add_action( 'admin_enqueue_scripts', 'aeprh_enqueue_styles_scripts_404r' );

function aeprh_plugin_add_settings_link( $aeprh_links ) {

	$aeprh_support_link = '<a href="https://geekcodelab.com/contact/"  target="_blank" >' . __( 'Support', 'all-404-pages-redirect-to-homepage' ) . '</a>';
	array_unshift( $aeprh_links, $aeprh_support_link );

	$aeprh_settings_link = '<a href="options-general.php?page=all-404-redirect-option">' . __( 'Settings', 'all-404-pages-redirect-to-homepage' ) . '</a>';
	array_unshift( $aeprh_links, $aeprh_settings_link );

	aeprh_create_table();

	return $aeprh_links;
}


$aeprh_plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$aeprh_plugin", 'aeprh_plugin_add_settings_link');

add_action( 'upgrader_process_complete', 'aeprh_upgrade_function',10, 2);

function aeprh_upgrade_function( $upgrader_object, $options ) {
	aeprh_create_table();
}

register_activation_hook( __FILE__ , 'aeprh_plugin_active_404r' );

function aeprh_plugin_active_404r(){
	aeprh_updater_activate();
	$aeprh_redirect_to	= aeprh_get_redirect_to_404r();
	$aeprh_status		= aeprh_get_status_404r();

	if(empty($aeprh_redirect_to)){
		update_option('redirect_to_404r',home_url());
	}

	if(empty($aeprh_status)){
		update_option('status_404r',0);
	}

}

add_action('upgrader_process_complete', 'aeprh_updater_activate'); // remove  transient  on plugin  update


function aeprh_redirect_404r(){

	if(is_404()) {

        $aeprh_redirect_to	= aeprh_get_redirect_to_404r();
        $aeprh_status		= aeprh_get_status_404r();
	    $aeprh_link			= aeprh_current_link_404r();
	    if($aeprh_link == $aeprh_redirect_to){
			_e("<b>All 404 Redirect to Homepage</b> has detected that the target URL is invalid, this will cause an infinite loop redirection, please go to the plugin settings and correct the traget link! ","all-404-pages-redirect-to-homepage");
	        exit();
	    }

	 	if($aeprh_status=='1' & $aeprh_redirect_to!=''){

			global $wpdb;
			global $wp;
			$aeprh_table_name = $wpdb->prefix."aeprh_links_lists";


			$aeprh_link_date 	= date("Y-m-d H:i:s");
			$aeprh_ip_address	= $_SERVER['REMOTE_ADDR'];
			$aeprh_curr_url = home_url( $wp->request );


			$rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $aeprh_table_name WHERE url = '$aeprh_curr_url' and ip_address = '$aeprh_ip_address' ");

			if($rowcount == 0){

				aeprh_create_table();

				$res = $wpdb->insert($aeprh_table_name, array('url' => $aeprh_curr_url, 'time' => $aeprh_link_date, 'ip_address' => $aeprh_ip_address) );
			}else{
				$res =	$wpdb->update($aeprh_table_name, array('time'=>$aeprh_link_date), array('url'=>$aeprh_curr_url));
			}

		 	header ('HTTP/1.1 301 Moved Permanently');
			header ("Location: " . $aeprh_redirect_to);
			exit();

		}
	}
}

//---------------------------------------------------------------

function aeprh_admin_menu_404r() {
	add_options_page(
		__('All 404 Redirect to Homepage','all-404-pages-redirect-to-homepage'),
		__('All 404 Redirect to Homepage','all-404-pages-redirect-to-homepage'),
		'manage_options',
		'all-404-redirect-option',
		'aeprh_options_menu_404r'
	);
}

//---------------------------------------------------------------//

function aeprh_options_menu_404r() {

	if (!current_user_can('manage_options')){

		wp_die( __('You do not have sufficient permissions to access this page.','all-404-pages-redirect-to-homepage') );
	}

	include( plugin_dir_path( __FILE__ ) . 'options.php' );
}

//---------------------------------------------------------------//

/** Admin Site Add Css And Script Start */
function aeprh_enqueue_styles_scripts_404r(){

    if( is_admin() ) {
        $aeprh_css = plugins_url() . '/'.  basename(dirname(__FILE__)) . "/assets/css/style.css";
        wp_enqueue_style( 'main-404-css', $aeprh_css, '',AEPRH_VERSION);

		$aeprh_js = plugins_url() . '/'.  basename(dirname(__FILE__)) . "/assets/js/aeprh-admin-script.js";
		wp_enqueue_script( 'wsppcp-custom', $aeprh_js, array('jquery'), AEPRH_VERSION, true );
    }

}
/** Admin Site Add Script End */