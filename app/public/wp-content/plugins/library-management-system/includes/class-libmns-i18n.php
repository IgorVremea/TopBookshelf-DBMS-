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

class LIBMNS_i18n_FREE {

	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			defined( 'LIBMNS_TEXT_DOMAIN' ) ? LIBMNS_TEXT_DOMAIN : 'library-management-system',
			false,
			dirname( LIBMNS_PLUGIN_BASENAME ) . '/languages/'
		);

	}
}
