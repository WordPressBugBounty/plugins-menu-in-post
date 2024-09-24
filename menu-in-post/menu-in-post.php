<?php
/**
 * Plugin Name: Menu In Post
 * Description: A simple but flexible plugin to add menus to a post or page.
 * Author: linux4me2
 * Author URI: https://profiles.wordpress.org/linux4me2
 * Text Domain: menu-in-post
 * Version: 1.3
 * License: GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0-standalone.html
 */

defined('ABSPATH') or die('No direct access.');

define('MENUINPOST_PLUGIN', __FILE__);
define('MENUINPOST_PLUGIN_DIR', untrailingslashit(dirname(MENUINPOST_PLUGIN)));

if (is_admin()) {
    include_once MENUINPOST_PLUGIN_DIR . '/admin/help-tabs.php';
    include_once MENUINPOST_PLUGIN_DIR . '/admin/admin.php';
}

add_shortcode('menu_in_post_menu', 'outputMenuInPostMenu');

/**
 * Initiates the output of a Menu In Post Menu.
 *
 * @param array $atts The attributes of the menu.
 *
 * @return Returns the menu via wp_nav_menu()
 */
function outputMenuInPostMenu($atts = array())
{
    if (isset($atts['menu'])) {
        $menu = absint($atts['menu']);
    } else {
        $menu = 0;
    }
    if ($menu == 0) {
        return fallbackMenuInPost();
    } else {
        $args = array(
            'menu'=>$menu, 
            'fallback_cb'=>'fallbackMenuInPost', 
            'echo'=>false
        );
    }
    /* 
        If menu_id is empty, don't pass a value, and the menu slug with an 
        incremented value added will be used. 
         
        If container_class is empty, don't pass a value and 
        'menu-{menu slug}-container' will be used.
    */
    $defaults = array(
        'menu_class'=>'menu',
        'menu_id'=>'',    
        'container'=>'div', 
        'container_class'=>'', 
        'container_id'=>'', 
        'style'=>'list', 
        'placeholder_text'=>esc_html(__('Select...', 'menu-in-post')), 
        'append_to_url'=>'', 
        'depth'=>0 
    );
    foreach ($defaults as $att=>$default) {
        switch($att) {
        case 'depth':
            if (isset($atts[$att])) {
                 $passed_depth = absint($atts[$att]);
                if ($passed_depth > 0) {
                    $args['depth'] = $passed_depth;
                }
            } else {
                $atts['depth'] = $default;
            }
            break;
        // These should be only strings.
        default:
            if (isset($atts[$att])) {
                $passed_att = sanitize_text_field($atts[$att]);
                if ($passed_att != '') {
                    $args[$att] = $passed_att;
                }
            } else {
                $atts[$att] = $default;
            }
        }    
    }
    if ($atts['style'] == 'dropdown') {
        $select = '<select class="mip-drop-nav"';
        if ($atts['menu_id'] != '') {
            $select .= ' id="' . $args['menu_id'] . '"';
        }
        $select .= '>';
        $args['items_wrap'] = $select . '<option value="#">' . 
            $atts['placeholder_text'] . '</option>%3$s</select>';
        $args['walker'] = new MIPWalkerNavMenuDropdownBuilder();
    } else {
        if ($atts['append_to_url'] != '') {
            $args['walker'] = new MIPWalkerNavMenuListBuilder();
        } 
    }
    if ($atts['append_to_url'] != '') {
        $args['append_to_url'] = $atts['append_to_url'];
    }
    return wp_nav_menu($args);
}

/**
 * Menu In Post fallback function.
 *
 * @return Nothing
 */
function fallbackMenuInPost()
{
    return;    
}

add_action('wp_enqueue_scripts', 'enqueueMenuInPostFrontEndJS');

/**
 * Selectively enqueues the JavaScript for Menu In Post
 *
 * @return Nothing, but it runs wp_enqueue_script().
 */
function enqueueMenuInPostFrontEndJS()
{
    $options = get_option(
        'mip_options', 
        array(
            'miploadjs' => 'always', 
            'miponlypages' => '', 
            'mipminimizejs' => 'yes'
        )
    );
    
    $load = false;
    $loadjs = $options['miploadjs'];
    if ($options['mipminimizejs'] === 'yes') {
        $file = 'main-min.js';
    } else {
        $file = 'main.js';
    }
    
    switch($loadjs) {
    case 'always':
        $load = true;
        break;
    case 'onlypages':
        $pagestr = trim(str_replace(' ', '', $options['miponlypages']));
        if ($pagestr !== '') {
            $pages = explode(',', $pagestr);
            if (is_array($pages)) {
                $pageid = get_queried_object_id();
                if (in_array($pageid, $pages)) {
                    $load = true;
                }
            }
        }
        break;
    }
    
    if ($load === true) {
        wp_enqueue_script(
            'menu_in_post_frontend_script', 
            plugins_url('js/' . $file, __FILE__), 
            array('jquery')
        );
    }
}

