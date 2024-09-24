<?php
/**
 * Menu In Post options and settings.
 *
 * @return Nothing
 */
function initMenuInPostOptions() 
{
    // Register option array.
    register_setting(
        'menu-in-post', 
        'mip_options'
    );
    
    add_settings_section(
        'mip_options_section_js',
        __('Javascript Options', 'menu-in-post'), 
        'callbackMIPJSOptions',
        'menu-in-post-options'
    );
    
    /* 
        Register a new field in the "mip_options_section_js" section, on the 
        menu-in-post-options page.
        
        Note: As of WP 4.6, the settings ID is only used internally.
        
        Use the $args "label_for" to populate the ID inside the callback.
        
        Note: If you ever add or change settings, make sure you change the 
        defaults you have set for the get_options() function calls both in 
        admin.php and in menu-in-post.php.
    */
    add_settings_field(
        'miploadjs', 
        __('Load JavaScript:', 'menu-in-post'),
        'callbackMenuInPostFieldLoadJS',
        'menu-in-post-options',
        'mip_options_section_js',
        array(
            'label_for' => 'miploadjs',
            'class' => 'mip-option-row'
        )
    );
    add_settings_field(
        'miponlypages', 
        __('Only on Posts/Pages:'), 
        'callbackMenuInPostFieldOnlyPages', 
        'menu-in-post-options', 
        'mip_options_section_js', 
        array(
            'label_for' => 'miponlypages', 
            'class' => 'mip-options-row mip-only-pages-row'
        )
    );
    add_settings_field(
        'mipminimizejs', 
        __('Minimize JavaScript:'), 
        'callbackMenuInPostMinimizeJS', 
        'menu-in-post-options', 
        'mip_options_section_js', 
        array(
            'label_for' => 'mipminimizejs', 
            'class' => 'mip-option-row'
        )
    );
    
    if (wp_is_block_theme()) {
        add_settings_section(
            'mip_options_section_appearance_menus',
            __('Show Appearance > Menus Submenu Item', 'menu-in-post'), 
            'callbackMIPAppearanceMenus',
            'menu-in-post-options'
        );
        
        add_settings_field(
            'mipaddappearancemenu', 
            __('Show Appearance > Menus:'), 
            'callbackMIPShowAppearanceMenus', 
            'menu-in-post-options', 
            'mip_options_section_appearance_menus', 
            array(
                'label_for' => 'mipshowappearancemenus', 
                'class' => 'mip-option-row'
            )
        );
    }
}

/**
 * Register initMenuInPostOptions to the admin_init action hook.
 */
add_action('admin_init', 'initMenuInPostOptions');

/**
 * Output the HTML for the Menu In Post Options page intro.
 *
 * @param array $args Arguments passed to the callback function.
 *
 * @return Returns the HTML for the intro.
 */
function callbackMIPJSOptions($args) 
{
    $str = esc_html_e(
        'Use the following settings to control how Menu In Post&#39;s JavaScript ' . 
            'is handled. Click the Help Tab for more information.', 
        'menu-in-post'
    ); 
    ?>
    <p id="<?php echo esc_attr($args['id']); ?>">
       <?php echo $str; ?>
    </p>
    <?php
}

/**
 * Output the HTML for the Add Appearance > Menus Option description.
 *
 * @param array $args Arguments passed to the callback function.
 *
 * @return Returns the HTML for the intro.
 */
function callbackMIPAppearanceMenus($args) 
{
    $str = esc_html_e(
        'Since WordPress version 5.9, the Appearance > Menus menu item is hidden ' . 
            'for block-enabled themes. Set &quot;Show Appearance > Menus&quot; ' . 
            'to &quot;yes&quot; to display the menu.', 
        'menu-in-post'
    ); 
    ?>
    <p id="<?php echo esc_attr($args['id']); ?>">
       <?php echo $str; ?>
    </p>
    <?php
}

