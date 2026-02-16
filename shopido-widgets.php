<?php
/**
 * Plugin Name: Shopido Widgets Pack
 * Description: elementor widgets pack
 * Version: 1.0.0
 * Author: Salar Shirkhani
 * Text Domain: shopido-widgets-pack
 */

if ( ! defined('ABSPATH') ) exit;

// ROUTES
define('SHOPIDO_WP_DIR', plugin_dir_path(__FILE__));
define('SHOPIDO_WP_URL', plugin_dir_url(__FILE__));

// MAIN LOADER
require_once SHOPIDO_WP_DIR . 'includes/init.php';
