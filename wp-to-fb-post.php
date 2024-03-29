<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              #
 * @since             1.0.0
 * @package           Wp_To_Fb_Post
 *
 * @wordpress-plugin
 * Plugin Name:       Wordpress to Facebook Post
 * Plugin URI:        #
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            #
 * Author URI:        #
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-to-fb-post
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
if(defined('WTFB_PLUGIN_NAME_VERSION')){
	define( 'WTFB_PLUGIN_NAME_VERSION', '1.0.0' );
}
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-to-fb-post-activator.php
 */
function activate_wp_to_fb_post() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-to-fb-post-activator.php';
	Wp_To_Fb_Post_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-to-fb-post-deactivator.php
 */
function deactivate_wp_to_fb_post() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-to-fb-post-deactivator.php';
	Wp_To_Fb_Post_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_to_fb_post' );
register_deactivation_hook( __FILE__, 'deactivate_wp_to_fb_post' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-to-fb-post.php';
require plugin_dir_path( __FILE__ ) . 'public/partials/wp-to-fb-post-api.php';
define('FACEBOOK_SDK_V4_SRC_DIR', plugin_dir_path( __FILE__ ) .'/Facebook/src');
require_once(plugin_dir_path( __FILE__ ).'/Facebook/src/autoload.php');

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_to_fb_post() {

	$plugin = new Wp_To_Fb_Post();
	$plugin->run();

}
run_wp_to_fb_post();

