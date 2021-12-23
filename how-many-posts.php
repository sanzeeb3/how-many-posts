<?php
/**
 * Plugin Name: How Many Posts?
 * Description: Sends a weekly email to the site administrator on number of posts published this past week.
 * Version: 1.0.0
 * Author: Sanjeev Aryal
 * Author URI: https://www.sanjeebaryal.com.np
 * Text Domain: how-many-posts
 */

/**
 * How Many Posts? Plugin.
 *
 * @package    How Many Posts?
 * @author     Sanjeev Aryal
 * @since      1.0.0
 *
 * @license    GPL-3.0+ @see https://www.gnu.org/licenses/gpl-3.0.html
 */

defined( 'ABSPATH' ) || die();

define( 'HOW_MANY_POSTS_PLUGIN_FILE', __FILE__ );
define( 'HOW_MANY_POSTS_PLUGIN_PATH', __DIR__ );

require_once HOW_MANY_POSTS_PLUGIN_PATH . '/plugin.php';
require_once HOW_MANY_POSTS_PLUGIN_PATH . '/action-scheduler/action-scheduler.php';

/**
 * Return the main instance of Plugin Class.
 *
 * @since  1.0.0
 *
 * @return Plugin.
 */
function how_many_posts() {
    $instance = \HowManyPosts\Plugin::get_instance();

    $instance->init();

    return $instance;

}

how_many_posts();