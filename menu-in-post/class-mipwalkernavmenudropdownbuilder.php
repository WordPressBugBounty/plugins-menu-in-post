<?php
/**
 * File containing the class MIPWalkerNavMenuDropdownBuilder.
 *
 * @package menu-in-post
 * @since 1.4
 */

namespace MenuInPost;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class MIPWalkerNavMenuDropdownBuilder.
 *
 * @package menu-in-post
 * @since 1.4
 */
class MIPWalkerNavMenuDropdownBuilder extends \Walker_Nav_Menu {

	/**
	 * Starts the list before the elements are added.
	 *
	 * @param string   $output Used to append additional content (passed by ref).
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 *
	 * @return void
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @param string   $output Used to append additional content (passed by ref).
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 *
	 * @return void
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
	}

	/**
	 * Starts the element output.
	 *
	 * @param string   $output Appends additional content (passed by reference).
	 * @param WP_Post  $item   Menu item data object.
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 * @param int      $id     The current menu item. Default 0.
	 *
	 * @return void
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		// Returns the HTML output for the element.
		// Create each option.
		$item_output = '';

		// Add spacing to the title based on the depth.
		$item->title = str_repeat( ' - ', $depth * 1 ) . $item->title;

		// Get the link.
		if ( ! empty( $args->append_to_url ) ) {
			$attributes = ! empty( $item->url ) ? ' value="' . esc_attr( $item->url ) .
				$args->append_to_url . '"' : '';
		} else {
			$attributes = ! empty( $item->url ) ? ' value="' . esc_attr( $item->url ) .
				'"' : '';
		}

		// Get the target, if any.
		if ( ! empty( $item->target ) ) {
			$attributes .= ' data-target="' . esc_attr( $item->target ) . '"';
		}

		// Add selected attribute if menu item is the current page.
		if ( $item->current ) {
			$attributes .= ' selected="selected"';
		}

		// Add the HTML.
		$item_output .= '<option' . $attributes . '>';
		$item_output .= apply_filters( 'the_title_attribute', $item->title );

		// Add the new item to the output string.
		$output .= $item_output;
	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @param string   $output Used to append additional content (passed by ref).
	 * @param WP_Post  $item   Menu item data object. Not used.
	 * @param int      $depth  Depth of page. Not Used.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 *
	 * @return void
	 */
	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		// Returns the closing tag, if needed.
		// Close the item.
		$output .= "</option>\n";
	}
}
