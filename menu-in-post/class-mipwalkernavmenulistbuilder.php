<?php
/**
 * File containing the class MIPWalkerNavMenuListBuilder.
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
 * Class MIPWalkerNavMenuListBuilder.
 *
 * @package menu-in-post
 * @since 1.4
 */
class MIPWalkerNavMenuListBuilder extends \Walker_Nav_Menu {

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
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;
		
		// Using a core filter; PHPCS suppressing a false positive.
		// @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		$args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

		$class_names = implode(
			' ',
			apply_filters(
				'nav_menu_css_class',
				array_filter( $classes ),
				$item,
				$args,
				$depth
			)
		);
		// @phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		// Using a core filter; PHPCS suppressing a false positive.
		// @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		$id = apply_filters(
			'nav_menu_item_id',
			'menu-item-' . $item->ID,
			$item,
			$args,
			$depth
		);
		// @phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $class_names . '>';

		$atts           = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		if ( '_blank' === $item->target && empty( $item->xfn ) ) {
			$atts['rel'] = 'noopener';
		} else {
			$atts['rel'] = $item->xfn;
		}
		if ( ! empty( $args->append_to_url ) ) {
			$atts['href'] = ! empty( $item->url ) ? $item->url .
				$args->append_to_url : '';
		} else {
			$atts['href'] = ! empty( $item->url ) ? $item->url : '';
		}
		$atts['aria-current'] = $item->current ? 'page' : '';

		// Using a core filter; PHPCS suppressing a false positive.
		// @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		$atts = apply_filters(
			'nav_menu_link_attributes',
			$atts,
			$item,
			$args,
			$depth
		);
		// @phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( is_scalar( $value ) && '' !== $value && false !== $value ) {
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		// Using a core filter; PHPCS suppressing a false positive.
		// @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		$title = apply_filters( 'the_title', $item->title, $item->ID );

		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );
		// @phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

		$item_output  = $args->before;
		$item_output .= '<a' . $attributes . '>';
		$item_output .= $args->link_before . $title . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		// Using a core filter; PHPCS suppressing a false positive.
		// @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		$output .= apply_filters(
			'walker_nav_menu_start_el',
			$item_output,
			$item,
			$depth,
			$args
		);
		// @phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
	}
}
