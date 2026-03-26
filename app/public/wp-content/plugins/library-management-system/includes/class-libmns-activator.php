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

require_once plugin_dir_path( __FILE__ ) . 'class-libmns-db-backup-helper.php';
require_once plugin_dir_path( __FILE__ ) . 'class-libmns-table-helper.php';

class LIBMNS_Activator_FREE {

	public function activate() {
		// Plugin Tables
		$this->owt7_library_generate_plugin_tables();
		$this->owt7_library_migrate_legacy_tables();
		$this->owt7_library_maybe_generate_book_copies_from_books();
		$this->owt7_library_cleanup_migrated_legacy_tables();
		// Insert Table Data
		$this->owt7_library_insert_default_data();
		$this->owt7_library_normalize_free_borrow_days();
		// Plugin Options
		$this->owt7_library_options();
		// Plugin pages
		$this->owt7_library_shortcodes();
		// Flush rewrite rules so library friendly URLs work
		flush_rewrite_rules();
	}

	public static function maybe_upgrade_plugin() {
		$activator      = new self();
		$stored_version = get_option( 'owt7_library_version', '' );

		if ( ! $activator->owt7_library_needs_upgrade( $stored_version ) ) {
			return;
		}

		$activator->activate();
	}

	private function owt7_library_needs_upgrade( $stored_version ) {
		$current_version = defined( 'LIBMNS_VERSION' ) ? (string) LIBMNS_VERSION : '';

		if ( $stored_version === '' ) {
			return $this->owt7_library_has_legacy_34_tables() || ! $this->owt7_library_all_required_tables_exist();
		}

		if ( $current_version !== '' && version_compare( (string) $stored_version, $current_version, '<' ) ) {
			return true;
		}

		return $this->owt7_library_has_pending_legacy_migration();
	}

