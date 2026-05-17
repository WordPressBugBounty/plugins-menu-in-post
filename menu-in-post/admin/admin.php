<?php
/**
 * Admin functions for Menu In Post
 *
 * @package menu-in-post
 * @since 1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sanitize mip_options before saving.
 *
 * Validates and cleans the option array saved under the MIP_OPTION_NAME option:
 * - miploadjs: must be one of 'always', 'never', 'onlypages'; defaults to existing value or 'always'.
 * - miponlypages: comma-separated list of post/page IDs; non-numeric characters removed, IDs 
 *  normalized to unique positive integers, stored as a comma-separated string.
 * - mipminimizejs: must be 'yes' or 'no'; defaults to existing value or 'yes'.
 * - mipshowappearancemenus: must be 'yes' or 'no'; defaults to existing value or 'no'.
 *
 * The function loads the existing option to preserve unknown or read-only keys when an input is missing,
 * and always returns a deterministic, side-effect-free cleaned array suitable for register_setting().
 *
 * @param array $input Raw input from the settings form (expected as mip_options[]).
 * @return array Cleaned mip_options array ready for storage.
 */
function menuinpost_sanitize_options( $input ) {
	$clean = array();

	// Load existing to preserve unknown/read-only keys if needed.
	$existing = get_option( MIP_OPTION_NAME, array() );

	// miploadjs: one of 'always', 'never', 'onlypages'
	$allowed_loadjs = array( 'always', 'never', 'onlypages' );
	$clean['miploadjs'] = ( isset( $input['miploadjs'] ) && in_array( $input['miploadjs'], $allowed_loadjs, true ) )
		? $input['miploadjs']
		: ( isset( $existing['miploadjs'] ) ? $existing['miploadjs'] : 'always' );

	// miponlypages: comma-separated integers (post/page IDs) -> store as sanitized comma list
	if ( isset( $input['miponlypages'] ) ) {
		$raw = trim( (string) $input['miponlypages'] );
		if ( $raw === '' ) {
			$clean['miponlypages'] = '';
		} else {
			$parts = array_map( 'trim', explode( ',', $raw ) );
			$ints = array();
			foreach ( $parts as $p ) {
				$p = preg_replace( '/\D+/', '', $p );
				if ( $p !== '' ) {
					$ints[] = absint( $p );
				}
			}
			$clean['miponlypages'] = implode( ',', array_unique( array_filter( $ints ) ) );
		}
	} else {
		$clean['miponlypages'] = isset( $existing['miponlypages'] ) ? $existing['miponlypages'] : '';
	}

	// mipminimizejs: 'yes' or 'no'
	$allowed_min = array( 'yes', 'no' );
	$clean['mipminimizejs'] = ( isset( $input['mipminimizejs'] ) && in_array( $input['mipminimizejs'], $allowed_min, true ) )
		? $input['mipminimizejs']
		: ( isset( $existing['mipminimizejs'] ) ? $existing['mipminimizejs'] : 'yes' );

	// mipshowappearancemenus: 'yes' or 'no'
	$clean['mipshowappearancemenus'] = ( isset( $input['mipshowappearancemenus'] ) && in_array( $input['mipshowappearancemenus'], $allowed_min, true ) )
		? $input['mipshowappearancemenus']
		: ( isset( $existing['mipshowappearancemenus'] ) ? $existing['mipshowappearancemenus'] : 'no' );

	return $clean;
}


/**
 * Menu In Post options and settings.
 *
 * @return void
 */
