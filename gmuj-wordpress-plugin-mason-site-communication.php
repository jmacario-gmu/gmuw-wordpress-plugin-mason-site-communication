<?php

/**
 * Main plugin file for the Mason WordPress: Mason Site Communication plugin
 */

/**
 * Plugin Name:       Mason WordPress: Mason Site Communication
 * Author:            Mason Web Administration
 * Plugin URI:        https://github.com/mason-webmaster/gmuw-wordpress-plugin-mason-site-communication
 * Description:       Mason WordPress plugin to help this website communicate with other websites.
 * Version:           0.9
 */


// Exit if this file is not called directly.
	if (!defined('WPINC')) {
		die;
	}

// Set up auto-updates
	require 'plugin-update-checker/plugin-update-checker.php';
	$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/mason-webmaster/gmuw-wordpress-plugin-mason-site-communication/',
	__FILE__,
	'gmuw-wordpress-plugin-mason-site-communication'
	);

// Set global variables
	$days_per_checkpoint=30;

// Include files
	// Admin menu
		include('php/admin-menu.php');
	// Branding
		include('php/fnsBranding.php');
	// Admin page
		include('php/admin-page.php');
	// Plugin settings
		include('php/settings.php');
	// cURL functions
		include('php/fnsCurl.php');
