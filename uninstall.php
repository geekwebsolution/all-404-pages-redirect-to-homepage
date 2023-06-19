<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

function aeprh_delete_plugin() {
	global $wpdb;


	$wpdb->query( sprintf(
		"DROP TABLE IF EXISTS %s",
		$wpdb->prefix . 'aeprh_links_lists'
	) );
}

if ( ! defined( 'AEPRH_VERSION' ) ) {
	aeprh_delete_plugin();
}
