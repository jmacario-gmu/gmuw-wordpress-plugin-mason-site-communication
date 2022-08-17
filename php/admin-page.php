<?php

/**
 * Summary: php file which implements the plugin WP admin page interface
 */

/**
 * Generates the plugin settings page
 */
function gmuw_msc_display_settings_page() {
	
	// Only continue if this user has the 'manage options' capability
	if (!current_user_can('manage_options')) return;

	// Get plugin options
	$gmuw_msc_options = get_option('gmuw_msc_options');

	// Begin HTML output
	echo "<div class='wrap'>";

	// Page title
	echo "<h1>" . esc_html(get_admin_page_title()) . "</h1>";

	// ask button action
		// Check whether the button has been pressed AND also check the nonce value
			if (isset($_POST['gmuw_msc_get_website_info_button']) && check_admin_referer('gmuw_msc_get_website_info_button_clicked')) {
		// We're good; run the action.
			gmuw_msc_get_website_info_button_action();
		}

	// Output basic plugin info
	echo "<p>This plugin helps to communicate with other Mason websites.</p>";


	/*
	// Begin settings form
	echo "<form action='options.php' method='post'>";

	// output settings fields - outputs required security fields - parameter specifes name of settings group
	settings_fields('gmuw_msc_options');

	// output setting sections - parameter specifies name of menu slug
	do_settings_sections('gmuw_msc');

	// submit button
	submit_button();

	// Close form
	echo "</form>";
	*/
	
	// Get info about another site

	// Heading
	echo '<h3>Get Website Information</h3>';
	echo '<p>Use the form below to get information about other Mason websites.</p>';
	// Start form
	echo '<form action="admin.php?page=gmuw_msc" method="post">';
	// Add nonce
	wp_nonce_field('gmuw_msc_get_website_info_button_clicked');
	// Add fields
	echo '<input type="hidden" value="true" name="gmuw_msc_get_website_info_button" />';

	echo '<table class="form-table" role="presentation"><tbody><tr>';
	echo '	<th scope="row">Website (domain)</th>';
	echo '	<td>';
	echo '		<input id="gmuw-check-on-domain-input" name="gmuw-check-on-domain" type="text" size="40" value="'.$_POST['gmuw-check-on-domain'].'"><br>';
	echo '		<label for="gmuw-check-on-domain-input">enter the domain of the site (e.g. example.gmu.edu)</label>';
	echo '	</td>';
	echo '</tr></tbody></table>';

	// Add submit button
	submit_button('Get website information');
	// End form
	echo '</form>';

	// Finish HTML output
	echo "</div>";

}

