<?php

/**
 * Easy Related Posts 
 *
 * @package   Easy related posts
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @link      http://erp.xdark.eu
 * @copyright 2014 Panagiotis Vagenas <pan.vagenas@gmail.com>
 *
 * @wordpress-plugin
 * Plugin Name:       Easy Related Posts 
 * Plugin URI:        http://erp.xdark.eu
 * Description:       A powerfull plugin to display related posts
 * Version:           1.6.0
 * Author:            Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Author URI:        http://xdark.eu
 * Text Domain:       easy-related-posts-eng
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
/* ----------------------------------------------------------------------------*
 * Global definitions
 * ---------------------------------------------------------------------------- */
if (!defined('ERP_SLUG')) {
    define('ERP_SLUG', 'erp');
}
if (!defined('EPR_MAIN_OPTIONS_ARRAY_NAME')) {
    define('EPR_MAIN_OPTIONS_ARRAY_NAME', ERP_SLUG . '_main_options');
}
if (!defined('EPR_BASE_PATH')) {
    define('EPR_BASE_PATH', plugin_dir_path(__FILE__));
}
if (!defined('EPR_BASE_URL')) {
    define('EPR_BASE_URL', plugin_dir_url(__FILE__));
}
if (!defined('EPR_DEFAULT_THUMBNAIL')) {
    define('EPR_DEFAULT_THUMBNAIL', plugin_dir_url(__FILE__) . 'front/assets/img/noImage.png');
}
if (!defined('ERP_RELATIVE_TABLE')) {
    define('ERP_RELATIVE_TABLE', ERP_SLUG . '_related');
}

/* ----------------------------------------------------------------------------*
 * Session functionality
 * ---------------------------------------------------------------------------- */
if (!defined('WP_SESSION_COOKIE')) {
    define('WP_SESSION_COOKIE', 'wp_erp_pro_session');
}

if (!class_exists('Recursive_ArrayAccess')) {
    require_once ( plugin_dir_path(__FILE__) . 'core/session_manager/class-recursive-arrayaccess.php' );
}

// Only include the functionality if it's not pre-defined.
if (!class_exists('WP_Session')) {
    require_once ( plugin_dir_path(__FILE__) . 'core/session_manager/class-wp-session.php' );
    require_once ( plugin_dir_path(__FILE__) . 'core/session_manager/wp-session.php' );
}

/* ----------------------------------------------------------------------------*
 * Core classes
 * ---------------------------------------------------------------------------- */

if (!class_exists('erpDefaults')) {
    require_once EPR_BASE_PATH . 'core/options/erpDefaults.php';
}
if (!class_exists('erpPaths')) {
    require_once EPR_BASE_PATH . 'core/helpers/erpPaths.php';
}

/* ----------------------------------------------------------------------------*
 * Public-Facing Functionality
 * ---------------------------------------------------------------------------- */
erpPaths::requireOnce(erpPaths::$erpWidget);
erpPaths::requireOnce(erpPaths::$easyRelatedPosts);

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook(__FILE__, array('easyRelatedPosts', 'activate'));
register_deactivation_hook(__FILE__, array('easyRelatedPosts', 'deactivate'));

/**
 * Define cron job actions
 */
add_filter('cron_schedules', array('easyRelatedPosts', 'addWeeklyCron'));
add_action('erp_weekly_event_hook', array('easyRelatedPosts', 'weeklyCronJob'));
/**
 * Register plugin and widget
 */
add_action('plugins_loaded', array('easyRelatedPosts', 'get_instance'));
add_action('widgets_init', function () {
    register_widget("erpWidget");
});

/* ----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 * ---------------------------------------------------------------------------- */
/**
 */
if (is_admin()) {
    erpPaths::requireOnce(erpPaths::$easyRelatedPostsAdmin);
    add_action('plugins_loaded', array('easyRelatedPostsAdmin', 'get_instance'));
}