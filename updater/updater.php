<?php

if (!defined('ABSPATH')) exit;

/**
 * License manager module
 */
function aeprh_updater_utility() {
    $prefix = 'AEPRH_';
    $settings = [
        'prefix' => $prefix,
        'get_base' => AEPRH_PLUGIN_BASENAME,
        'get_slug' => AEPRH_PLUGIN_DIR,
        'get_version' => AEPRH_BUILD,
        'get_api' => 'https://download.geekcodelab.com/',
        'license_update_class' => $prefix . 'Update_Checker'
    ];

    return $settings;
}

function aeprh_updater_activate() {

    // Refresh transients
    delete_site_transient('update_plugins');
    delete_transient('aeprh_plugin_updates');
    delete_transient('aeprh_plugin_auto_updates');
}

require_once(AEPRH_PLUGIN_DIR_PATH . 'updater/class-update-checker.php');
