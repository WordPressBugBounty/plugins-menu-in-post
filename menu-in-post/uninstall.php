<?php
/**
 * Uninstall functions for Menu In Post
 *
 * @package menu-in-post
 * @since 1.0.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

delete_option( MIP_OPTION_NAME );
