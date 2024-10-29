<?php
/*
 * Plugin Name: BBP Improvements for Yoast
 * Version: 1.0
 * Plugin URI: https://wordpress.org/plugins/bbp-improvements-for-yoast/
 * Description: This plugin helps Yoast wordpress SEO work better under BBpress.
 * Author: 4Games
 * Author URI: https://www.sockscap64.com/wordpress-plguin-bbpress-improvments-for-yoast-seo/
 * Requires at least: 4.0
 * Tested up to: 5.0.2
 *
 * Text Domain: bbp-improvments-for-yoast
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author 4Games
 * @since 1.0.0
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
 
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Detect plugin. 
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// Load plugin class files
require_once( 'includes/class-bbp-improvments-for-yoast.php' );

/**
 * Returns the main instance of BBPress_Improvements_for_Yoast to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object BBPress_Improvements_for_Yoast
 */
function BBPress_Improvements_for_Yoast () {
	$instance = BBPress_Improvements_for_Yoast::instance( __FILE__, '1.0.0' );

	return $instance;
}

BBPress_Improvements_for_Yoast();