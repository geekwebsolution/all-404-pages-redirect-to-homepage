<?php
function aeprh_current_link_404r(){

	$aeprh_prt = $_SERVER['SERVER_PORT'];
	$aeprh_sname = $_SERVER['SERVER_NAME'];
	
	if (array_key_exists('HTTPS',$_SERVER) && $_SERVER['HTTPS'] != 'off' && $_SERVER['HTTPS'] != '')
	$aeprh_sname = "https://" . $aeprh_sname; 
	else
	$aeprh_sname = "http://" . $aeprh_sname; 
	
	if($aeprh_prt !=80)
	{
	$aeprh_sname = $aeprh_sname . ":" . $aeprh_prt;
	} 
	
	$aeprh_path = $aeprh_sname . $_SERVER["REQUEST_URI"];
	
	return $aeprh_path ;

}

function aeprh_get_status_404r() {
	return get_option('status_404r');
}

function aeprh_get_redirect_to_404r() {
	return get_option('redirect_to_404r');
}

// Error message
function failure_option_msg_404r($msg){	
	_e('<div class="notice notice-error aeprh-error-msg is-dismissible"><p>' . $msg . '</p></div>','all-404-pages-redirect-to-homepage');	
}

// Success message
function  success_option_msg_404r($msg){
	_e('<div class="notice notice-success aeprh-success-msg is-dismissible"><p>'. $msg . '</p></div>','all-404-pages-redirect-to-homepage');	
}


function aeprh_create_table(){
	global $wpdb;

	$aeprh_table_name = $wpdb->prefix . 'aeprh_links_lists';
	
	if($wpdb->get_var( "show tables like '$aeprh_table_name'" ) != $aeprh_table_name) {

		$aeprh_charset_collate = $wpdb->get_charset_collate();

		$aeprh_sql = "CREATE TABLE IF NOT EXISTS $aeprh_table_name (
			id mediumint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			ip_address varchar(90) DEFAULT '' NOT NULL,
			time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			url varchar(300) DEFAULT '' NOT NULL
		) $aeprh_charset_collate;";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $aeprh_sql );
	}

}