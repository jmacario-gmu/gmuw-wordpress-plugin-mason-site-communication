<?php

/**
 * Summary: php file which sets up plugin settings
 */

/**
 * Register plugin settings
 */
add_action('admin_init', 'gmuw_msc_register_settings');
function gmuw_msc_register_settings() {
    
    // Register serialized options setting to store this plugin's options
    register_setting(
        'gmuw_msc_options',
        'gmuw_msc_options',
        'gmuw_msc_callback_validate_options'
    );

} 