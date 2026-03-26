<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/includes
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class LIBMNS_Sanitize_FREE
 */
class LIBMNS_Sanitize_FREE {

	/**
	 * Sanitize single-line text for DB storage (names, titles, etc.).
	 * Safe for UTF-8 and all scripts (Latin, Arabic, Devanagari, etc.).
	 *
	 * @param string $input Raw input (e.g. from $_REQUEST).
	 * @return string Sanitized string, safe for VARCHAR/TEXT columns.
	 */
	public static function multilingual_text( $input ) {
		if ( ! is_string( $input ) ) {
			return '';
		}
		$input = wp_unslash( $input );
		$input = wp_check_invalid_utf8( $input, true );
		$input = wp_strip_all_tags( $input );
		$input = trim( $input );
		$input = str_replace( "\0", '', $input );
		return $input;
	}

	/**
	 * Sanitize multi-line text for DB storage (description, address, etc.).
	 * Preserves newlines; safe for UTF-8 and all languages.
	 *
	 * @param string $input Raw input (e.g. from $_REQUEST).
	 * @return string Sanitized string, safe for TEXT columns.
	 */
	public static function multilingual_textarea( $input ) {
		if ( ! is_string( $input ) ) {
			return '';
		}
		$input = wp_unslash( $input );
		return sanitize_textarea_field( $input );
	}
}