/**
 * Output the HTML for the Load JS option.
 *
 * @param array $args Arguments passed to the callback function.
 *
 * @return Returns the HTML.
 */
function callbackMenuInPostFieldLoadJS($args) 
{
    $options = get_option('mip_options');
    ?>
    <select
        id="<?php echo esc_attr($args['label_for']); ?>" 
        name="mip_options[<?php echo esc_attr($args['label_for']); ?>]"
    >
        <?php
        $vals = array(
            'always' => 'always (default)', 
            'never' => 'never', 
            'onlypages' => 'only on posts/pages'
        );
        foreach ($vals as $val => $desc) {
            $selected = '';
            if (isset($options[$args['label_for']]) 
                && $options[$args['label_for']] == $val
            ) {
                $selected = ' selected';
            }
            ?>
            <option 
                value="<?php echo $val; ?>"
                <?php echo $selected; ?>
            >
                <?php echo $desc; ?>
            </option>
            <?php
        }
        ?>
    </select>
    <?php
}

/**
 * Output the HTML for the Only Posts/Pages option.
 *
 * @param array $args Arguments passed to the callback function.
 *
 * @return Returns the HTML.
 */
function callbackMenuInPostFieldOnlyPages($args) 
{
    $options = get_option('mip_options');
    if (isset($options[$args['label_for']])) {
        $val = $options[$args['label_for']];
    } else {
        $val = '';
    }
    ?>
    <input 
        id="<?php echo esc_attr($args['label_for']); ?>" 
        name="mip_options[<?php echo esc_attr($args['label_for']); ?>]" 
        type="text" 
        placeholder="comma-separated post/page IDs" 
        value="<?php echo $val; ?>"
    >
    <?php
}

/**
 * Output the HTML for the Minimize JS option.
 *
 * @param array $args Arguments passed to the callback function.
 *
 * @return Returns the HTML.
 */
function callbackMenuInPostMinimizeJS($args) 
{
    $options = get_option('mip_options');
    if (isset($options[$args['label_for']])) {
        $selected = ' selected';
    } else {
        $selected = '';
    }
    ?>
    <select
        id="<?php echo esc_attr($args['label_for']); ?>" 
        name="mip_options[<?php echo esc_attr($args['label_for']); ?>]"
    >
        <?php
        $vals = array(
            'yes' => 'yes (default)', 
            'no' => 'no'
        );
        foreach ($vals as $val => $desc) {
            $selected = '';
            if (isset($options[$args['label_for']]) 
                && $options[$args['label_for']] == $val
            ) {
                $selected = ' selected';
            }
            ?>
            <option 
                value="<?php echo $val; ?>"
                <?php echo $selected; ?>
            >
                <?php echo $desc; ?>
            </option>
            <?php
        }
        ?>
    </select>
    <?php
}

/**
 * Output the HTML for the Show Menu Item option.
 *
 * @param array $args Arguments passed to the callback function.
 *
 * @return Returns the HTML.
 */
function callbackMIPShowAppearanceMenus($args) 
{
    $options = get_option('mip_options');
    if (isset($options[$args['label_for']])) {
        $selected = ' selected';
    } else {
        $selected = '';
    }
    ?>
    <select
        id="<?php echo esc_attr($args['label_for']); ?>" 
        name="mip_options[<?php echo esc_attr($args['label_for']); ?>]"
    >
        <?php
        $vals = array(
            'no' => 'no (default)', 
            'yes' => 'yes'
        );
        foreach ($vals as $val => $desc) {
            $selected = '';
            if (isset($options[$args['label_for']]) 
                && $options[$args['label_for']] == $val
            ) {
                $selected = ' selected';
            }
            ?>
            <option 
                value="<?php echo $val; ?>"
                <?php echo $selected; ?>
            >
                <?php echo $desc; ?>
            </option>
            <?php
        }
        ?>
    </select>
    <?php
}