function gmuw_msc_get_website_info_button_action(){

	// Get the homepage to do a basic check of this site.
		$homepage_response = gmuw_msc_get_url_content('https://'.$_POST['gmuw-check-on-domain']);

	// See what we can infer about this site generally based on the homepage. 

	// Is it WordPress? (check for "/wp-content/" in the HTML response)
	if (preg_match('/\/wp-content\//i',$homepage_response)) {
		$it_is_wordpress=true;

		// Is it Mason WordPress
			if (preg_match('/gmu[wj]-/i',$homepage_response)) {
				$it_is_mason_wordpress=true;
			}

	}

	// Is it Drupal? (check for "/sites/" in the HTML response)
	if (preg_match('/\/sites\//i',$homepage_response)) {
		$it_is_drupal=true;

		// Is it Mason Drupal
			if (preg_match('/sitemasonry/i',$homepage_response)) {
				$it_is_mason_drupal=true;
			}

	}

	// Is it WordPress?
	if ($it_is_wordpress) {
		// Get site info from the WordPress API
			// Set URL for REST endpoint
				$wordpress_info_endpoint_url='https://' . $_POST['gmuw-check-on-domain'] . '/wp-json/';
			// Try to get the info
				$wordpress_info_response = gmuw_msc_get_url_content($wordpress_info_endpoint_url);
				//echo '<p><textarea>'.$wordpress_info_response.'</textarea></p>';
			// Try to parse it as JSON
				$wordpress_info_response_json = json_decode($wordpress_info_response);
				//var_dump($wordpress_info_response_json);
	}

	//Is it mason wordpress?
	if ($it_is_mason_wordpress) {

		// get active theme info for this site from the Mason Site Check In plugin API
			// Set URL for REST endpoint
				$mason_site_check_in_theme_info_endpoint_url='https://' . $_POST['gmuw-check-on-domain'] . '/wp-json/gmuj-sci/theme-info';
			// Try to get the info
				$mason_site_check_in_theme_info_response = gmuw_msc_get_url_content($mason_site_check_in_theme_info_endpoint_url);
				//echo '<p><textarea>'.$mason_site_check_in_theme_info_response.'</textarea></p>';
			// Try to parse it as JSON
				$mason_site_check_in_theme_info_response_json = json_decode($mason_site_check_in_theme_info_response);
				//var_dump($mason_site_check_in_theme_info_response_json);

		// get organizational info from the Mason Meta Information plugin API
			// Set URL for REST endpoint
				$mason_info_endpoint_url='https://' . $_POST['gmuw-check-on-domain'] . '/wp-json/gmuj-mmi/mason-site-info';
			// Try to get the info
				$mason_info_response = gmuw_msc_get_url_content($mason_info_endpoint_url);
				//echo '<p><textarea>'.$mason_info_response.'</textarea></p>';
			// Try to parse it as JSON
				$mason_info_response_json = json_decode($mason_info_response);
				//var_dump($mason_info_response_json);

		// get info for this site from the Mason Site Check In plugin API
			// Set URL for REST endpoint
				$mason_site_check_in_endpoint_url='https://' . $_POST['gmuw-check-on-domain'] . '/wp-json/gmuj-sci/most-recent-modifications';
			// Try to get the info
				$mason_site_check_in_response = gmuw_msc_get_url_content($mason_site_check_in_endpoint_url);
				//echo '<p><textarea>'.$mason_site_check_in_response.'</textarea></p>';
			// Try to parse it as JSON
				$mason_site_check_in_response_json = json_decode($mason_site_check_in_response);
				//var_dump($mason_site_check_in_response_json);

	}

	// Output message to page
	echo '<div id="message" class="updated fade">';

	// Introductory info
	echo '<h1>Website Information: ' . $_POST['gmuw-check-on-domain'] . '</h1>';

	// Tech info
	echo '<h2>Technology/Platform</h2>';
	
	if (empty($homepage_response)){
		echo '<p>We could not find this site... &#128533;</p>';
	} else {
		echo '<p>';
		//echo 'Technology: ';
		if ($it_is_wordpress) { echo 'WordPress'; }
		if ($it_is_drupal) { echo 'Drupal'; }
		if (!($it_is_wordpress||$it_is_drupal)) { echo 'Not WordPress or Drupal</span>'; }
		//echo '<br />';
		echo ' / ';
		//echo 'Platform: ';
		if ($it_is_mason_wordpress) { echo '<span style="color:green;">Mason WordPress Platform</span>'; }
		if ($it_is_mason_drupal) { echo '<span style="color:green;">Site Masonry (Mason Drupal)</span>'; }
		if (!($it_is_mason_wordpress || $it_is_mason_drupal)) { echo 'Not a centrally-managed Mason platform'; }
		echo '</p>';

		// Is it WordPress?
		if ($it_is_wordpress) {

			// Site name
			echo '<h2>Site Name</h2>';

			echo '<p>';
			echo $wordpress_info_response_json->name;
			echo '</p>';

		}

		// If it is Mason WordPress, we may be able to get additional info
		if ($it_is_mason_wordpress) {

			// Theme info
			echo '<h2>Theme Information</h2>';


			// Did we get a json response?
			if (gettype($mason_site_check_in_theme_info_response_json)!='object'){
				echo '<p>We got a response, but it was not what we expected. Most likely the Site Check-In plugin is not up-to-date. &#128533;</p>';
			} else {
				if ($mason_site_check_in_theme_info_response_json->data->status==404){
						echo '<p>We got a JSON response, but it was a 404. Most likely the Site Check-In plugin is not activated or up-to-date. &#128533;</p>';
				} else {
					echo '<p>';
					echo 'Theme: '.$mason_site_check_in_theme_info_response_json->theme.' ('.$mason_site_check_in_theme_info_response_json->theme_version.')';
					echo '</p>';
				}
			}


			// Organizational and contact info
			echo '<h2>Organizational/Contact Information</h2>';

			// Did we get a json response?
			if (gettype($mason_info_response_json)!='object'){
				echo '<p>We got a response, but it was not what we expected. Most likely the Mason Meta Information plugin is not up-to-date. &#128533;</p>';
			} else {

				if ($mason_info_response_json->data->status==404){
						echo '<p>We got a JSON response, but it was a 404. Most likely the Mason Meta Information plugin is not activated or up-to-date. &#128533;</p>';
				} else {

					// Organizational info
					//echo '<h4>Organizational Information</h4>';
					echo '<p>';
					echo 'Unit: '.$mason_info_response_json->unit.'<br />';
					echo 'Department: '.$mason_info_response_json->department;
					echo '</p>';

					// Contact info
					//echo '<h4>Contact Information</h4>';
					echo '<p>';
					echo 'Technical contact: '.$mason_info_response_json->technical_contact.'<br />';
					echo 'Content contact: '.$mason_info_response_json->content_contact;
					echo '</p>';
				}

			}

			// Recent activity
			echo '<h2>Recent Activity</h2>';

			// Did we get a json response?
			if (gettype($mason_site_check_in_response_json)!='object'){
				echo '<p>We got a response, but it was not what we expected. Most likely the Site Check-In plugin is not up-to-date. &#128533;</p>';
			} else {
				if ($mason_site_check_in_response_json->data->status==404){
						echo '<p>We got a JSON response, but it was a 404. Most likely the Site Check-In plugin is not activated or up-to-date. &#128533;</p>';
				} else {
					echo '<p>';
					echo 'Site last modified: '.$mason_site_check_in_response_json->last_modified.'<br />';
					echo 'Last login: '.$mason_site_check_in_response_json->last_login.' ('.$mason_site_check_in_response_json->last_login_user.')';
					echo '</p>';
				}
			}

		} else {
			echo '<h2>Other Information</h2>';
			echo '<p>We are unable to determine additional website organizational/contact/modification information. &#128533;</p>';
		}

	}

	// Finish page message output
    echo '</div>';

}