	private function owt7_library_generate_plugin_tables(){

		global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
		if ( empty( $charset_collate ) ) {
            $charset_collate = 'DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci';
        }

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // DB: "Users" Table
        $tbl_users = LIBMNS_Table_Helper_FREE::get_table_name( 'users' );
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $tbl_users)) != $tbl_users) {
            $sqlUserTable = 'CREATE TABLE `' . $tbl_users . '` (
                                    `id` int NOT NULL AUTO_INCREMENT,
                                    `register_from` enum("web","admin") DEFAULT "admin",
                                    `wp_user` int NOT NULL DEFAULT "0",
                                    `wp_user_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
                                    `u_id` VARCHAR(250) DEFAULT NULL,
                                    `name` VARCHAR(250) DEFAULT NULL,
                                    `email` VARCHAR(250) DEFAULT NULL,
                                    `gender` enum("male","female","other") DEFAULT NULL,
                                    `branch_id` int(5) DEFAULT NULL,
                                    `phone_no` VARCHAR(250) DEFAULT NULL,
                                    `profile_image` VARCHAR(250) DEFAULT NULL,
                                    `address_info` text,
                                    `status` int NOT NULL DEFAULT "1",
                                    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                    PRIMARY KEY (`id`)
                                    ) '.$charset_collate.';';
            dbDelta($sqlUserTable);
        } else {
            $this->owt7_library_add_missing_columns($tbl_users, $this->owt7_library_premium_columns_users());
        }

		// DB: "Books" Table
        $tbl_books = LIBMNS_Table_Helper_FREE::get_table_name( 'books' );
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $tbl_books)) != $tbl_books) {
            $sqlBookTable = 'CREATE TABLE `' . $tbl_books . '` (
									id INT NOT NULL AUTO_INCREMENT,
									book_id VARCHAR(250) DEFAULT NULL,
									bookcase_id INT(5) DEFAULT NULL,
									bookcase_section_id INT(5) DEFAULT NULL,
									category_id INT(5) DEFAULT NULL,
									name VARCHAR(250) DEFAULT NULL,
									author_name TEXT DEFAULT NULL,
									publication_name TEXT DEFAULT NULL,
									publication_year VARCHAR(250) DEFAULT NULL,
									publication_location VARCHAR(250) DEFAULT NULL,
									amount VARCHAR(250) DEFAULT NULL,
									cover_image VARCHAR(250) DEFAULT NULL,
									isbn VARCHAR(250) DEFAULT NULL,
									book_url VARCHAR(250) DEFAULT NULL,
									stock_quantity INT(5) DEFAULT NULL,
									book_language VARCHAR(250) DEFAULT NULL,
									book_pages INT(5) DEFAULT NULL,
									description TEXT,
                                    is_woocom_product INT NOT NULL DEFAULT "0",
                                    is_woocom_stock INT NOT NULL DEFAULT "0",
                                    woocom_regular_price VARCHAR(250) DEFAULT NULL,
                                    woocom_sale_price VARCHAR(250) DEFAULT NULL,
                                    woocom_book_preview_pdf_link TEXT DEFAULT NULL,
                                    woocom_book_pdf_link TEXT DEFAULT NULL,
									status INT NOT NULL DEFAULT "1",
									created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                    PRIMARY KEY (`id`)
                              ) '.$charset_collate.';';
            dbDelta($sqlBookTable);
        } else {
            $this->owt7_library_add_missing_columns($tbl_books, $this->owt7_library_premium_columns_books());
            $this->owt7_library_upgrade_books_author_publication_columns($tbl_books);
        }

        // DB: "Bookcases" Table
        $tbl_bookcase = LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' );
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $tbl_bookcase)) != $tbl_bookcase) {
            $sqlBookcaseTable = 'CREATE TABLE `' . $tbl_bookcase . '` (
                                    `id` int NOT NULL AUTO_INCREMENT,
                                    `name` VARCHAR(250) DEFAULT NULL,
                                    `status` enum("1","0") NOT NULL DEFAULT "1",
                                    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                    PRIMARY KEY (`id`)
                              ) '.$charset_collate.';';
            dbDelta($sqlBookcaseTable);
        }

        // DB: "Bookcase Sections" Table
        $tbl_bookcase_sections = LIBMNS_Table_Helper_FREE::get_table_name( 'sections' );
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $tbl_bookcase_sections)) != $tbl_bookcase_sections) {
            $sqlBookcaseSectionTable = 'CREATE TABLE `' . $tbl_bookcase_sections . '` (
                                    `id` int NOT NULL AUTO_INCREMENT,
                                    `name` VARCHAR(250) DEFAULT NULL,
                                    `bookcase_id` int(5) DEFAULT NULL,
                                    `status` enum("1","0") NOT NULL DEFAULT "1",
                                    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                    PRIMARY KEY (`id`)
                              )  '.$charset_collate.';';
            dbDelta($sqlBookcaseSectionTable);
        }

        // DB: "Branches" Table
        $tbl_branch = LIBMNS_Table_Helper_FREE::get_table_name( 'branches' );
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $tbl_branch)) != $tbl_branch) {
            $sqlBranchTable = 'CREATE TABLE `' . $tbl_branch . '` (
                                    `id` int NOT NULL AUTO_INCREMENT,
                                    `name` VARCHAR(250) DEFAULT NULL,
                                    `status` enum("1","0") NOT NULL DEFAULT "1",
                                    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                    PRIMARY KEY (`id`)
                              ) '.$charset_collate.';';
            dbDelta($sqlBranchTable);
        }

        // DB: "Categories" Table
        $tbl_category = LIBMNS_Table_Helper_FREE::get_table_name( 'categories' );
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $tbl_category)) != $tbl_category) {
            $sqlCategoryTable = 'CREATE TABLE `' . $tbl_category . '` (
                                    `id` int NOT NULL AUTO_INCREMENT,
                                    `name` VARCHAR(250) DEFAULT NULL,
                                    `status` enum("1","0") NOT NULL DEFAULT "1",
                                    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                    PRIMARY KEY (`id`)
                              ) '.$charset_collate.';';
            dbDelta($sqlCategoryTable);
        }

        // DB: "Book Borrow" Table
        $tbl_book_borrow = LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' );
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $tbl_book_borrow)) != $tbl_book_borrow) {
            $sqlBookBorrowTable = 'CREATE TABLE `' . $tbl_book_borrow . '` (
                                    `id` int NOT NULL AUTO_INCREMENT,
                                    `borrow_id` VARCHAR(250) DEFAULT NULL,
                                    `category_id` int(5) DEFAULT NULL,
                                    `book_id` int(5) DEFAULT NULL,
                                    `branch_id` int(5) DEFAULT NULL,
                                    `wp_user` int NOT NULL DEFAULT "0",
                                    `u_id` int(5) DEFAULT NULL,
                                    `borrows_days` int(5) DEFAULT NULL,
                                    `return_date` VARCHAR(250) DEFAULT NULL,
                                    `is_self_checkout` int NOT NULL DEFAULT "0",
                                    `checkout_status` enum("1", "2", "3", "4", "5") NOT NULL DEFAULT "5" COMMENT "1 - approved_by_admin, 2 - self_approved, 3 - checkout_pending, 4 - checkout_rejected, 5 - no_status",
                                    `status` enum("1","0") NOT NULL DEFAULT "1",
                                    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                    PRIMARY KEY (`id`)
                              ) '.$charset_collate.';';
            dbDelta($sqlBookBorrowTable);
        } else {
            $this->owt7_library_add_missing_columns($tbl_book_borrow, $this->owt7_library_premium_columns_book_borrow());
        }

        // DB: "Book Return" Table
        $tbl_book_return = LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' );
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $tbl_book_return)) != $tbl_book_return) {
            $sqlBookReturnTable = 'CREATE TABLE `' . $tbl_book_return . '` (
                                    `id` int NOT NULL AUTO_INCREMENT,
                                    `borrow_id` VARCHAR(250) DEFAULT NULL,
                                    `return_id` VARCHAR(50) DEFAULT NULL COMMENT "Display ID e.g. LMSBR + 7 digit timestamp",
                                    `category_id` int(5) DEFAULT NULL,
                                    `book_id` int(5) DEFAULT NULL,
                                    `branch_id` int(5) DEFAULT NULL,
                                    `wp_user` int NOT NULL DEFAULT "0",
                                    `u_id` int(5) DEFAULT NULL,
                                    `has_fine_status` enum("1","0") NOT NULL DEFAULT "0",
                                    `is_self_return` int NOT NULL DEFAULT "0",
                                    `return_status` enum("1", "2", "3", "4", "5") NOT NULL DEFAULT "5" COMMENT "1 - approved_by_admin, 2 - self_returned, 3 - return_pending, 4 - return_rejected, 5 - no_status",
                                    `return_condition` VARCHAR(50) DEFAULT NULL,
                                    `return_remark` TEXT DEFAULT NULL,
                                    `status` enum("1","0") NOT NULL DEFAULT "1",
                                    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                    PRIMARY KEY (`id`)
                              ) '.$charset_collate.';';
            dbDelta($sqlBookReturnTable);
        } else {
            $this->owt7_library_add_missing_columns($tbl_book_return, $this->owt7_library_premium_columns_book_return());
            $this->owt7_library_backfill_return_id( $tbl_book_return );
        }

        // DB: "Return Late Fine" Table
        $tbl_book_late_fine = LIBMNS_Table_Helper_FREE::get_table_name( 'book_late_fine' );
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $tbl_book_late_fine)) != $tbl_book_late_fine) {
            $sqlLateFineTable = 'CREATE TABLE `' . $tbl_book_late_fine . '` (
                                    `id` int NOT NULL AUTO_INCREMENT,
                                    `return_id` int(5) DEFAULT NULL,
                                    `book_id` int(5) DEFAULT NULL,
                                    `wp_user` int NOT NULL DEFAULT "0",
                                    `u_id` int(5) DEFAULT NULL,
                                    `extra_days` int(5) DEFAULT NULL,
                                    `fine_amount` int(5) DEFAULT NULL,
                                    `fine_type` VARCHAR(50) DEFAULT "late_return" COMMENT "late_return, damaged_book, lost_book, missing_pages",
                                    `has_paid` enum("1","2") NOT NULL DEFAULT "1" COMMENT "1 - Not Paid, 2 - Paid",
                                    `status` enum("1","0") NOT NULL DEFAULT "1",
                                    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                    PRIMARY KEY (`id`)
                              ) '.$charset_collate.';';
            dbDelta($sqlLateFineTable);
        } else {
            $this->owt7_library_add_missing_columns($tbl_book_late_fine, $this->owt7_library_premium_columns_book_late_fine());
        }

        $this->owt7_library_add_accession_number_after_book_id();

        // DB: "Borrow Book Days" Table
        $tbl_issue_days = LIBMNS_Table_Helper_FREE::get_table_name( 'issue_days' );
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $tbl_issue_days)) != $tbl_issue_days) {
            $sqlBorrowDaysTable = 'CREATE TABLE `' . $tbl_issue_days . '` (
                                    `id` int NOT NULL AUTO_INCREMENT,
                                    `days` int(5) DEFAULT NULL,
                                    `status` enum("1","0") NOT NULL DEFAULT "1",
                                    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                    PRIMARY KEY (`id`)
                              ) '.$charset_collate.';';
            dbDelta($sqlBorrowDaysTable);
        }

        // DB: "LMS SQL Data Backup" Table
        $tbl_data_backups = LIBMNS_Table_Helper_FREE::get_table_name( 'data_backups' );
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $tbl_data_backups)) != $tbl_data_backups) {
            $sqlDataBackupsTable = 'CREATE TABLE `' . $tbl_data_backups . '` (
                                    `id` int NOT NULL AUTO_INCREMENT,
                                    `file_name` VARCHAR(250) DEFAULT NULL,
                                    `file_size` VARCHAR(250) DEFAULT NULL,
                                    `file_flag` VARCHAR(250) DEFAULT NULL,
                                    `file_path` VARCHAR(250) DEFAULT NULL,
                                    `backup_type` enum("lms","deactivate") NOT NULL DEFAULT "lms",
                                    `lms_data_version` VARCHAR(250) DEFAULT NULL,
                                    `status` enum("1","0") NOT NULL DEFAULT "1",
                                    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                    PRIMARY KEY (`id`)
                              ) '.$charset_collate.';';
            dbDelta($sqlDataBackupsTable);
        }

        // DB: "Book Copies" Table
        $tbl_book_copies = LIBMNS_Table_Helper_FREE::get_table_name( 'data_books_copies' );
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $tbl_book_copies)) != $tbl_book_copies) {
            $sqlBookCopiesTable = 'CREATE TABLE `' . $tbl_book_copies . '` (
                                    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                    `book_id` VARCHAR(150) NOT NULL,
                                    `accession_number` VARCHAR(50) NOT NULL,
                                    `status` VARCHAR(50) NOT NULL DEFAULT "available",
                                    `shelf_location` VARCHAR(250) DEFAULT NULL,
                                    `notes` TEXT DEFAULT NULL,
                                    `is_barcode_exists` TINYINT(1) NOT NULL DEFAULT 0,
                                    `is_qrcode_exists` TINYINT(1) NOT NULL DEFAULT 0,
                                    `barcode_path` VARCHAR(500) DEFAULT NULL,
                                    `qrcode_path` VARCHAR(500) DEFAULT NULL,
                                    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                    PRIMARY KEY (`id`),
                                    UNIQUE KEY unique_accession_number (`accession_number`)
                              ) '.$charset_collate.';';
            dbDelta($sqlBookCopiesTable);
        } else {
            $this->owt7_library_add_missing_columns( $tbl_book_copies, $this->owt7_library_copies_barcode_qrcode_columns() );
        }

        $this->owt7_library_ensure_custom_labels_table( $charset_collate );
	}

	public function owt7_library_ensure_custom_labels_table( $charset_collate = '' ) {
		global $wpdb;
		if ( empty( $charset_collate ) ) {
			$charset_collate = $wpdb->get_charset_collate();
		}
		if ( empty( $charset_collate ) ) {
			$charset_collate = 'DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci';
		}
		$tbl_custom_labels = LIBMNS_Table_Helper_FREE::get_table_name( 'custom_labels' );
		if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $tbl_custom_labels ) ) != $tbl_custom_labels ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$sqlCustomLabelsTable = 'CREATE TABLE `' . $tbl_custom_labels . '` (
				`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				`text_key` VARCHAR(500) NOT NULL,
				`module` VARCHAR(100) NOT NULL,
				`text_en` TEXT DEFAULT NULL,
				`text_fr` TEXT DEFAULT NULL,
				`text_es` TEXT DEFAULT NULL,
				`text_it` TEXT DEFAULT NULL,
				`created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				`updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				PRIMARY KEY (`id`),
				UNIQUE KEY unique_text_key (`text_key`(191))
			) ' . $charset_collate . ';';
			dbDelta( $sqlCustomLabelsTable );
		}
	}

	private function owt7_library_legacy_table_map() {
		global $wpdb;

		return array(
			'branches'       => $wpdb->prefix . 'owt7_lib_branch',
			'users'          => $wpdb->prefix . 'owt7_lib_users',
			'bookcases'      => $wpdb->prefix . 'owt7_lib_bookcase',
			'sections'       => $wpdb->prefix . 'owt7_lib_bookcase_sections',
			'categories'     => $wpdb->prefix . 'owt7_lib_category',
			'books'          => $wpdb->prefix . 'owt7_lib_books',
			'book_borrow'    => $wpdb->prefix . 'owt7_lib_book_borrow',
			'book_return'    => $wpdb->prefix . 'owt7_lib_book_return',
			'book_late_fine' => $wpdb->prefix . 'owt7_lib_book_late_fine',
		);
	}

	private function owt7_library_all_required_tables_exist() {
		foreach ( LIBMNS_Table_Helper_FREE::get_all_table_names() as $table_name ) {
			if ( ! $this->owt7_library_table_exists( $table_name ) ) {
				return false;
			}
		}

		return true;
	}

	private function owt7_library_has_legacy_34_tables() {
		foreach ( $this->owt7_library_legacy_table_map() as $module => $legacy_table ) {
			$current_table = LIBMNS_Table_Helper_FREE::get_table_name( $module );
			if ( $legacy_table !== $current_table && $this->owt7_library_table_exists( $legacy_table ) ) {
				return true;
			}
		}

		return false;
	}

	private function owt7_library_has_pending_legacy_migration() {
		foreach ( $this->owt7_library_legacy_table_map() as $module => $legacy_table ) {
			$current_table = LIBMNS_Table_Helper_FREE::get_table_name( $module );
			if ( $legacy_table === $current_table || ! $this->owt7_library_table_exists( $legacy_table ) ) {
				continue;
			}

			if ( ! $this->owt7_library_table_exists( $current_table ) ) {
				return true;
			}

			if ( $this->owt7_library_get_row_count( $legacy_table ) > 0 && $this->owt7_library_get_row_count( $current_table ) === 0 ) {
				return true;
			}
		}

		return false;
	}

	private function owt7_library_table_exists( $table_name ) {
		global $wpdb;

		if ( ! is_string( $table_name ) || $table_name === '' ) {
			return false;
		}

		return $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) === $table_name;
	}

	private function owt7_library_get_table_columns( $table_name ) {
		global $wpdb;

		if ( ! $this->owt7_library_table_exists( $table_name ) ) {
			return array();
		}

		$table_name_escaped = str_replace( '`', '``', $table_name );
		$columns            = $wpdb->get_col( "SHOW COLUMNS FROM `{$table_name_escaped}`" );

		return is_array( $columns ) ? $columns : array();
	}

	private function owt7_library_get_row_count( $table_name ) {
		global $wpdb;

		if ( ! $this->owt7_library_table_exists( $table_name ) ) {
			return 0;
		}

		$table_name_escaped = str_replace( '`', '``', $table_name );

		return (int) $wpdb->get_var( "SELECT COUNT(*) FROM `{$table_name_escaped}`" );
	}

	private function owt7_library_copy_legacy_rows( $source_table, $target_table ) {
		global $wpdb;

		if (
			! $this->owt7_library_table_exists( $source_table ) ||
			! $this->owt7_library_table_exists( $target_table ) ||
			$source_table === $target_table ||
			$this->owt7_library_get_row_count( $source_table ) === 0 ||
			$this->owt7_library_get_row_count( $target_table ) > 0
		) {
			return;
		}

		$source_columns = $this->owt7_library_get_table_columns( $source_table );
		$target_columns = $this->owt7_library_get_table_columns( $target_table );
		$copy_columns   = array();

		foreach ( $target_columns as $column_name ) {
			if ( in_array( $column_name, $source_columns, true ) ) {
				$copy_columns[] = $column_name;
			}
		}

		if ( empty( $copy_columns ) ) {
			return;
		}

		$escaped_columns = array_map(
			static function( $column_name ) {
				return '`' . str_replace( '`', '``', $column_name ) . '`';
			},
			$copy_columns
		);

		$target_table_escaped = str_replace( '`', '``', $target_table );
		$source_table_escaped = str_replace( '`', '``', $source_table );
		$columns_sql          = implode( ', ', $escaped_columns );

		$wpdb->query(
			"INSERT INTO `{$target_table_escaped}` ({$columns_sql}) SELECT {$columns_sql} FROM `{$source_table_escaped}`"
		);
	}

	private function owt7_library_migrate_legacy_tables() {
		foreach ( $this->owt7_library_legacy_table_map() as $module => $legacy_table ) {
			$current_table = LIBMNS_Table_Helper_FREE::get_table_name( $module );
			$this->owt7_library_copy_legacy_rows( $legacy_table, $current_table );
		}

		$this->owt7_library_finalize_migrated_borrow_tables();
	}

	private function owt7_library_cleanup_migrated_legacy_tables() {
		$tables_to_drop = array();

		foreach ( $this->owt7_library_legacy_table_map() as $module => $legacy_table ) {
			$current_table = LIBMNS_Table_Helper_FREE::get_table_name( $module );

			if ( $legacy_table === $current_table || ! $this->owt7_library_table_exists( $legacy_table ) ) {
				continue;
			}

			if ( ! $this->owt7_library_is_legacy_table_fully_migrated( $legacy_table, $current_table ) ) {
				return;
			}

			$tables_to_drop[] = $legacy_table;
		}

		foreach ( $tables_to_drop as $legacy_table ) {
			$this->owt7_library_drop_table( $legacy_table );
		}
	}

	private function owt7_library_is_legacy_table_fully_migrated( $legacy_table, $current_table ) {
		global $wpdb;

		if ( ! $this->owt7_library_table_exists( $legacy_table ) ) {
			return true;
		}

		if ( ! $this->owt7_library_table_exists( $current_table ) ) {
			return false;
		}

		$legacy_count = $this->owt7_library_get_row_count( $legacy_table );
		if ( $legacy_count === 0 ) {
			return true;
		}

		$current_count = $this->owt7_library_get_row_count( $current_table );
		if ( $current_count < $legacy_count ) {
			return false;
		}

		$legacy_columns  = $this->owt7_library_get_table_columns( $legacy_table );
		$current_columns = $this->owt7_library_get_table_columns( $current_table );

		if ( in_array( 'id', $legacy_columns, true ) && in_array( 'id', $current_columns, true ) ) {
			$legacy_table_escaped  = str_replace( '`', '``', $legacy_table );
			$current_table_escaped = str_replace( '`', '``', $current_table );

			$missing_rows = (int) $wpdb->get_var(
				"SELECT COUNT(*)
				FROM `{$legacy_table_escaped}` AS legacy
				LEFT JOIN `{$current_table_escaped}` AS current_data ON current_data.id = legacy.id
				WHERE current_data.id IS NULL"
			);

			return $missing_rows === 0;
		}

		return $current_count >= $legacy_count;
	}

	private function owt7_library_drop_table( $table_name ) {
		global $wpdb;

		if ( ! $this->owt7_library_table_exists( $table_name ) ) {
			return;
		}

		$table_name_escaped = str_replace( '`', '``', $table_name );
		$wpdb->query( "DROP TABLE IF EXISTS `{$table_name_escaped}`" );
	}

	private function owt7_library_finalize_migrated_borrow_tables() {
		global $wpdb;

		$tbl_book_borrow = LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' );
		if ( $this->owt7_library_table_exists( $tbl_book_borrow ) ) {
			$tbl_book_borrow_escaped = str_replace( '`', '``', $tbl_book_borrow );
			$wpdb->query(
				$wpdb->prepare(
					"UPDATE `{$tbl_book_borrow_escaped}` SET checkout_status = %s WHERE borrow_id IS NOT NULL AND borrow_id != '' AND (checkout_status IS NULL OR checkout_status = '' OR checkout_status = %s)",
					(string) LIBMNS_CHECKOUT_APPROVED_BY_ADMIN,
					(string) LIBMNS_CHECKOUT_NO_STATUS
				)
			);
		}

		$tbl_book_return = LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' );
		if ( $this->owt7_library_table_exists( $tbl_book_return ) ) {
			$tbl_book_return_escaped = str_replace( '`', '``', $tbl_book_return );
			$wpdb->query(
				$wpdb->prepare(
					"UPDATE `{$tbl_book_return_escaped}` SET return_status = %s WHERE borrow_id IS NOT NULL AND borrow_id != '' AND (return_status IS NULL OR return_status = '' OR return_status = %s)",
					(string) LIBMNS_RETURN_APPROVED_BY_ADMIN,
					(string) LIBMNS_RETURN_NO_STATUS
				)
			);
			$wpdb->query(
				$wpdb->prepare(
					"UPDATE `{$tbl_book_return_escaped}` SET return_condition = CASE WHEN has_fine_status = %s THEN %s ELSE %s END WHERE return_condition IS NULL OR return_condition = ''",
					'1',
					LIBMNS_RETURN_CONDITION_LATE,
					LIBMNS_RETURN_CONDITION_NORMAL
				)
			);
			$this->owt7_library_backfill_migrated_return_ids( $tbl_book_return );
		}

		$tbl_book_late_fine = LIBMNS_Table_Helper_FREE::get_table_name( 'book_late_fine' );
		if ( $this->owt7_library_table_exists( $tbl_book_late_fine ) ) {
			$tbl_book_late_fine_escaped = str_replace( '`', '``', $tbl_book_late_fine );
			$wpdb->query(
				$wpdb->prepare(
					"UPDATE `{$tbl_book_late_fine_escaped}` SET fine_type = %s WHERE fine_type IS NULL OR fine_type = ''",
					LIBMNS_RETURN_CONDITION_LATE
				)
			);
		}
	}

	private function owt7_library_backfill_migrated_return_ids( $table_name ) {
		global $wpdb;

		$table_name_escaped = str_replace( '`', '``', $table_name );
		$rows               = $wpdb->get_results(
			"SELECT id FROM `{$table_name_escaped}` WHERE return_id IS NULL OR return_id = '' ORDER BY id ASC"
		);

		if ( ! is_array( $rows ) || empty( $rows ) ) {
			return;
		}

		$return_prefix = defined( 'LIBMNS_BOOK_RETURN_PREFIX' ) ? LIBMNS_BOOK_RETURN_PREFIX : 'LMSBR';

		foreach ( $rows as $row ) {
			$row_id = isset( $row->id ) ? (int) $row->id : 0;
			if ( $row_id <= 0 ) {
				continue;
			}

			$wpdb->update(
				$table_name,
				array(
					'return_id' => $this->owt7_library_generate_migrated_display_id( $return_prefix, $row_id ),
				),
				array(
					'id' => $row_id,
				),
				array( '%s' ),
				array( '%d' )
			);
		}
	}

	private function owt7_library_generate_migrated_display_id( $prefix, $row_id ) {
		return $prefix . str_pad( (string) max( 0, (int) $row_id ), 7, '0', STR_PAD_LEFT );
	}

	private function owt7_library_maybe_generate_book_copies_from_books() {
		global $wpdb;

		$tbl_books   = LIBMNS_Table_Helper_FREE::get_table_name( 'books' );
		$tbl_copies  = LIBMNS_Table_Helper_FREE::get_table_name( 'data_books_copies' );
		$tbl_borrow  = LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' );
		$tbl_return  = LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' );

		if ( ! $this->owt7_library_table_exists( $tbl_books ) || ! $this->owt7_library_table_exists( $tbl_copies ) ) {
			return;
		}

		$tbl_books_escaped = str_replace( '`', '``', $tbl_books );
		$books             = $wpdb->get_results(
			"SELECT id, book_id, bookcase_id, bookcase_section_id, stock_quantity FROM `{$tbl_books_escaped}` WHERE book_id IS NOT NULL AND book_id != ''",
			ARRAY_A
		);

		if ( ! is_array( $books ) || empty( $books ) ) {
			return;
		}

		$tbl_copies_escaped = str_replace( '`', '``', $tbl_copies );

		foreach ( $books as $book ) {
			$book_code = isset( $book['book_id'] ) ? trim( (string) $book['book_id'] ) : '';
			$book_row_id = isset( $book['id'] ) ? (int) $book['id'] : 0;
			if ( $book_code === '' || $book_row_id <= 0 ) {
				continue;
			}

			$existing_copies = (int) $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*) FROM `{$tbl_copies_escaped}` WHERE book_id = %s",
					$book_code
				)
			);
			if ( $existing_copies > 0 ) {
				continue;
			}

			$available_stock = max( 0, isset( $book['stock_quantity'] ) ? (int) $book['stock_quantity'] : 0 );
			$active_borrows  = $this->owt7_library_get_active_borrow_count_for_book( $book_row_id, $tbl_borrow, $tbl_return );
			$total_copies    = $available_stock + $active_borrows;

			if ( $total_copies <= 0 ) {
				continue;
			}

			$shelf_location = $this->owt7_library_get_book_shelf_location(
				isset( $book['bookcase_id'] ) ? (int) $book['bookcase_id'] : 0,
				isset( $book['bookcase_section_id'] ) ? (int) $book['bookcase_section_id'] : 0
			);

			for ( $i = 1; $i <= $total_copies; $i++ ) {
				$wpdb->insert(
					$tbl_copies,
					array(
						'book_id'          => $book_code,
						'accession_number' => $book_code . '-' . sprintf( '%05d', $i ),
						'status'           => ( $i <= $active_borrows ) ? 'borrowed' : 'available',
						'shelf_location'   => $shelf_location !== '' ? $shelf_location : null,
					),
					array( '%s', '%s', '%s', '%s' )
				);
			}
		}
	}

	private function owt7_library_get_active_borrow_count_for_book( $book_row_id, $tbl_borrow, $tbl_return ) {
		global $wpdb;

		if ( $book_row_id <= 0 || ! $this->owt7_library_table_exists( $tbl_borrow ) ) {
			return 0;
		}

		$tbl_borrow_escaped = str_replace( '`', '``', $tbl_borrow );

		if ( ! $this->owt7_library_table_exists( $tbl_return ) ) {
			return (int) $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*) FROM `{$tbl_borrow_escaped}` WHERE book_id = %d AND status = %d",
					$book_row_id,
					1
				)
			);
		}

		$tbl_return_escaped = str_replace( '`', '``', $tbl_return );

		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM `{$tbl_borrow_escaped}` AS borrow
				WHERE borrow.book_id = %d
				AND borrow.status = %d
				AND NOT EXISTS (
					SELECT 1 FROM `{$tbl_return_escaped}` AS rt
					WHERE rt.borrow_id = borrow.borrow_id
					AND rt.status = %d
				)",
				$book_row_id,
				1,
				1
			)
		);
	}

	private function owt7_library_get_book_shelf_location( $bookcase_id, $section_id ) {
		global $wpdb;

		$parts = array();

		if ( $bookcase_id > 0 ) {
			$tbl_bookcase = LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' );
			if ( $this->owt7_library_table_exists( $tbl_bookcase ) ) {
				$name = $wpdb->get_var(
					$wpdb->prepare(
						'SELECT name FROM `' . str_replace( '`', '``', $tbl_bookcase ) . '` WHERE id = %d LIMIT 1',
						$bookcase_id
					)
				);
				if ( is_string( $name ) && $name !== '' ) {
					$parts[] = $name;
				}
			}
		}

		if ( $section_id > 0 ) {
			$tbl_sections = LIBMNS_Table_Helper_FREE::get_table_name( 'sections' );
			if ( $this->owt7_library_table_exists( $tbl_sections ) ) {
				$name = $wpdb->get_var(
					$wpdb->prepare(
						'SELECT name FROM `' . str_replace( '`', '``', $tbl_sections ) . '` WHERE id = %d LIMIT 1',
						$section_id
					)
				);
				if ( is_string( $name ) && $name !== '' ) {
					$parts[] = $name;
				}
			}
		}

		return implode( ' | ', $parts );
	}

	private function owt7_library_copies_barcode_qrcode_columns() {
        return array(
            'is_barcode_exists' => 'TINYINT(1) NOT NULL DEFAULT 0',
            'is_qrcode_exists'  => 'TINYINT(1) NOT NULL DEFAULT 0',
            'barcode_path'      => 'VARCHAR(500) DEFAULT NULL',
            'qrcode_path'       => 'VARCHAR(500) DEFAULT NULL',
        );
    }

	public function owt7_lms_ensure_book_copies_code_columns() {
        global $wpdb;
        $tbl = LIBMNS_Table_Helper_FREE::get_table_name( 'data_books_copies' );
        if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $tbl ) ) !== $tbl ) {
            return;
        }
        $this->owt7_library_add_missing_columns( $tbl, $this->owt7_library_copies_barcode_qrcode_columns() );
    }

	private function owt7_library_add_missing_columns( $table_name, $columns ) {
		global $wpdb;
		if ( empty( $columns ) ) {
			return;
		}
		$table_name_escaped = str_replace( '`', '``', $table_name );
		$existing = $wpdb->get_col( "SHOW COLUMNS FROM `{$table_name_escaped}`" );
		if ( ! is_array( $existing ) ) {
			return;
		}
		foreach ( $columns as $column_name => $definition ) {
			if ( in_array( $column_name, $existing, true ) ) {
				continue;
			}
			$col_escaped = str_replace( '`', '``', $column_name );
			$sql = sprintf(
				'ALTER TABLE `%s` ADD COLUMN `%s` %s',
				$table_name_escaped,
				$col_escaped,
				$definition
			);
			$wpdb->query( $sql );
		}
	}

	private function owt7_library_premium_columns_users() {
		return array(
			'wp_user'    => 'int NOT NULL DEFAULT "0"',
			'wp_user_id' => 'bigint(20) UNSIGNED NOT NULL DEFAULT 0',
		);
	}

	private function owt7_library_upgrade_books_author_publication_columns( $tbl_books ) {
		global $wpdb;
		$table_name_escaped = str_replace( '`', '``', $tbl_books );
		$cols = $wpdb->get_results( "SHOW COLUMNS FROM `{$table_name_escaped}` WHERE Field IN ('author_name','publication_name')", ARRAY_A );
		if ( ! is_array( $cols ) ) {
			return;
		}
		foreach ( $cols as $col ) {
			$field = $col['Field'];
			$type  = isset( $col['Type'] ) ? strtolower( $col['Type'] ) : '';
			if ( strpos( $type, 'varchar' ) !== false ) {
				$col_escaped = str_replace( '`', '``', $field );
				$wpdb->query( "ALTER TABLE `{$table_name_escaped}` MODIFY COLUMN `{$col_escaped}` TEXT DEFAULT NULL" );
			}
		}
	}

	private function owt7_library_premium_columns_books() {
		return array(
			'is_woocom_product'         => 'INT NOT NULL DEFAULT "0"',
			'is_woocom_stock'            => 'INT NOT NULL DEFAULT "0"',
			'woocom_regular_price'       => 'VARCHAR(250) DEFAULT NULL',
			'woocom_sale_price'          => 'VARCHAR(250) DEFAULT NULL',
			'woocom_book_preview_pdf_link' => 'TEXT DEFAULT NULL',
			'woocom_book_pdf_link'      => 'TEXT DEFAULT NULL',
		);
	}

	private function owt7_library_premium_columns_book_borrow() {
		return array(
			'wp_user'           => 'int NOT NULL DEFAULT "0"',
			'is_self_checkout'  => 'int NOT NULL DEFAULT "0"',
			'checkout_status'   => 'enum("1", "2", "3", "4", "5") NOT NULL DEFAULT "5" COMMENT "1 - approved_by_admin, 2 - self_approved, 3 - checkout_pending, 4 - checkout_rejected, 5 - no_status"',
		);
	}

	private function owt7_library_premium_columns_book_return() {
		return array(
			'wp_user'         => 'int NOT NULL DEFAULT "0"',
			'is_self_return'  => 'int NOT NULL DEFAULT "0"',
			'return_status'   => 'enum("1", "2", "3", "4", "5") NOT NULL DEFAULT "5" COMMENT "1 - approved_by_admin, 2 - self_returned, 3 - return_pending, 4 - return_rejected, 5 - no_status"',
			'return_id'        => 'VARCHAR(50) DEFAULT NULL',
			'return_condition' => 'VARCHAR(50) DEFAULT NULL',
			'return_remark'   => 'TEXT DEFAULT NULL',
		);
	}

	private function owt7_library_premium_columns_book_late_fine() {
		return array(
			'wp_user'  => 'int NOT NULL DEFAULT "0"',
			'fine_type' => 'VARCHAR(50) DEFAULT "late_return" COMMENT "late_return, damaged_book, lost_book, missing_pages"',
		);
	}

	private function owt7_library_backfill_return_id( $table_name ) {
		global $wpdb;
		$table_escaped = str_replace( '`', '``', $table_name );
		$existing = $wpdb->get_col( "SHOW COLUMNS FROM `{$table_escaped}`" );
		if ( ! is_array( $existing ) ) {
			return;
		}
		if ( in_array( 'return_code', $existing, true ) && in_array( 'return_id', $existing, true ) ) {
			$wpdb->query( "UPDATE `{$table_escaped}` SET return_id = return_code WHERE (return_id IS NULL OR return_id = '') AND return_code IS NOT NULL AND return_code != ''" );
			$col_escaped = str_replace( '`', '``', 'return_code' );
			$wpdb->query( "ALTER TABLE `{$table_escaped}` DROP COLUMN `{$col_escaped}`" );
		}
	}

	private function owt7_library_add_accession_number_after_book_id() {
		global $wpdb;
		$tables = array(
			LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ),
			LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' ),
			LIBMNS_Table_Helper_FREE::get_table_name( 'book_late_fine' ),
		);
		$column_def = 'VARCHAR(250) DEFAULT NULL';
		$after_col  = 'book_id';
		foreach ( $tables as $table_name ) {
			if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) !== $table_name ) {
				continue;
			}
			$table_escaped = str_replace( '`', '``', $table_name );
			$existing     = $wpdb->get_col( "SHOW COLUMNS FROM `{$table_escaped}`" );
			if ( ! is_array( $existing ) || in_array( 'accession_number', $existing, true ) ) {
				continue;
			}
			$wpdb->query( "ALTER TABLE `{$table_escaped}` ADD COLUMN `accession_number` {$column_def} AFTER `{$after_col}`" );
		}
	}

	public function owt7_library_insert_default_data($data_installer = false){
        
        global $wpdb;
		$tbl_branch = LIBMNS_Table_Helper_FREE::get_table_name( 'branches' );
		if ( $this->owt7_library_table_exists( $tbl_branch ) ) {
			$branch_exists = (int) $wpdb->get_var(
				$wpdb->prepare(
					'SELECT COUNT(*) FROM `' . str_replace( '`', '``', $tbl_branch ) . '` WHERE name = %s',
					'No Branch'
				)
			);

			if ( $branch_exists === 0 ) {
				$wpdb->insert(
					$tbl_branch,
					array(
						'name' => 'No Branch',
					),
					array( '%s' )
				);
			}
		}
    }
	
	private function owt7_library_options(){
        update_option("owt7_library_version", LIBMNS_VERSION);
        update_option("owt7_library_system", serialize([
            "lms" => "free",
            "type" => LIBMNS_PLUGIN_SLUG
        ]));
        update_option("owt7_library_db_tables", [
            LIBMNS_Table_Helper_FREE::get_table_name( 'branches' ),
            LIBMNS_Table_Helper_FREE::get_table_name( 'users' ),
            LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' ),
            LIBMNS_Table_Helper_FREE::get_table_name( 'sections' ),
            LIBMNS_Table_Helper_FREE::get_table_name( 'categories' ),
            LIBMNS_Table_Helper_FREE::get_table_name( 'books' ),
            LIBMNS_Table_Helper_FREE::get_table_name( 'issue_days' ),
            LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ),
            LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' ),
            LIBMNS_Table_Helper_FREE::get_table_name( 'book_late_fine' ),
            LIBMNS_Table_Helper_FREE::get_table_name( 'custom_labels' ),
        ]);
        update_option("owt7_lms_late_fine_currency", "1");
        update_option("owt7_lms_country", "India");
        update_option("owt7_lms_currency", "INR");
		update_option( 'owt7_lms_theme_primary', LIBMNS_THEME_PRIMARY_DEFAULT );
		update_option( 'owt7_lms_theme_accent', LIBMNS_THEME_ACCENT_DEFAULT );
		update_option( 'owt7_lms_theme_action_clone', LIBMNS_THEME_ACTION_CLONE_DEFAULT );
		update_option( 'owt7_lms_theme_action_view', LIBMNS_THEME_ACTION_VIEW_DEFAULT );
		update_option( 'owt7_lms_theme_action_edit', LIBMNS_THEME_ACTION_EDIT_DEFAULT );
		update_option( 'owt7_lms_theme_action_book_copies', LIBMNS_THEME_ACTION_BOOK_COPIES_DEFAULT );
		update_option( 'owt7_lms_theme_action_delete', LIBMNS_THEME_ACTION_DELETE_DEFAULT );
		$library_user_display_name = 'Library User (LMS)';
		add_role(
			'owt7_library_user',
			$library_user_display_name,
			array(
				'read'                            => true,
				'edit_posts'                      => true,
				'view_library_menu'               => true,
				'access_owt7_library_user_portal' => true,
			)
		);
		$wp_roles = wp_roles();
		if ( isset( $wp_roles->roles['owt7_library_user'] ) ) {
			$wp_roles->roles['owt7_library_user']['name'] = $library_user_display_name;
			if ( isset( $wp_roles->role_names['owt7_library_user'] ) ) {
				$wp_roles->role_names['owt7_library_user'] = $library_user_display_name;
			}
			update_option( $wp_roles->role_key, $wp_roles->roles );
		}

		// Allow Access Permission
		$roles = array( 'administrator' );
		foreach ( $roles as $role_name ) {
			$role = get_role( $role_name );
			if ( $role ) {
				$role->add_cap( 'manage_owt7_library_system' );
				$role->add_cap( 'view_library_menu' );
			}
		}
	}

	public static function ensure_library_user_role_and_caps() {
		$library_user_display_name = 'Library User (LMS)';
		$role = get_role( 'owt7_library_user' );
		if ( ! $role ) {
			add_role(
				'owt7_library_user',
				$library_user_display_name,
				array(
					'read'                            => true,
					'edit_posts'                      => true,
					'view_library_menu'               => true,
					'access_owt7_library_user_portal' => true,
				)
			);
			$wp_roles = wp_roles();
			if ( isset( $wp_roles->roles['owt7_library_user'] ) ) {
				$wp_roles->roles['owt7_library_user']['name'] = $library_user_display_name;
				if ( isset( $wp_roles->role_names['owt7_library_user'] ) ) {
					$wp_roles->role_names['owt7_library_user'] = $library_user_display_name;
				}
				update_option( $wp_roles->role_key, $wp_roles->roles );
			}
		} else {

			if ( ! $role->has_cap( 'edit_posts' ) ) {
				$role->add_cap( 'edit_posts' );
			}
		}
		$admin_role = get_role( 'administrator' );
		if ( $admin_role && ! $admin_role->has_cap( 'view_library_menu' ) ) {
			$admin_role->add_cap( 'view_library_menu' );
		}
	}

	private function owt7_library_normalize_free_borrow_days() {
		global $wpdb;

		$tbl_issue_days = LIBMNS_Table_Helper_FREE::get_table_name( 'issue_days' );
		if ( ! $this->owt7_library_table_exists( $tbl_issue_days ) ) {
			return;
		}

		$tbl_issue_days_escaped = str_replace( '`', '``', $tbl_issue_days );
		$active_rows            = (int) $wpdb->get_var( "SELECT COUNT(*) FROM `{$tbl_issue_days_escaped}` WHERE status = 1" );

		if ( $active_rows === 0 ) {
			$wpdb->insert(
				$tbl_issue_days,
				array(
					'days'   => LIBMNS_DEFAULT_BORROW_DAYS,
					'status' => 1,
				),
				array( '%d', '%d' )
			);
		}
	}

    private function owt7_library_shortcodes(){

        global $wpdb;

        $pages = [
            [
                'title' => "Library Books",
                'content' => "[owt7_library_books]"
            ]
        ];

        // Create pages
        foreach ($pages as $page) {

            $slug = "wp-" . sanitize_title($page['title']);

            $is_page_exists = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT ID FROM {$wpdb->prefix}posts WHERE post_name = %s AND post_type = 'page' AND post_status IN ('publish', 'draft', 'pending')",
                    $slug
                )
            );

            if (!empty($is_page_exists)) {
                //
            }else{

                wp_insert_post(array(
                    'post_title'   => $page['title'],
                    'post_content' => $page['content'],
                    'post_status'  => 'publish',
                    'post_type'    => 'page',
                    'post_name'    => $slug,
                    'post_author'  => 1, // Admin user ID
                    'post_date'    => current_time('mysql'),
                    'post_date_gmt' => current_time('mysql', true)
                ));
            }
        }

    }

	// Helper functions: Return table names

    public function owt7_library_tbl_users(){
        return LIBMNS_Table_Helper_FREE::get_table_name( 'users' );
    }

    public function owt7_library_tbl_books() {
        return LIBMNS_Table_Helper_FREE::get_table_name( 'books' );
    }

    public function owt7_library_tbl_bookcase(){
        return LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' );
    }

    public function owt7_library_tbl_bookcase_sections(){
        return LIBMNS_Table_Helper_FREE::get_table_name( 'sections' );
    }

    public function owt7_library_tbl_branch(){
        return LIBMNS_Table_Helper_FREE::get_table_name( 'branches' );
    }

    public function owt7_library_tbl_category(){
        return LIBMNS_Table_Helper_FREE::get_table_name( 'categories' );
    }

    public function owt7_library_tbl_book_borrow(){
        return LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' );
    }

    public function owt7_library_tbl_book_return(){
        return LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' );
    }

    public function owt7_library_tbl_book_late_fine(){
        return LIBMNS_Table_Helper_FREE::get_table_name( 'book_late_fine' );
    }

    public function owt7_library_tbl_issue_days(){
        return LIBMNS_Table_Helper_FREE::get_table_name( 'issue_days' );
    }

    public function owt7_library_tbl_data_backups(){
        return LIBMNS_Table_Helper_FREE::get_table_name( 'data_backups' );
    }

    public function owt7_library_tbl_data_books_copies(){
        return LIBMNS_Table_Helper_FREE::get_table_name( 'data_books_copies' );
    }
	
    // ...
}