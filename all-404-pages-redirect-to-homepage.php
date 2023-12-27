<?php
/*
Plugin Name: All 404 Pages Redirect to Homepage

Description: a plugin to redirect all 404 pages to home page or any custom page

Author: Geek Code Lab

Version: 1.9

Author URI: https://geekcodelab.com/

Text Domain : all-404-pages-redirect-to-homepage
*/

if ( ! defined( 'ABSPATH' ) ) exit;

if (!defined("AEPRH_PLUGIN_DIR_PATH"))

	define("AEPRH_PLUGIN_DIR_PATH", plugin_dir_path(__FILE__));

require_once( plugin_dir_path( __FILE__ ) . 'functions.php' );

define( 'AEPRH_VERSION', '1.9' );

add_action('admin_menu', 'admin_menu_404r');

add_action('wp', 'redirect_404r');

add_action( 'admin_enqueue_scripts', 'enqueue_styles_scripts_404r' );

function aeprh_plugin_add_settings_link( $links ) { 
	$support_link = '<a href="https://geekcodelab.com/contact/"  target="_blank" >' . __( 'Support', 'all-404-pages-redirect-to-homepage' ) . '</a>'; 
	array_unshift( $links, $support_link );

	$settings_link = '<a href="options-general.php?page=all-404-redirect-option">' . __( 'Settings', 'all-404-pages-redirect-to-homepage' ) . '</a>';
	array_unshift( $links, $settings_link );

	
	global $wpdb;
	$table_name = $wpdb->prefix . 'aeprh_links_lists';

	
	
	if($wpdb->get_var( "show tables like '$table_name'" ) != $table_name) {

		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id mediumint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			ip_address varchar(90) DEFAULT '' NOT NULL,
			time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			url varchar(300) DEFAULT '' NOT NULL
			) $charset_collate;";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'aeprh_plugin_add_settings_link');

add_action( 'upgrader_process_complete', 'aeprh_upgrade_function',10, 2);
 
function aeprh_upgrade_function( $upgrader_object, $options ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'aeprh_links_lists';

	
	
	if($wpdb->get_var( "show tables like '$table_name'" ) != $table_name) {

		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id mediumint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			ip_address varchar(90) DEFAULT '' NOT NULL,
			time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			url varchar(300) DEFAULT '' NOT NULL
			) $charset_collate;";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}
}

register_activation_hook( __FILE__ , 'plugin_active_404r' );

function plugin_active_404r(){

	$redirect_to	= get_redirect_to_404r();
	$status			= get_status_404r();

	if(empty($redirect_to)){
		update_option('redirect_to_404r',home_url());
	}

	if(empty($status)){ 
		update_option('status_404r',0);
	}

}


function redirect_404r(){

	if(is_404()) {

        $redirect_to	= get_redirect_to_404r();
        $status			= get_status_404r();
	    $link			= current_link_404r();
	    if($link == $redirect_to){
			_e("<b>All 404 Redirect to Homepage</b> has detected that the target URL is invalid, this will cause an infinite loop redirection, please go to the plugin settings and correct the traget link! ","all-404-pages-redirect-to-homepage");
	        exit(); 
	    }

	 	if($status=='1' & $redirect_to!=''){

			global $wpdb;
			global $wp;
			$table_name = $wpdb->prefix."aeprh_links_lists";

			
			$link_date 	= date("Y-m-d H:i:s");
			$ip_address	= $_SERVER['REMOTE_ADDR'];
			$curr_url = home_url( $wp->request );
			
			
			$rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE url = '$curr_url' and ip_address = '$ip_address' ");
			
			if($rowcount == 0){
				if($wpdb->get_var( "show tables like '$table_name'" ) != $table_name) {
	
					$charset_collate = $wpdb->get_charset_collate();
					$sql = "CREATE TABLE IF NOT EXISTS $table_name (
						id mediumint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
						ip_address varchar(90) DEFAULT '' NOT NULL,
						time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
						url varchar(300) DEFAULT '' NOT NULL
						) $charset_collate;";
					
					require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
					dbDelta( $sql );
				}
				$res = $wpdb->insert($table_name, array('url' => $curr_url, 'time' => $link_date, 'ip_address' => $ip_address) );				
			}else{
				$res =	$wpdb->update($table_name, array('time'=>$link_date), array('url'=>$curr_url));
			}

		 	header ('HTTP/1.1 301 Moved Permanently');
			header ("Location: " . $redirect_to);
			exit(); 

		}
	}
}



//---------------------------------------------------------------

function admin_menu_404r() {
	add_options_page(
		__('All 404 Redirect to Homepage','all-404-pages-redirect-to-homepage'), 
		__('All 404 Redirect to Homepage','all-404-pages-redirect-to-homepage'), 
		'manage_options', 
		'all-404-redirect-option', 
		'options_menu_404r'  
	);
}

//---------------------------------------------------------------//

function options_menu_404r() {
	
	if (!current_user_can('manage_options')){

		wp_die( __('You do not have sufficient permissions to access this page.','all-404-pages-redirect-to-homepage') );
	}

	include( plugin_dir_path( __FILE__ ) . 'options.php' );
}

//---------------------------------------------------------------//

/** Admin Site Add Css And Script Start */
function enqueue_styles_scripts_404r(){

    if( is_admin() ) { 
        $css= plugins_url() . '/'.  basename(dirname(__FILE__)) . "/assets/css/style.css";               

        wp_enqueue_style( 'main-404-css', $css, '',AEPRH_VERSION);

		$js= plugins_url() . '/'.  basename(dirname(__FILE__)) . "/assets/js/aeprh-admin-script.js";       
		wp_enqueue_script( 'wsppcp-custom', $js, array('jquery'), AEPRH_VERSION, true );
    }

}
/** Admin Site Add Script End */