/**
 * Create the Menu In Post menus for WordPress Admin.
 *
 * @return Returns the options for the Menu In Post menus.
 */
function addMenuInPostAdminMenus()
{
    $tools = add_submenu_page(
        'tools.php', 
        __('Menu In Post Tools', 'menu-in-post'), 
        __('Menu In Post Tools', 'menu-in-post'), 
        'manage_options', 
        'menu-in-post', 
        'outputMenuInPostToolsPageHTML'
    );
    add_action('load-' . $tools, 'loadMenuInPostToolsPage');
    
    $options = add_submenu_page(
        'options-general.php', 
        __('Menu In Post', 'menu-in-post'), 
        __('Menu In Post', 'menu-in-post'), 
        'manage_options', 
        'menu-in-post-options', 
        'outputMenuInPostOptionsHTML'
    );
    add_action('load-' . $options, 'loadMenuInPostOptionsPage');
}
add_action('admin_menu', 'addMenuInPostAdminMenus');

/**
 * Load the Menu In Post Tools Admin page and Help Tabs.
 *
 * @return Nothing
 */
function loadMenuInPostToolsPage()
{
    $help_tabs = new MENUINPOST_Help_Tabs(get_current_screen());
    $help_tabs->mipSetHelpTabs('tools');
}

/**
 * Load the Menu In Post Admin Settings page and Help Tabs.
 *
 * @return Nothing
 */
function loadMenuInPostOptionsPage() 
{
    $help_tabs = new MENUINPOST_Help_Tabs(get_current_screen());
    $help_tabs->mipSetHelpTabs('options');
}

/**
 * Output the HTML for the Menu In Post Settings page.
 *
 * @return Returns the HTML.
 */
function outputMenuInPostOptionsHTML() 
{
    if (!current_user_can('manage_options')) {
        return;
    }
    /* 
        Check to see if the user submitted the settings.
        
        WP adds the "settings-updated" $_GET parameter to the url.
    */
    if (isset($_GET['settings-updated'])) {
        // Add settings saved message with the class of "updated".
        add_settings_error(
            'mip_messages', 
            'mip_message', 
            __('Settings Saved', 'menu-in-post'), 
            'updated'
        );
    }

    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form 
            name="menu-in-post-options-form" 
            method="post" 
            action="options.php"
        >
            <?php
            settings_fields('menu-in-post');
            do_settings_sections('menu-in-post-options');
            submit_button('Save Settings');
            ?>
        </form>
    </div>
    <?php
}

/**
 * Output the HTML for the Menu In Post Tools page.
 *
 * @return Returns the HTML.
 */