function menuinpost_init_options() {
	// Register option array.
	register_setting(
		'menu-in-post',
		MIP_OPTION_NAME,
		array( 'sanitize_callback' => 'menuinpost_sanitize_options' )
	);

	add_settings_section(
		'mip_options_section_js',
		__( 'Javascript Options', 'menu-in-post' ),
		'callback_mipjs_options',
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
		__( 'Load JavaScript:', 'menu-in-post' ),
		'callback_mip_field_load_js',
		'menu-in-post-options',
		'mip_options_section_js',
		array(
			'label_for' => 'miploadjs',
			'class'     => 'mip-option-row',
		)
	);
	add_settings_field(
		'miponlypages',
		__( 'Only on Posts/Pages:', 'menu-in-post' ),
		'callback_mip_field_only_pages',
		'menu-in-post-options',
		'mip_options_section_js',
		array(
			'label_for' => 'miponlypages',
			'class'     => 'mip-options-row mip-only-pages-row',
		)
	);
	add_settings_field(
		'mipminimizejs',
		__( 'Minimize JavaScript:', 'menu-in-post' ),
		'callback_mip_minimize_js',
		'menu-in-post-options',
		'mip_options_section_js',
		array(
			'label_for' => 'mipminimizejs',
			'class'     => 'mip-option-row',
		)
	);

	if ( wp_is_block_theme() ) {
		add_settings_section(
			'mip_options_section_appearance_menus',
			__( 'Show Appearance > Menus Submenu Item', 'menu-in-post' ),
			'callback_mip_appearance_menus',
			'menu-in-post-options'
		);

		add_settings_field(
			'mipaddappearancemenu',
			__( 'Show Appearance > Menus:', 'menu-in-post' ),
			'callback_mip_show_appearance_menus',
			'menu-in-post-options',
			'mip_options_section_appearance_menus',
			array(
				'label_for' => 'mipshowappearancemenus',
				'class'     => 'mip-option-row',
			)
		);
	}
}

/**
 * Register menuinpost_init_options to the admin_init action hook.
 */
add_action( 'admin_init', 'menuinpost_init_options' );

/**
 * Output the HTML for the Menu In Post Options page intro.
 *
 * @param array $args Arguments passed to the callback function.
 *
 * @return void
 */
function callback_mipjs_options( $args ) {
	// Returns the HTML for the intro.
	$str = esc_html__(
		"Use the following settings to control how Menu In Post's JavaScript is handled. Click the Help Tab for more information.",
		'menu-in-post'
	);
	?>
	<p id="<?php echo esc_attr( $args['id'] ); ?>">
		<?php echo esc_html( $str ); // $str is already escaped, but still generates a warning, so escape it again ?>
	</p>
	<?php
}

/**
 * Output the HTML for the Add Appearance > Menus Option description.
 *
 * @param array $args Arguments passed to the callback function.
 *
 * @return void
 */
function callback_mip_appearance_menus( $args ) {
	// Returns the HTML for the intro.
	$str = esc_html__(
		'Since WordPress version 5.9, the Appearance > Menus menu item is hidden for block-enabled themes. Set "Show Appearance > Menus" to "yes" to display the menu.',
		'menu-in-post'
	);
	?>
	<p id="<?php echo esc_attr( $args['id'] ); ?>">
		<?php echo esc_html( $str ); // $str is already escaped above, but still generates a warning, so escape it again. ?>
	</p>
	<?php
}

/**
 * Output the HTML for the Load JS option.
 *
 * @param array $args Arguments passed to the callback function.
 *
 * @return void
 */
