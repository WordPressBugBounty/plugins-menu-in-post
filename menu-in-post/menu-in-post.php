<?php
/**
 * Plugin Name: Menu In Post
 * Description: A simple but flexible plugin to add menus to a post or page.
 * Author: linux4me2
 * Author URI: https://profiles.wordpress.org/linux4me2
 * Text Domain: menu-in-post
 * Version: 1.4.1
 * License: GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0-standalone.html
 *
 * @package menu-in-post
 */

use MenuInPost\MIPHelpTabs;
use MenuInPost\MIPWalkerNavMenuDropdownBuilder;
use MenuInPost\MIPWalkerNavMenuListBuilder;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MENUINPOST_PLUGIN', __FILE__ );
define( 'MENUINPOST_PLUGIN_DIR', untrailingslashit( dirname( MENUINPOST_PLUGIN ) ) );

if ( ! defined( 'MENU_IN_POST_VERSION' ) ) {
	define( 'MENU_IN_POST_VERSION', '1.4.1' ); // keep in sync with the plugin header.
}

// Register a simple autoloader that looks in the plugin’s root directory.
spl_autoload_register(
	function ( $theclass ) {
		
		
		
		$prefix = 'MenuInPost\\';
		$base   = __DIR__ . '/';

		// Only handle our own namespace.
		$len = strlen( $prefix );
		if ( strncmp( $prefix, $theclass, $len ) !== 0 ) {
				return;
		}

		// Strip the namespace prefix.
		$relative = substr( $theclass, $len );

		// Convert namespace separators to directory separators.
		$relative = str_replace( '\\', '/', $relative );

		// Build the full path to the file.
		$file = $base . 'class-' . strtolower($relative) . '.php';
		
		// ******************** Debugging code. ********************
		//error_log( "[MenuInPost] Autoloader invoked for class: $theclass" );
		//error_log( "[MenuInPost] class filename = $file" );
		// ******************** End debugging code. ****************
		
		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
);

if ( is_admin() ) {
	include_once MENUINPOST_PLUGIN_DIR . '/admin/admin.php';
}

add_shortcode( 'menu_in_post_menu', 'output_mip_menu' );


/**
 * Initiates the output of a Menu In Post Menu.
 *
 * @param array $atts The attributes of the menu.
 *
 * @return Returns the menu via wp_nav_menu()
 */
function output_mip_menu( $atts = array() ) {
	if ( isset( $atts['menu'] ) ) {
		$menu = absint( $atts['menu'] );
	} else {
		$menu = 0;
	}
	if ( 0 === $menu ) {
		return fallback_mip();
	} else {
		$args = array(
			'menu'        => $menu,
			'fallback_cb' => 'fallback_mip',
			'echo'        => false,
		);
	}

	/*
		If menu_id is empty, don't pass a value, and the menu slug with an
		incremented value added will be used.

		If container_class is empty, don't pass a value and
		'menu-{menu slug}-container' will be used.
	*/
	$defaults = array(
		'menu_class'       => 'menu',
		'menu_id'          => '',
		'container'        => 'div',
		'container_class'  => '',
		'container_id'     => '',
		'style'            => 'list',
		'placeholder_text' => esc_html( __( 'Select...', 'menu-in-post' ) ),
		'append_to_url'    => '',
		'depth'            => 0,
	);
	foreach ( $defaults as $att => $default ) {
		switch ( $att ) {
			case 'depth':
				if ( isset( $atts[ $att ] ) ) {
					$passed_depth = absint( $atts[ $att ] );
					if ( $passed_depth > 0 ) {
						$args['depth'] = $passed_depth;
					}
				} else {
					$atts['depth'] = $default;
				}
				break;
			// These should be only strings.
			default:
				if ( isset( $atts[ $att ] ) ) {
					$passed_att = sanitize_text_field( $atts[ $att ] );
					if ( '' !== $passed_att ) {
						$args[ $att ] = $passed_att;
					}
				} else {
					$atts[ $att ] = $default;
				}
		}
	}
	if ( 'dropdown' === $atts['style'] ) {
		$select = '<select class="mip-drop-nav"';
		if ( '' !== $atts['menu_id'] ) {
			$select .= ' id="' . $args['menu_id'] . '"';
		}
		$select            .= '>';
		$args['items_wrap'] = $select . '<option value="#">' .
			$atts['placeholder_text'] . '</option>%3$s</select>';
		$args['walker']     = new MIPWalkerNavMenuDropdownBuilder();
	} elseif ( '' !== $atts['append_to_url'] ) {
			$args['walker'] = new MIPWalkerNavMenuListBuilder();
	}
	if ( '' !== $atts['append_to_url'] ) {
		$args['append_to_url'] = $atts['append_to_url'];
	}
	return wp_nav_menu( $args );
}

/**
 * Menu In Post fallback function.
 *
 * @return void
 */
function fallback_mip() {}

add_action( 'wp_enqueue_scripts', 'enqueue_mip_front_end_js' );

/**
 * Selectively enqueues the JavaScript for Menu In Post
 *
 * @return void
 */
function enqueue_mip_front_end_js() {
	$options = get_option(
		'mip_options',
		array(
			'miploadjs'     => 'always',
			'miponlypages'  => '',
			'mipminimizejs' => 'yes',
		)
	);

	$load   = false;
	$loadjs = $options['miploadjs'];
	if ( 'yes' === $options['mipminimizejs'] ) {
		$file = 'main-min.js';
	} else {
		$file = 'main.js';
	}

	switch ( $loadjs ) {
		case 'always':
			$load = true;
			break;
		case 'onlypages':
			$pagestr = trim( str_replace( ' ', '', $options['miponlypages'] ) );
			if ( '' !== $pagestr ) {
				$pages = explode( ',', $pagestr );
				if ( is_array( $pages ) ) {
					$pageid = get_queried_object_id();
					if ( in_array( $pageid, $pages, true ) ) {
						$load = true;
					}
				}
			}
			break;
	}

	if ( true === $load ) {
		wp_enqueue_script(
			'menu_in_post_frontend_script',
			plugins_url( 'js/' . $file, __FILE__ ),
			array( 'jquery' ),
			MENU_IN_POST_VERSION,
			true
		);
	}
}
