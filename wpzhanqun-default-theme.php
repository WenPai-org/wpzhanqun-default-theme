<?php
/*
Plugin Name: WPZhanqun Default Theme
Plugin URI: https://wpzhanqun.com/plugins/default-theme
Description: Allows you to easily select a new default theme for new site signups
Author: WPZhanqun
Text Domain: wpzhanqun-default-theme
Version: 1.0.0
Author URI: https://wpzhanqun.com
Network: true
*/

/*
Copyright 2012-2021 WenPai (http://wenpai.org)
Developer: WenPai

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

//force multisite
if ( ! is_multisite() ) {
	exit( __( 'WPZhanqun Default Theme is only compatible with Multisite installs.', 'wpzhanqun-default-theme' ) );
}

//------------------------------------------------------------------------//
//---Hook-----------------------------------------------------------------//
//------------------------------------------------------------------------//

add_action( 'plugins_loaded', 'default_theme_localization' );
add_action( 'wpmu_options', 'default_theme_site_admin_options' );
add_action( 'update_wpmu_options', 'default_theme_site_admin_options_process', 1 );
add_action( 'wpmu_new_blog', 'default_theme_switch_theme', 1, 1 );

//------------------------------------------------------------------------//
//---Functions------------------------------------------------------------//
//------------------------------------------------------------------------//

function default_theme_localization() {
	// Load up the localization file if we're using WordPress in a different language
	// Place it in this plugin's "languages" folder and name it "wpzhanqun-default-theme-[value in wp-config].mo"
	load_plugin_textdomain( 'wpzhanqun-default-theme', false, '/wpzhanqun-default-theme/languages' );
}

function default_theme_switch_theme( $blog_ID ) {

	$default_theme = get_site_option( 'default_theme' );
	$themes        = wp_get_themes();

	//we have to go through all this to handle child themes, otherwise it will throw errors
	foreach ( (array) $themes as $key => $theme ) {
		$stylesheet = esc_html( $theme['Stylesheet'] );
		$template   = esc_html( $theme['Template'] );
		if ( $default_theme == $stylesheet || $default_theme == $template ) {
			$new_stylesheet = $stylesheet;
			$new_template   = $template;
		}
	}

	//activate it
	switch_to_blog( $blog_ID );
	switch_theme( $new_template, $new_stylesheet );
	restore_current_blog();
}

function default_theme_site_admin_options_process() {
	update_site_option( 'default_theme', $_POST['default_theme'] );
}

//------------------------------------------------------------------------//
//---Output Functions-----------------------------------------------------//
//------------------------------------------------------------------------//

function default_theme_site_admin_options() {

	$themes = wp_get_themes();

	$default_theme = get_site_option( 'default_theme' );
	if ( empty( $default_theme ) ) {
		$default_theme = 'default';
	}
	?>
	<h3><?php _e( 'Theme Settings', 'wpzhanqun-default-theme' ) ?></h3>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><?php _e( 'Default Theme', 'wpzhanqun-default-theme' ) ?></th>
			<td><select name="default_theme">
					<?php
					foreach ( $themes as $key => $theme ) {
						$theme_key = esc_html( $theme['Stylesheet'] );
						echo '<option value="' . $theme_key . '"' . ( $theme_key == $default_theme ? ' selected' : '' ) . '>' . $key . '</option>' . "\n";
					}
					?>
				</select>
				<br/><?php _e( 'The selected default theme applies to the new sites.', 'wpzhanqun-default-theme' ); ?></td>
		</tr>
	</table>
	<?php
}
