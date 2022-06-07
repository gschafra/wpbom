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
	 * Class PluginController
	 */
	class PluginController {
		public static $wpbom_db_version = '1.0';

		public static function plugin_activation()
		{
			static::install();

			add_option( 'wpbom_db_version', static::$wpbom_db_version );
		}

		public static function plugin_deactivation()
		{

		}

		private static function install()
		{
			global $wpdb;

			$table_name = $wpdb->prefix . 'wpbom_cpe_dict';

			$charset_collate = $wpdb->get_charset_collate();

			$sqlTable = "CREATE TABLE $table_name (
				vendor varchar(250) DEFAULT '' NOT NULL,
				product varchar(250) DEFAULT '' NOT NULL
			) $charset_collate;";

			$sqlIndex = "CREATE UNIQUE INDEX ${table_name}_vendor_product_uindex ON $table_name (vendor, product);";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sqlTable );
			$wpdb->query( $sqlIndex );
		}
	}
