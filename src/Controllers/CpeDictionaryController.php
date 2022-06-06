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

use Prewk\XmlStringStreamer;

/**
 * Class CpeDictionaryController
 */
class CpeDictionaryController {
	/**
	 * Update
	 */
	public static function update() {
		$downloaded_dictionary_archive = download_url('https://nvd.nist.gov/feeds/xml/cpe/dictionary/official-cpe-dictionary_v2.3.xml.gz');

		// move and rename the downloaded file
		$dictionary_archive = WP_CONTENT_DIR.'/uploads/official-cpe-dictionary_v2.3.xml.gz';
		$dictionary = WP_CONTENT_DIR.'/uploads/official-cpe-dictionary_v2.3.xml';
		rename($downloaded_dictionary_archive, $dictionary_archive);

		// decompress from gz
		static::gunzip($dictionary_archive, $dictionary);

		// populate db
		static::populate_dict_db($dictionary);

		return $dictionary;
	}

	private static function gunzip($file_name, $out_file_name){
		// Raising this value may increase performance
		$buffer_size = 4096; // read 4kb at a time
		// Open our files (in binary mode)
		$file = gzopen($file_name, 'rb');
		$out_file = fopen($out_file_name, 'wb');
		// Keep repeating until the end of the input file
		while(!gzeof($file)) {
			// Read buffer-size bytes
			// Both fwrite and gzread and binary-safe
			fwrite($out_file, gzread($file, $buffer_size));
		}
		// Files are done, close files
		fclose($out_file);
		gzclose($file);
	}

	private static function populate_dict_db($dictionary) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'wpbom_cpe_dict';

		$wpdb->query("TRUNCATE TABLE $table_name");

		$streamer = XmlStringStreamer::createStringWalkerParser($dictionary);

		while ($node = $streamer->getNode()) {
			$simpleXmlNode = simplexml_load_string($node);
		}
	}
}