function outputMenuInPostToolsPageHTML()
{
    if (!current_user_can('manage_options')) {
        return;
    }
    $options = get_option(
        'mip_options', 
        array(
            'miploadjs' => 'always', 
            'miponlypages' => '', 
            'mipminimizejs' => 'yes'
        )
    );
    $shortcode = '';
    /* 
        Fallback for no JavaScript. Normally, the form is submitted via JavaScript, 
        and everything is done client-side. 
    */
    if (isset($_POST['mip_menu'])) {
        $atts = array(
        'menu'=>'mip_menu', 
        'menu_class'=>'mip_menu_class', 
        'menu_id'=>'mip_menu_id',  
        'container'=>'mip_container', 
        'container_class'=>'mip_container_class', 
        'container_id'=>'mip_container_id', 
        'depth'=>'mip_depth', 
        'style'=>'mip_style', 
        'placeholder_text'=>'mip_placeholder_text', 
        'append_to_url'=>'mip_append_to_url'
        );
        foreach ($atts as $att=>$field) {
            switch($att) {
            case 'menu':
            case 'depth':
            case 'container':
                $$att = absint($_POST[$field]);
                break;
            break;
            // These should only be strings.
            default:
                $$att = trim(sanitize_text_field($_POST[$field]));
            }
        }
        // Build the shortcode.
        $shortcode = '[menu_in_post_menu';
        foreach ($atts as $att=>$field) {
            switch($att) {
            case 'menu':
                $shortcode .= ' ' . $att . '=' . $$att;
                break;
            case 'depth':
                if ($depth > 0) {
                    $shortcode .= ' depth=' . $depth;
                }
                break;
            case 'style':
                if ($style != 'list') {
                    $shortcode .= ' style=' . $style;
                }
                break;
            case 'container':
                if ($container == 0) {
                    $shortcode .= ' container=&#34;false&#34;';
                }
                break;
            default:
                if ($$att != '') {
                    $shortcode .= ' ' . $att . '=&#34;' . $$att . '&#34;';
                }
            }
        }
        $shortcode .= ']';
    }
    /* ************************* End of JavaScript fallback. ********************* */
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(__('Menu In Post Tools', 'menu-in-post')); ?></h1>
        <?php
        $str = '';
        $title = esc_html(__('Note:', 'menu-in-post'));
        switch($options['miploadjs']) {
        case 'never':
            $str = esc_html(
                __(
                    'Dropdown-style menus will not work with your current ' . 
                    'setting of &quot;never&quot; in Settings > Menu In Post, ' . 
                    'Load JavaScript.', 'menu-in-post'
                )
            ); 
            break;
        case 'onlypages':
            $str = esc_html(
                __(
                    'Dropdown-style menus will not work with your current ' . 
                    'setting of &quot;only on posts/pages&quot; in Settings > ' . 
                    'Menu In Post > Menu In Post Options, Load JavaScript ' . 
                    'unless you have set the correct page ID(s) in &quot;Only on ' . 
                    'Posts/Pages&quot;.', 
                    'menu-in-post'
                )
            );
            break;
        }
        if ($str !== '') {
            /*
                Use the wp_admin_notice() function, added in WP 6.4, if possible.
                
                Fall back to HTML output if < WP 6.4.
                
                You can remove the conditional for WP version once there has 
                been time for everyone to upgrade to >= WP 6.4.
            */
            if (is_wp_version_compatible('6.4')) {
                wp_admin_notice(
                    __('<strong>' . $title . '</strong> ' . $str, 'menu-in-post'),
                    array(
                        'type' => 'warning',
                        'dismissible' => false
                    )
                );
            } else {
                ?>
                <div class="notice notice-warning">
                    <p>
                        <strong><?php echo $title; ?></strong> <?php echo $str; ?>
                    </p>
                </div>
                <?php
            }
        } 
        ?>
        <p>
            <?php 
                echo esc_html(
                    __(
                        'Use the form below to create shortcodes to display ' . 
                            'menus in posts and pages. Paste the shortcodes ' . 
                            'you create in a Shortcode Block to display them.', 
                        'menu-in-post'
                    )
                ); 
            ?>
        </p>
        <h2><?php echo esc_html(__('Shortcode Builder', 'menu-in-post')); ?></h2>
    <?php
        $menus = wp_get_nav_menus();
    if (is_array($menus) && count($menus) > 0) {
        ?>
        <form 
            name="mip_shortcode_builder_form" 
            id="mip_shortcode_builder_form" 
            method="post"
        >
            <div class="inputgroup">
                <div class="inputrow">
                    <label for="mip_menu">
                    <?php 
                        echo esc_html(__('Select Menu', 'menu-in-post')); 
                    ?>:
                    </label>
                    <select name="mip_menu" id="mip_menu">
                    <?php
                    foreach ($menus as $menu) {
                        echo '<option value="' . $menu->term_id . '">' . 
                            $menu->name . '</option>';
                    }
                    ?>
                    </select>
                </div>
                <div class="inputrow">
                    <label for="mip_container">
                    <?php 
                        echo esc_html(
                            __('Include Container', 'menu-in-post')
                        ); 
                    ?>:
                    </label>
                    <select name="mip_container" id="mip_container">
                        <option value="1">
                        <?php echo esc_html(__('Yes', 'menu-in-post')); ?>
                        </option>
                        <option value="0">
                        <?php echo esc_html(__('No', 'menu-in-post')); ?>
                        </option>
                    </select>
                </div>
                <div class="inputrow">
                    <label for="mip_container_id">
                        <?php 
                        echo esc_html(__('Container ID', 'menu-in-post')); 
                        ?>:
                    </label>
                    <input 
                        type="text" 
                        name="mip_container_id" 
                        id="mip_container_id"
                    >
                </div>
                <div class="inputrow">
                    <label for="mip_container_class">
                        <?php 
                        echo esc_html(
                            __('Container Class(es)', 'menu-in-post')
                        ); 
                        ?>:
                    </label>
                    <input 
                        type="text" 
                        name="mip_container_class" 
                        id="mip_container_class"
                    >
                </div>
                <div class="inputrow">
                    <label for="mip_menu_id">
                        <?php echo esc_html(__('Menu ID', 'menu-in-post')); ?>:
                    </label>
                    <input type="text" name="mip_menu_id" id="mip_menu_id">
                </div>
                <div class="inputrow">
                    <label for="mip_menu_class">
                        <?php 
                            echo esc_html(__('Menu Class(es)', 'menu-in-post')); 
                        ?>:
                    </label>
                    <input type="text" name="mip_menu_class" id="mip_menu_class">
                </div>
                <div class="inputrow">
                    <label for="mip_depth">
                        <?php echo esc_html(__('Depth', 'menu-in-post')); ?>:
                    </label>
                    <select name="mip_depth" id="mip_depth">
                        <?php
                        $depth_options = array(
                            0=>esc_html(__('All Levels', 'menu-in-post')), 
                            1=>esc_html(__('1 Level', 'menu-in-post')), 
                            2=>esc_html(__('2 Levels', 'menu-in-post')), 
                            3=>esc_html(__('3 Levels', 'menu-in-post')), 
                            4=>esc_html(__('4 Levels', 'menu-in-post')), 
                            5=>esc_html(__('5 Levels', 'menu-in-post'))
                        );
                        foreach ($depth_options as $value=>$text) {
                            if ($value == 0) {
                                echo '<option value="' . $value . 
                                    '" selected="selected">' . $text . 
                                    '</option>';
                            } else {
                                echo '<option value="' . $value . '">' . 
                                    $text . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="inputrow">
                    <label for="mip_style">
                        <?php echo esc_html(__('Style', 'menu-in-post')); ?>:
                    </label>
                    <select name="mip_style" id="mip_style">
                        <?php
                        $style_options = array(
                            'list'=>esc_html(
                                __('List of Links', 'menu-in-post')
                            ), 
                            'dropdown'=>esc_html(__('Dropdown', 'menu-in-post'))
                        );
                        foreach ($style_options as $value=>$text) {
                            if ($value == 'list') {
                                echo '<option value="' . $value . 
                                    '" selected="selected">' . $text . 
                                    '</option>';
                            } else {
                                echo '<option value="' . $value . '">' . 
                                    $text . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="inputrow">
                    <label for="mip_placeholder_text">
                        <?php 
                            echo esc_html(
                                __('Placeholder Text', 'menu-in-post')
                            ); 
                        ?>:
                    </label>
                    <input 
                        type="text" 
                        name="mip_placeholder_text" 
                        id="mip_placeholder_text"
                    >
                </div>
                <div class="inputrow">
                    <label for="mip_append_to_url">
                        <?php 
                            echo esc_html(__('Append to URL', 'menu-in-post')); 
                        ?>:
                    </label>
                    <input 
                        type="text" 
                        name="mip_append_to_url" 
                        id="mip_append_to_url"
                    >
                </div>
            </div><br>
            <input 
                type="submit" 
                name="mip_build" 
                value="<?php 
                    echo esc_attr(__('Build the Shortcode', 'menu-in-post')); 
                ?>"
            >
        </form>
        <div>
            <label for="mip_shortcode_builder_output">
                <?php echo esc_html(__('Shortcode', 'menu-in-post')); ?>:
            </label>
            <div class="mip_shortcode_output_hightlight">
                <input 
                    type="text" 
                    name="mip_shortcode_builder_output" 
                    id="mip_shortcode_builder_output" 
                    value="<?php echo $shortcode; ?>" 
                    readonly
                >
            </div>
            <div>
                <button 
                    type="button" 
                    id="mip_shortcode_output_copy_button"
                >
                <?php echo esc_html(__('Copy Shortcode', 'menu-in-post')); ?>
                </button>&nbsp;
                <span id="mip_shortcode_copy_success">
                    <?php echo esc_html(__('Copied...', 'menu-in-post')); ?>
                </span>
            </div>
      </div>
    </div>
        <?php
    } else {
        $str = esc_html(
            __(
                'You must create one or more menus (Appearance > Menus) ' . 
                    'prior to using Menu In Post&#39;s Shortcode Builder.', 
                'menu-in-post'
            )
        );
        /*
            Use the wp_admin_notice() function, added in WP 6.4, if possible. 
            
            Fall back to HTML output if < WP 6.4.
            
            You can remove the conditional for WP version once there has 
            been time for everyone to upgrade to >= WP 6.4.
        */
        if (is_wp_version_compatible('6.4')) {
            wp_admin_notice(
                __($str, 'menu-in-post'),
                array(
                    'type' => 'warning',
                    'dismissible' => true
                )
            );
        } else {
            ?>
            <div class="notice notice-warning is-dismissible">
                <p><?php echo $str; ?></p>
            </div>
            <?php
        }
    }
}

/**
 * Enqueue the scripts and stylesheet(s) required for Menu In Post Admin pages.
 * Option: $live can be set to false during development in order to load 
 * unminified versions of CSS and JS in Admin.
 *
 * @param string $hook Hook associated with the Tools page.
 *
 * @return Nothing.
 */
function enqueueMenuInPostAdminScripts($hook)
{
    if ($hook != 'tools_page_menu-in-post' 
        && $hook != 'settings_page_menu-in-post-options'
    ) {
        return;
    }
    $options = get_option(
        'mip_options', 
        array(
            'miploadjs' => 'always', 
            'miponlypages' => '', 
            'mipminimizejs' => 'yes'
        )
    );
    if ($options['mipminimizejs'] == 'yes') {
        $min = '-min';
    } else {
        $min = '';
    }
    wp_enqueue_style(
        'menu_in_post_admin_style', 
        plugins_url('css/style' . $min . '.css', __FILE__)
    );
    wp_enqueue_script(
        'menu_in_post_admin_script', 
        plugins_url('js/main' . $min . '.js', __FILE__), 
        array('jquery')
    );
}
add_action('admin_enqueue_scripts', 'enqueueMenuInPostAdminScripts');

/**
 * Optionally, add back the Appearance > Menus menu subitem so that users 
 * of block themes can add/edit classic menus for use in Menu In Post by 
 * registering the existing Menus menu that was removed in WP 5.9+ for 
 * block-enabled themes.
 *
 * @return Nothing.
 */
function getBackAppearanceMenus() 
{
    $options = get_option(
        'mip_options', 
        array('mipshowappearancemenus' => 'no')
    );
    if (array_key_exists('mipshowappearancemenus', $options)) {
        if ($options['mipshowappearancemenus'] == 'yes' && wp_is_block_theme()) {
            register_nav_menus(
                array(
                    'primary' => esc_html__('Primary Menu',
                    'raft'
                    )
                )
            );
        }
    }
}
add_action('init', 'getBackAppearanceMenus');
?>
