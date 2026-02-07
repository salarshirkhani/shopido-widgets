<?php
/**
 * Plugin Name: Shopido Widgets Pack
 * Description: مجموعه ویجت‌های المنتور + CPT استوری برای قالب شما.
 * Version: 1.0.0
 * Author: Shopido
 * Text Domain: shopido-widgets-pack
 */

if ( ! defined('ABSPATH') ) exit;

// مسیرها
define('SHOPIDO_WP_DIR', plugin_dir_path(__FILE__));
define('SHOPIDO_WP_URL', plugin_dir_url(__FILE__));

// لودر اصلی
require_once SHOPIDO_WP_DIR . 'includes/init.php';