function callback_mip_field_load_js( $args ) {
	// Returns the HTML.
	$options = get_option( MIP_OPTION_NAME );
	?>
	<select
		id="<?php echo esc_attr( $args['label_for'] ); ?>" 
		name="mip_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
	>
		<?php
		$vals = array(
			'always'    => 'always (default)',
			'never'     => 'never',
			'onlypages' => 'only on posts/pages',
		);
		foreach ( $vals as $val => $desc ) {
			$selected = '';
			if ( isset( $options[ $args['label_for'] ] )
				&& $options[ $args['label_for'] ] === $val
			) {
				$selected = ' selected';
			}
			?>
			<option 
				value="<?php echo esc_html( $val ); ?>"
				<?php echo esc_html( $selected ); ?>
			>
				<?php echo esc_html( $desc ); ?>
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
 * @return void
 */
function callback_mip_field_only_pages( $args ) {
	// Returns the HTML.
	$options = get_option( MIP_OPTION_NAME );
	if ( isset( $options[ $args['label_for'] ] ) ) {
		$val = $options[ $args['label_for'] ];
	} else {
		$val = '';
	}
	?>
	<input 
		id="<?php echo esc_attr( $args['label_for'] ); ?>" 
		name="mip_options[<?php echo esc_attr( $args['label_for'] ); ?>]" 
		type="text" 
		placeholder="comma-separated post/page IDs" 
		value="<?php echo esc_html( $val ); ?>"
	>
	<?php
}

/**
 * Output the HTML for the Minimize JS option.
 *
 * @param array $args Arguments passed to the callback function.
 *
 * @return void
 */
function callback_mip_minimize_js( $args ) {
	// Returns the HTML.
	$options = get_option( MIP_OPTION_NAME );
	if ( isset( $options[ $args['label_for'] ] ) ) {
		$selected = ' selected';
	} else {
		$selected = '';
	}
	?>
	<select
		id="<?php echo esc_attr( $args['label_for'] ); ?>" 
		name="mip_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
	>
		<?php
		$vals = array(
			'yes' => 'yes (default)',
			'no'  => 'no',
		);
		foreach ( $vals as $val => $desc ) {
			$selected = '';
			if ( isset( $options[ $args['label_for'] ] )
				&& $options[ $args['label_for'] ] === $val
			) {
				$selected = ' selected';
			}
			?>
			<option 
				value="<?php echo esc_html( $val ); ?>"
				<?php echo esc_html( $selected ); ?>
			>
				<?php echo esc_html( $desc ); ?>
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
 * @return void
 */
function callback_mip_show_appearance_menus( $args ) {
	// Returns the HTML.
	$options = get_option( MIP_OPTION_NAME );
	if ( isset( $options[ $args['label_for'] ] ) ) {
		$selected = ' selected';
	} else {
		$selected = '';
	}
	?>
	<select
		id="<?php echo esc_attr( $args['label_for'] ); ?>" 
		name="mip_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
	>
		<?php
		$vals = array(
			'no'  => 'no (default)',
			'yes' => 'yes',
		);
		foreach ( $vals as $val => $desc ) {
			$selected = '';
			if ( isset( $options[ $args['label_for'] ] )
				&& $options[ $args['label_for'] ] === $val
			) {
				$selected = ' selected';
			}
			?>
			<option 
				value="<?php echo esc_html( $val ); ?>"
				<?php echo esc_html( $selected ); ?>
			>
				<?php echo esc_html( $desc ); ?>
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
 * @return void
 */
function menuinpost_add_admin_menus() {
	// Returns the options for the Menu In Post menus.
	$tools = add_submenu_page(
		'tools.php',
		__( 'Menu In Post Tools', 'menu-in-post' ),
		__( 'Menu In Post Tools', 'menu-in-post' ),
		'manage_options',
		'menu-in-post',
		'menuinpost_output_tools_page_html'
	);
	add_action( 'load-' . $tools, 'menuinpost_load_tools_page' );

	$options = add_submenu_page(
		'options-general.php',
		__( 'Menu In Post', 'menu-in-post' ),
		__( 'Menu In Post', 'menu-in-post' ),
		'manage_options',
		'menu-in-post-options',
		'menuinpost_output_post_options_html'
	);
	add_action( 'load-' . $options, 'menuinpost_load_options_page' );
}
add_action( 'admin_menu', 'menuinpost_add_admin_menus' );

/**
 * Load the Menu In Post Tools Admin page and Help Tabs.
 *
 * @return void
 */
function menuinpost_load_tools_page() {
	$help_tabs = new \MenuInPost\MIPHelpTabs( get_current_screen() );
	$help_tabs->mip_set_help_tabs( 'tools' );
}

/**
 * Load the Menu In Post Admin Settings page and Help Tabs.
 *
 * @return void
 */
function menuinpost_load_options_page() {
	$help_tabs = new \MenuInPost\MIPHelpTabs( get_current_screen() );
	$help_tabs->mip_set_help_tabs( 'options' );
}

/**
 * Output the HTML for the Menu In Post Settings page.
 *
 * @return void
 */
function menuinpost_output_post_options_html() {
	// Returns the HTML.
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	// Check to see if the user submitted the settings.
	// WP adds the "settings-updated" $_GET parameter to the url.
	if ( isset( $_GET['settings-updated'] ) ) {
		/* Verify the nonce that settings_fields() printed. */
		if ( isset( $_POST['option_page'] ) && isset( $_POST['_wpnonce'] ) ) {

			// Unsanitize the raw POST values.
			$option_page = sanitize_key( wp_unslash( $_POST['option_page'] ) );
			$nonce       = sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) );

			// The second argument is the name of the nonce field (default is '_wpnonce').
			check_admin_referer( $option_page, '_wpnonce' );
		}

		// Add settings saved message with the class of "updated".
		add_settings_error(
			'mip_messages',
			'mip_message',
			__( 'Settings Saved', 'menu-in-post' ),
			'updated'
		);
	}

	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form 
			name="menu-in-post-options-form" 
			method="post" 
			action="options.php"
		>
			<?php
			settings_fields( 'menu-in-post' );
			do_settings_sections( 'menu-in-post-options' );
			submit_button( 'Save Settings' );
			?>
		</form>
	</div>
	<?php
}

/**
 * Output the HTML for the Menu In Post Tools page.
 *
 * @return void
 */
function menuinpost_output_tools_page_html() {
	// Returns the HTML.
	// In the Tools page callback (runs on every load).
	if ( isset( $_GET['_wpnonce'] ) ) {
		// Uns­lash and sanitise the value – PHPCS‑friendly.
		$nonce = sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) );

		// Verify it against the current screen’s action.
		// The first argument is the *action* you want to protect;
		// the second argument is the name of the GET variable (default is '_wpnonce').
		check_admin_referer( 'menu-in-post', '_wpnonce' );
	}

	// Capability check.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission for this task.', 'menu-in-post' ), 403 );
	}

	$options   = get_option(
		MIP_OPTION_NAME,
		array(
			'miploadjs'     => 'always',
			'miponlypages'  => '',
			'mipminimizejs' => 'yes',
		)
	);
	$shortcode = '';
	// Fallback for no JavaScript. Normally, the form is submitted via JavaScript,
	// and everything is done client-side.
	if ( isset( $_POST['mip_menu'] ) ) {
		$atts = array(
			'menu'             => 'mip_menu',
			'menu_class'       => 'mip_menu_class',
			'menu_id'          => 'mip_menu_id',
			'container'        => 'mip_container',
			'container_class'  => 'mip_container_class',
			'container_id'     => 'mip_container_id',
			'depth'            => 'mip_depth',
			'style'            => 'mip_style',
			'placeholder_text' => 'mip_placeholder_text',
			'append_to_url'    => 'mip_append_to_url',
		);
		foreach ( $atts as $att => $field ) {
			if ( isset( $_POST[ $field ] ) ) {
				switch ( $att ) {
					case 'menu':
					case 'depth':
					case 'container':
						$$att = absint( $_POST[ $field ] );
						break;
					// These should only be strings.
					default:
						$$att = trim( sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
				}
			}
		}
		// Build the shortcode.
		$shortcode = '[menu_in_post_menu';
		foreach ( $atts as $att => $field ) {
			switch ( $att ) {
				case 'menu':
					$shortcode .= ' ' . $att . '=' . $$att;
					break;
				case 'depth':
					if ( $depth > 0 ) {
						$shortcode .= ' depth=' . $depth;
					}
					break;
				case 'style':
					if ( 'list' !== $style ) {
						$shortcode .= ' style=' . $style;
					}
					break;
				case 'container':
					if ( 0 === $container ) {
						$shortcode .= ' container=&#34;false&#34;';
					}
					break;
				default:
					if ( '' !== $$att ) {
						$shortcode .= ' ' . $att . '=&#34;' . $$att . '&#34;';
					}
			}
		}
		$shortcode .= ']';
	}
	/* ************************* End of JavaScript fallback. ********************* */
	?>
	<div class="wrap">
		<h1><?php echo esc_html( __( 'Menu In Post Tools', 'menu-in-post' ) ); ?></h1>
		<?php
		$str   = '';
		$title = esc_html( __( 'Note:', 'menu-in-post' ) );
		switch ( $options['miploadjs'] ) {
			case 'never':
				$str = esc_html(
					__(
						'Dropdown-style menus will not work with your current setting of "never" in Settings > Menu In Post, Load JavaScript.',
						'menu-in-post'
					)
				);
				break;
			case 'onlypages':
				$str = esc_html(
					__(
						'Dropdown-style menus will not work with your current setting of "only on posts/pages" in Settings > Menu In Post > Menu In Post Options, Load JavaScript unless you have set the correct page ID(s) in "Only on Posts/Pages".',
						'menu-in-post'
					)
				);
				break;
		}
		if ( '' !== $str ) {
			/*
				Use the wp_admin_notice() function, added in WP 6.4, if possible.

				Fall back to HTML output if < WP 6.4.

				You can remove the conditional for WP version once there has
				been time for everyone to upgrade to >= WP 6.4.
			*/
			if ( is_wp_version_compatible( '6.4' ) ) {
				wp_admin_notice(
					sprintf(
						/* translators: %1$s = already‑translated title, %2$s = already‑translated description */
						__( '<strong>%1$s</strong> %2$s', 'menu-in-post' ),
						$title,
						$str
					),
					array(
						'type'        => 'warning',
						'dismissible' => false,
					)
				);
			} else {
				?>
				<div class="notice notice-warning">
					<p>
						<strong><?php echo esc_html( $title ); ?></strong> <?php echo esc_html( $str ); ?>
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
						'Use the form below to create shortcodes to display menus in posts and pages. Paste the shortcodes you create in a Shortcode Block to display them.',
						'menu-in-post'
					)
				);
			?>
		</p>
		<h2><?php echo esc_html( __( 'Shortcode Builder', 'menu-in-post' ) ); ?></h2>
	<?php
		$menus = wp_get_nav_menus();
	if ( is_array( $menus ) && count( $menus ) > 0 ) {
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
						echo esc_html( __( 'Select Menu', 'menu-in-post' ) );
					?>
					:
					</label>
					<select name="mip_menu" id="mip_menu">
					<?php
					foreach ( $menus as $menu ) {
						echo '<option value="' . esc_html( $menu->term_id ) . '">' .
							esc_html( $menu->name ) . '</option>';
					}
					?>
					</select>
				</div>
				<div class="inputrow">
					<label for="mip_container">
					<?php
						echo esc_html(
							__( 'Include Container', 'menu-in-post' )
						);
					?>
					:
					</label>
					<select name="mip_container" id="mip_container">
						<option value="1">
						<?php echo esc_html( __( 'Yes', 'menu-in-post' ) ); ?>
						</option>
						<option value="0">
						<?php echo esc_html( __( 'No', 'menu-in-post' ) ); ?>
						</option>
					</select>
				</div>
				<div class="inputrow">
					<label for="mip_container_id">
						<?php
						echo esc_html( __( 'Container ID', 'menu-in-post' ) );
						?>
						:
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
							__( 'Container Class(es)', 'menu-in-post' )
						);
						?>
						:
					</label>
					<input 
						type="text" 
						name="mip_container_class" 
						id="mip_container_class"
					>
				</div>
				<div class="inputrow">
					<label for="mip_menu_id">
						<?php echo esc_html( __( 'Menu ID', 'menu-in-post' ) ); ?>:
					</label>
					<input type="text" name="mip_menu_id" id="mip_menu_id">
				</div>
				<div class="inputrow">
					<label for="mip_menu_class">
						<?php
							echo esc_html( __( 'Menu Class(es)', 'menu-in-post' ) );
						?>
						:
					</label>
					<input type="text" name="mip_menu_class" id="mip_menu_class">
				</div>
				<div class="inputrow">
					<label for="mip_depth">
						<?php echo esc_html( __( 'Depth', 'menu-in-post' ) ); ?>:
					</label>
					<select name="mip_depth" id="mip_depth">
						<?php
						$depth_options = array(
							0 => esc_html( __( 'All Levels', 'menu-in-post' ) ),
							1 => esc_html( __( '1 Level', 'menu-in-post' ) ),
							2 => esc_html( __( '2 Levels', 'menu-in-post' ) ),
							3 => esc_html( __( '3 Levels', 'menu-in-post' ) ),
							4 => esc_html( __( '4 Levels', 'menu-in-post' ) ),
							5 => esc_html( __( '5 Levels', 'menu-in-post' ) ),
						);
						foreach ( $depth_options as $value => $text ) {
							if ( 0 === $value ) {
								echo '<option value="' . esc_html( $value ) .
									'" selected="selected">' . esc_html( $text ) .
									'</option>';
							} else {
								echo '<option value="' . esc_html( $value ) . '">' .
									esc_html( $text ) . '</option>';
							}
						}
						?>
					</select>
				</div>
				<div class="inputrow">
					<label for="mip_style">
						<?php echo esc_html( __( 'Style', 'menu-in-post' ) ); ?>:
					</label>
					<select name="mip_style" id="mip_style">
						<?php
						$style_options = array(
							'list'     => esc_html(
								__( 'List of Links', 'menu-in-post' )
							),
							'dropdown' => esc_html( __( 'Dropdown', 'menu-in-post' ) ),
						);
						foreach ( $style_options as $value => $text ) {
							if ( 'list' === $value ) {
								echo '<option value="' . esc_html( $value ) .
									'" selected="selected">' . esc_html( $text ) .
									'</option>';
							} else {
								echo '<option value="' . esc_html( $value ) . '">' .
									esc_html( $text ) . '</option>';
							}
						}
						?>
					</select>
				</div>
				<div class="inputrow">
					<label for="mip_placeholder_text">
						<?php
							echo esc_html(
								__( 'Placeholder Text', 'menu-in-post' )
							);
						?>
						:
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
							echo esc_html( __( 'Append to URL', 'menu-in-post' ) );
						?>
						:
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
				value="<?php echo esc_attr( __( 'Build the Shortcode', 'menu-in-post' ) ); ?>"
			>
		</form>
		<div>
			<label for="mip_shortcode_builder_output">
				<?php echo esc_html( __( 'Shortcode', 'menu-in-post' ) ); ?>:
			</label>
			<div class="mip_shortcode_output_hightlight">
				<input 
					type="text" 
					name="mip_shortcode_builder_output" 
					id="mip_shortcode_builder_output" 
					value="<?php echo esc_html( $shortcode ); ?>" 
					readonly
				>
			</div>
			<div>
				<button 
					type="button" 
					id="mip_shortcode_output_copy_button"
				>
				<?php echo esc_html( __( 'Copy Shortcode', 'menu-in-post' ) ); ?>
				</button>&nbsp;
				<span id="mip_shortcode_copy_success">
					<?php echo esc_html( __( 'Copied...', 'menu-in-post' ) ); ?>
				</span>
			</div>
		</div>
	</div>
		<?php
	} elseif ( is_wp_version_compatible( '6.4' ) ) {
		/*
			Use the wp_admin_notice() function, added in WP 6.4, if possible.

			Fall back to HTML output if < WP 6.4.

			You can remove the conditional for WP version once there has
			been time for everyone to upgrade to >= WP 6.4.
		*/
		wp_admin_notice(
			esc_html__(
				"You must create one or more menus (Appearance > Menus) prior to using Menu In Post's Shortcode Builder.",
				'menu-in-post'
			),
			array(
				'type'        => 'warning',
				'dismissible' => true,
			)
		);
	} else {
		?>
		<div class="notice notice-warning is-dismissible">
			<p><?php echo esc_html( $str ); ?></p>
		</div>
		<?php
	}
}

/**
 * Enqueue the scripts and stylesheet(s) required for Menu In Post Admin pages.
 * Option: $live can be set to false during development in order to load
 * unminified versions of CSS and JS in Admin.
 *
 * @param string $hook Hook associated with the Tools page.
 *
 * @return void
 */
function menuinpost_enqueue_admin_scripts( $hook ) {
	if ( 'tools_page_menu-in-post' !== $hook
		&& 'settings_page_menu-in-post-options' !== $hook
	) {
		return;
	}
	$options = get_option(
		MIP_OPTION_NAME,
		array(
			'miploadjs'     => 'always',
			'miponlypages'  => '',
			'mipminimizejs' => 'yes',
		)
	);
	if ( 'yes' === $options['mipminimizejs'] ) {
		$min = '-min';
	} else {
		$min = '';
	}
	wp_enqueue_style(
		'menu_in_post_admin_style',
		plugins_url( 'css/style' . $min . '.css', __FILE__ ),
		array(),
		MENU_IN_POST_VERSION,
		'all'
	);
	wp_enqueue_script(
		'menu_in_post_admin_script',
		plugins_url( 'js/main' . $min . '.js', __FILE__ ),
		array( 'jquery' ),
		MENU_IN_POST_VERSION,
		false
	);
}
add_action( 'admin_enqueue_scripts', 'menuinpost_enqueue_admin_scripts' );

/**
 * Optionally, add back the Appearance > Menus menu subitem so that users
 * of block themes can add/edit classic menus for use in Menu In Post by
 * registering the existing Menus menu that was removed in WP 5.9+ for
 * block-enabled themes.
 *
 * @return void
 */
function menuinpost_get_back_appearance_menus() {
	$options = get_option(
		MIP_OPTION_NAME,
		array( 'mipshowappearancemenus' => 'no' )
	);
	if ( array_key_exists( 'mipshowappearancemenus', $options ) ) {
		if ( 'yes' === $options['mipshowappearancemenus'] && wp_is_block_theme() ) {
			register_nav_menus(
				array(
					'primary' => esc_html__(
						'Primary Menu',
						'menu-in-post'
					),
				)
			);
		}
	}
}
add_action( 'init', 'menuinpost_get_back_appearance_menus' );
?>
