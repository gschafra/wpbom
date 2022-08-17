<?php
/**
 * Wpbom - WordPress integration with OWASP CycloneDX and Dependency Track
 * Copyright (C) 2021-2022  Vitor Guia
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @package Wpbom
 */

namespace Sepbit\WpBom\Controllers;

/**
 * OptionsPageController
 */
class OptionsPageController {
	/**
	 * Options page
	 */
	public static function options_page() {
		$cmb = new_cmb2_box(
			array(
				'id'           => 'wpbom_options_page',
				'title'        => __( 'WpBom', 'wpbom' ),
				'object_types' => array( 'options-page' ),
				'option_key'   => 'wpbom',
                                'icon_url'     => 'dashicons-admin-plugins',
			)
		);
		$cmb->add_field(
			array(
				'name' => __( 'Server', 'wpbom' ),
				'id'   => SEPBIT_WPBOM_PRE . '_server',
				'type' => 'text',
			)
		);
		$cmb->add_field(
			array(
				'name' => __( 'API key', 'wpbom' ),
				'id'   => SEPBIT_WPBOM_PRE . '_api_key',
				'type' => 'text',
			)
		);
		$cmb->add_field(
			array(
				'name' => __( 'Project', 'wpbom' ),
				'id'   => SEPBIT_WPBOM_PRE . '_project',
				'type' => 'text',
			)
		);
		$cmb->add_field(
			array(
				'name' => __( 'Disable', 'wpbom' ),
				'id'   => SEPBIT_WPBOM_PRE . '_disable',
				'type' => 'checkbox',
				'desc' => 'I want to pause auto update',
			)
		);

		$cmb->add_field(
			array(
				'name'          => __( 'Custom buttons', 'wpbom' ),
				'id'            => SEPBIT_WPBOM_PRE . '_manual',
				'type'          => 'text',
				'desc'          => 'Manual actions',
				'render_row_cb' => array( __CLASS__, 'custom_buttons' ),
			)
		);
	}

	/**
	 * Custom buttons
	 */
	public static function custom_buttons( $field_args, $field ) {
		$classes     = $field->row_classes();
		$description = $field->args( 'description' );

		$url      = menu_page_url( 'wpbom', false );
		$url      = wp_nonce_url( $url, 'wpbom' );
		$update   = add_query_arg( 'update', 'true', $url );
		$download = add_query_arg( 'download', 'true', $url );
		$cpeUpdate = add_query_arg( 'cpe', 'true', $url );

		// Implement ajax call according to
		// https://wpmudev.com/blog/using-ajax-with-wordpress/
		// https://developer.wordpress.org/reference/hooks/wp_ajax_action/
		// https://developer.wordpress.org/plugins/javascript/ajax/
		$update_cpe_dict_nonce = wp_create_nonce('update_cpe_dict_nonce');
		?>
		<div class="custom-field-row <?php echo esc_attr( $classes ); ?>">
			<div class="button-row">
				<a class="button button-secondary" href="<?php esc_html_e( $update ); ?>">Manual update</a>
				<a class="button button-secondary" href="<?php esc_html_e( $download ); ?>">Download BOM file</a>
				<button class="button button-secondary button-with-loader wpbom-update-cpe-update" title="For PURL component lookup" data-nonce="<?php esc_html_e( $update_cpe_dict_nonce ); ?>"><div class="custom-loader"></div>Update CPE dictionary</button>
			</div>
			<p class="description"><?php echo esc_html( $description ); ?></p>
		</div>
		<?php
		if ( isset( $_GET['_wpnonce'] ) && ! empty( $_GET['_wpnonce'] ) ) {
			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'wpbom' ) ) {
				return;
			}
		}

		if ( isset( $_GET['update'] ) && ! empty( $_GET['update'] ) ) {
			echo '<pre>';
			print_r( DependencyTrackController::update() );
			echo '</pre>';
		}

		if ( isset( $_GET['cpe'] ) && ! empty( $_GET['cpe'] ) ) {
			echo '<pre>';
			print_r( CpeDictionaryController::update() );
			echo '</pre>';
		}

		wp_register_script( 'wpbom.js', SEPBIT_WPBOM_URL . 'src/Assets/js/wpbom.js', ['jquery'], SEPBIT_WPBOM_VER );
		wp_enqueue_script( 'wpbom.js' );

		wp_register_style( 'wpbom.css', SEPBIT_WPBOM_URL . 'src/Assets/js/wpbom.css', [], SEPBIT_WPBOM_VER );
		wp_enqueue_style( 'wpbom.css' );
	}

	public static function update_cpe_dict($data)
	{

	}

}