/**
 * Generates content for general settings section
 */
function gmuw_msc_callback_section_settings_general() {

	echo '<p>Set the Mason Site Communications general settings.</p>';

}

/**
 * Generates text field for plugin settings option
 */
function gmuw_msc_callback_field_text($args) {
	
	//Get array of options. If the specified option does not exist, get default options from a function
	$options = get_option('gmuw_msc_options', gmuw_msc_options_default());
	
	//Extract field id and label from arguments array
	$id    = isset($args['id'])    ? $args['id']    : '';
	$label = isset($args['label']) ? $args['label'] : '';
	
	//Get setting value
	$value = isset($options[$id]) ? sanitize_text_field($options[$id]) : '';
	
	//Output field markup
	echo '<input id="gmuw_msc_options_'. $id .'" name="gmuw_msc_options['. $id .']" type="text" size="40" value="'. $value .'">';
	echo "<br />";
	echo '<label for="gmuw_msc_options_'. $id .'">'. $label .'</label>';
	
}

/**
 * Sets default plugin options
 */
function gmuw_msc_options_default() {

	return array(
		//'gmuw_msc_settings_field_01'   => 'default value',
		//'gmuw_msc_settings_email'   => 'webmaster@gmu.edu',
	);

}

/**
 * Validate plugin options
 */
function gmuw_msc_callback_validate_options($input) {
	
	// Example field
	if (isset($input['gmuw_msc_settings_field_01'])) {
		$input['gmuw_msc_settings_field_01'] = sanitize_text_field($input['gmuw_msc_settings_field_01']);
	}

	return $input;
	
}
