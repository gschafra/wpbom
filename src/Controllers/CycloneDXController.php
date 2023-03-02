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
 * CycloneDXController
 */
class CycloneDXController {
	/**
	 * CycloneDX v1.3 JSON Reference
	 *
	 * @see https://cyclonedx.org/docs/1.3/json/
	 */
	public static function bom() {
		$bom = array(
			'bomFormat'   => 'CycloneDX',
			'specVersion' => '1.3',
			'version'     => 1,
			'metadata'    => array(
				'tools'     => array(
					array(
						'vendor'  => 'Sepbit',
						'name'    => 'WpBom',
						'version' => SEPBIT_WPBOM_VER,
					),
				),
				'component' => array(
					'type'     => 'application',
					'bom-ref'  => 'pkg:deb/debian/wordpress@' . get_bloginfo( 'version' ),
					'name'     => 'wordpress',
					'version'  => get_bloginfo( 'version' ),
					'purl'     => 'pkg:deb/debian/wordpress@' . get_bloginfo( 'version' ),
					'cpe'      => 'cpe:2.3:a:wordpress:wordpress:' . get_bloginfo( 'version' ) . ':-:*:*:*:*:*:*',
					'licenses' => array(
						array(
							'license' => array(
								'id' => 'GPL-2.0-or-later',
							),
						),
					),
				),
			),
		);

		/*
		 * Plugins
		 */
		$key = 0;
		foreach ( get_plugins() as $plugin ) {
			if ( empty( $plugin['TextDomain'] ) || empty( $plugin['Version'] ) ) {
				continue;
			}

			$bom['components'][ $key ]['type']    = 'application';
			$bom['components'][ $key ]['name']    = $plugin['TextDomain'];
			$bom['components'][ $key ]['version'] = $plugin['Version'];

			if ( ! empty( $plugin['Author'] ) ) {
				$bom['components'][ $key ]['author'] = $plugin['Author'];
			}

			if ( ! empty( $plugin['Description'] ) ) {
				$bom['components'][ $key ]['description'] = $plugin['Description'];
			}

			if ( ! empty( $plugin['PluginURI'] ) ) {
				$bom['components'][ $key ]['externalReferences'][] = array(
					'url'     => $plugin['PluginURI'],
					'comment' => 'PluginURI',
					'type'    => 'website',
				);
			}

			if ( ! empty( $plugin['AuthorURI'] ) ) {
				$bom['components'][ $key ]['externalReferences'][] = array(
					'url'     => $plugin['AuthorURI'],
					'comment' => 'AuthorURI',
					'type'    => 'website',
				);
			}

			if ( ! empty( $plugin['UpdateURI'] ) ) {
				$bom['components'][ $key ]['externalReferences'][] = array(
					'url'     => $plugin['UpdateURI'],
					'comment' => 'UpdateURI',
					'type'    => 'website',
				);
			}

			// TODO #1: Guess author and use it instead of "plugins"
			$bom['components'][ $key ]['bom-ref'] = 'pkg:wordpress/plugins/' . $plugin['TextDomain'] . '@' . $plugin['Version'];
			// WordPress PURL not supported yet, see https://github.com/package-url/purl-spec/blob/master/PURL-TYPES.rst#other-candidate-types-to-define
			// $bom['components'][ $key ]['purl']    = 'pkg:wordpress/plugins/' . $plugin['TextDomain'] . '@' . $plugin['Version'];
			// TODO #1: Guess CPE
			$bom['components'][ $key ]['cpe']    = 'cpe:2.3:a:rocklobster:contact_form_7:' . $plugin['Version'] . ':*:*:*:*:wordpress:*:*';

			$key++;
		}

		/*
		 * Themes
		 */
		foreach ( wp_get_themes() as $theme ) {
			if ( empty( $theme->get( 'TextDomain' ) ) || empty( $theme->get( 'Version' ) ) ) {
				continue;
			}

			$bom['components'][ $key ]['type']    = 'application';
			$bom['components'][ $key ]['bom-ref'] = 'pkg:wordpress/themes/' . $theme->get( 'TextDomain' ) . '@' . $theme->get( 'Version' );
			$bom['components'][ $key ]['name']    = $theme->get( 'TextDomain' );
			$bom['components'][ $key ]['version'] = $theme->get( 'Version' );
			// TODO #1: Guess PURL
			$bom['components'][ $key ]['purl']    = 'pkg:wordpress/themes/' . $theme->get( 'TextDomain' ) . '@' . $theme->get( 'Version' );

			if ( ! empty( $theme->get( 'Author' ) ) ) {
				$bom['components'][ $key ]['author'] = $theme->get( 'Author' );
			}

			if ( ! empty( $theme->get( 'Description' ) ) ) {
				$bom['components'][ $key ]['description'] = $theme->get( 'Description' );
			}

			if ( ! empty( $theme->get( 'PluginURI' ) ) ) {
				$bom['components'][ $key ]['externalReferences'][] = array(
					'url'     => $theme->get( 'PluginURI' ),
					'comment' => 'PluginURI',
					'type'    => 'website',
				);
			}

			if ( ! empty( $theme->get( 'AuthorURI' ) ) ) {
				$bom['components'][ $key ]['externalReferences'][] = array(
					'url'     => $theme->get( 'AuthorURI' ),
					'comment' => 'AuthorURI',
					'type'    => 'website',
				);
			}

			if ( ! empty( $theme->get( 'UpdateURI' ) ) ) {
				$bom['components'][ $key ]['externalReferences'][] = array(
					'url'     => $theme->get( 'UpdateURI' ),
					'comment' => 'UpdateURI',
					'type'    => 'website',
				);
			}

			$key++;
		}

		return apply_filters( 'wpbom_bom', $bom );
	}

	/**
	 * Download BOM file
	 */
	public static function json() {
		if ( isset( $_GET['_wpnonce'] ) && ! empty( $_GET['_wpnonce'] ) ) {
			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'wpbom' ) ) {
				return;
			}
		}

		if ( isset( $_GET['download'] ) && ! empty( $_GET['download'] ) ) {
			header( 'Content-Disposition: attachment; filename="bom.json"' );
			wp_send_json( self::bom() );
		}
	}

	/**
	 * @param string $text_domain
	 * @param string|null $author
	 *
	 * @return [string, string]|null
	 */
	public static function guess_cpe_vendor_and_product( $text_domain, $author ) {
		// TODO #1: Implement matching text domain and author agains CPE dict's vendor and product
		// Using various strategies like natural language matching
		// https://dev.mysql.com/doc/refman/8.0/en/fulltext-natural-language.html
		// or SOUNDS LIKE
		// or exact matching with non-digit/word character substitution (- => _)
		// i.e. REGEXP_REPLACE(product, '[^a-zA-Z0-9]', '') = REGEXP_REPLACE('contact-form.7', '[^a-zA-Z0-9]', '')
		// to find the best single match

	}

}
