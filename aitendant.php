<?php 
/*
 * Plugin Name: aitendant for WordPress
 * Version: 1.1
 * Plugin URI: http://www.aitendant.com/plugins/wordpress/aitendant/
 * Description: Adds the necessary JavaScript code to enable aitendant.
 * Author: Robb Bennett
 * Author URI: http://www.visual23.com/
 * Text Domain: aitendant
 */

define('AITENDANT_VERSION', '1.1');

// Constants for enabled/disabled state
define("ai_enabled", "enabled", true);
define("ai_disabled", "disabled", true);

// Defaults, etc.
define("key_ai_status", "ai_status", true);

define("ai_status_default", ai_disabled, true);

// Create the default key and status
add_option(key_ai_status, ai_status_default, '');

// Create a option page for settings
add_action('admin_init', 'ai_admin_init');
add_action('admin_menu', 'add_ai_option_page');

// Initialize the options
function ai_admin_init() {
	# Load the localization information
	$plugin_dir = basename(dirname(__FILE__));
	load_plugin_textdomain('aitendant', 'wp-content/plugins/' . $plugin_dir . '/localizations', $plugin_dir . '/localizations');
}

# Add the core aitendant script, with a high priority to ensure last script for async tracking
add_action('wp_footer', 'add_aitendant', 999999);


// Hook in the options page function
function add_ai_option_page() {
	$plugin_page = add_options_page(__('aitendant for WordPress Settings', 'aitendant'), 'aitendant', 'manage_options', basename(__FILE__), 'ai_options_page');
}

add_action('plugin_action_links_' . plugin_basename(__FILE__), 'ai_filter_plugin_actions');

// Add settings option
function ai_filter_plugin_actions($links) {
	$new_links = array();
	
	$new_links[] = '<a href="options-general.php?page=aitendant.php">' . __('Settings', 'aitendant') . '</a>';
	
	return array_merge($new_links, $links);
}

add_filter('plugin_row_meta', 'ai_filter_plugin_links', 10, 2);

function ai_options_page() {
	// If we are a postback, store the options
	if (isset($_POST['info_update'])) {
							
		// Update the status
		$ai_status = $_POST[key_ai_status];
		if (($ai_status != ai_enabled) && ($ai_status != ai_disabled))
			$ai_status = ai_status_default;
		update_option(key_ai_status, $ai_status);

		// Give an updated message
		echo "<div class='updated fade'><p><strong>" . __('aitendant for WordPress settings saved.', 'aitendant') . "</strong></p></div>";
	}

	// Output the options page
	?>

<div class="wrap">
<h2>
  <?php _e('aitendant for WordPress Settings', 'aitendant'); ?>
</h2>
<form method="post" action="options-general.php?page=aitendant.php">
  <?php
			# Add a nonce
			wp_nonce_field('aitendant-update_settings');
			?>
  <h3>
    <?php _e('Basic Settings', 'aitendant'); ?>
  </h3>
  <?php if (get_option(key_ai_status) == ai_disabled) { ?>
  <div style="margin:10px auto; border:3px #f00 solid; background-color:#fdd; color:#000; padding:10px; text-align:center;">
    <?php _e('aitendant integration is currently <strong>DISABLED</strong>.', 'aitendant'); ?>
  </div>
  <?php } ?>
  <table class="form-table" cellspacing="2" cellpadding="5" width="100%">
    <tr>
      <th width="30%" valign="top" style="padding-top: 10px;"> <label for="<?php echo key_ai_status ?>">
          <?php _e('aitendant is', 'aitendant'); ?>
          :</label>
      </th>
      <td><?php
						echo "<select name='".key_ai_status."' id='".key_ai_status."'>\n";
						
						echo "<option value='".ai_enabled."'";
						if(get_option(key_ai_status) == ai_enabled)
							echo " selected='selected'";
						echo ">" . __('Enabled', 'aitendant') . "</option>\n";
						
						echo "<option value='".ai_disabled."'";
						if(get_option(key_ai_status) == ai_disabled)
							echo" selected='selected'";
						echo ">" . __('Disabled', 'aitendant') . "</option>\n";
						
						echo "</select>\n";
						?></td>
    </tr>
  </table>
  
  <p class="submit">
    <input type="submit" name="info_update" value="<?php _e('Save Changes', 'aitendant'); ?>" />
  </p>
  </div>
</form>
<?php
}


/**
 * Echos out the aitendant code
 **/
function add_aitendant()
{
	# Add the notice that aitendant for WordPress is enabled
	echo "<!-- aitendant for WordPress by aitendant" . AITENDANT_VERSION . ": https://app.aitendant.com/_api/aitendant.1.2.js -->\n";
	echo "<script language='JavaScript' type='text/javascript' src='https://app.aitendant.com/_api/aitendant.1.2.js'></script>\n";
}


?>
