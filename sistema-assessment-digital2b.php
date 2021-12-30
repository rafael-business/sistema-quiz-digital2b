<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.digital2b.com
 * @since             1.0.0
 * @package           sistema_assessment_digital2b
 *
 * @wordpress-plugin
 * Plugin Name:       Sistema de Assessment Digital2b
 * Plugin URI:        https://www.digital2b.com
 * Description:       Sistema de Assessments
 * Version:           2.0.0
 * Author:            Guilherme Lopes / Rafael Business
 * Author URI:        https://www.digital2b.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sistema-assessment-digital2b
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
define( 'sistema_assessment_digital2b_VERSION', '1.1.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-sistema-assessment-digital2b-activator.php
 */
function activate_sistema_assessment_digital2b() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sistema-assessment-digital2b-activator.php';
	sistema_assessment_digital2b_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-sistema-assessment-digital2b-deactivator.php
 */
function deactivate_sistema_assessment_digital2b() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sistema-assessment-digital2b-deactivator.php';
	sistema_assessment_digital2b_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_sistema_assessment_digital2b' );
register_deactivation_hook( __FILE__, 'deactivate_sistema_assessment_digital2b' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-sistema-assessment-digital2b.php';

require plugin_dir_path( __FILE__ ) . 'includes/custom-post-type.php';
require plugin_dir_path( __FILE__ ) . 'includes/upload.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_sistema_assessment_digital2b() {

	$plugin = new sistema_assessment_digital2b();
	$plugin->run();

}



run_sistema_assessment_digital2b();