class MIPWalkerNavMenuDropdownBuilder extends Walker_Nav_Menu
{
    /**
     * Starts the list before the elements are added.
     *
     * @param string   $output Used to append additional content (passed by ref).
     * @param int      $depth  Depth of menu item. Used for padding.
     * @param stdClass $args   An object of wp_nav_menu() arguments.
     *
     * @return Nothing here.
     */
    function start_lvl(&$output, $depth = 0, $args = null)
    {
    }
    
    /**
     * Ends the list of after the elements are added.
     *
     * @param string   $output Used to append additional content (passed by ref).
     * @param int      $depth  Depth of menu item. Used for padding.
     * @param stdClass $args   An object of wp_nav_menu() arguments.
     *
     * @return Nothing here.
     */
    function end_lvl(&$output, $depth = 0, $args = null)
    {
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
     * @return Returns the HTML output for the element.
     */
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
    {
        // Create each option.
        $item_output = '';

        // Add spacing to the title based on the depth.
        $item->title = str_repeat(' - ', $depth * 1) . $item->title;

        // Get the link.
        if (!empty($args->append_to_url)) {
            $attributes = !empty($item->url) ? ' value="' . esc_attr($item->url) . 
                $args->append_to_url .'"' : '';
        } else {
            $attributes = !empty($item->url) ? ' value="' . esc_attr($item->url) . 
                '"' : '';
        }
        
        // Get the target, if any.
        if (!empty($item->target)) {
            $attributes .= ' data-target="' . esc_attr($item->target) . '"';
        }
        
        // Add selected attribute if menu item is the current page.
        if ($item->current) {
            $attributes .= ' selected="selected"';
        }
        
        // Add the HTML.
        $item_output .= '<option'. $attributes .'>';
        $item_output .= apply_filters('the_title_attribute', $item->title);

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
     * @return Returns the closing tag, if needed.
     */
    function end_el(&$output, $item, $depth = 0, $args = null)
    {
        // Close the item.
        $output .= "</option>\n";

    }

}

class MIPWalkerNavMenuListBuilder extends Walker_Nav_Menu
{
    /**
     * Starts the element output.
     *
     * @param string   $output Appends additional content (passed by reference).
     * @param WP_Post  $item   Menu item data object.
     * @param int      $depth  Depth of menu item. Used for padding.
     * @param stdClass $args   An object of wp_nav_menu() arguments.
     * @param int      $id     The current menu item. Default 0.
     *
     * @return Returns the HTML output for the element.
     */
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
    {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = ( $depth ) ? str_repeat($t, $depth) : '';
 
        $classes   = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
 
        $args = apply_filters('nav_menu_item_args', $args, $item, $depth);
 
        $class_names = implode(
            ' ', 
            apply_filters(
                'nav_menu_css_class', 
                array_filter($classes), 
                $item, 
                $args, 
                $depth
            )
        );
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
 
        $id = apply_filters(
            'nav_menu_item_id', 
            'menu-item-' . $item->ID, 
            $item, 
            $args, 
            $depth
        );
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';
 
        $output .= $indent . '<li' . $id . $class_names . '>';
 
        $atts           = array();
        $atts['title']  = !empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = !empty($item->target) ? $item->target : '';
        if ('_blank' === $item->target && empty($item->xfn) ) {
            $atts['rel'] = 'noopener';
        } else {
            $atts['rel'] = $item->xfn;
        }
        if (!empty($args->append_to_url)) {
            $atts['href'] = !empty($item->url) ? $item->url . 
                $args->append_to_url : '';
        } else {
            $atts['href'] = !empty($item->url) ? $item->url : '';
        }
        $atts['aria-current'] = $item->current ? 'page' : '';
 
        $atts = apply_filters(
            'nav_menu_link_attributes', 
            $atts, 
            $item, 
            $args, 
            $depth
        );
 
        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if (is_scalar($value) && '' !== $value && false !== $value ) {
                $value = ( 'href' === $attr ) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }
 
        $title = apply_filters('the_title', $item->title, $item->ID);
 
        $title = apply_filters('nav_menu_item_title', $title, $item, $args, $depth);
 
        $item_output  = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . $title . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;
 
        $output .= apply_filters(
            'walker_nav_menu_start_el', 
            $item_output, 
            $item, 
            $depth, 
            $args
        );
    }
}
?>