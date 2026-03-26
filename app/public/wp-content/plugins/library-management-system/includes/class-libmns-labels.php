<?php
/**
 * Custom labels (texts) per module – DB override with .po/.mo fallback.
 *
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/includes
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Libmns_Labels_FREE {

	const TEXT_DOMAIN = 'library-management-system';

	private static $cache = null;

	private static $locale_columns = array(
		'en' => 'text_en',
		'fr' => 'text_fr',
		'es' => 'text_es',
		'it' => 'text_it',
	);

	public static function get_table_name() {
		return LIBMNS_Table_Helper_FREE::get_table_name( 'custom_labels' );
	}

	public static function table_exists() {
		global $wpdb;
		$tbl = self::get_table_name();
		return $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $tbl ) ) === $tbl;
	}

	public static function get_current_locale_column() {
		$locale = function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = is_string( $locale ) ? strtolower( substr( $locale, 0, 2 ) ) : 'en';
		return isset( self::$locale_columns[ $locale ] ) ? self::$locale_columns[ $locale ] : 'text_en';
	}

	public static function get_all_modules() {
		return array(
			'menus'            => __( 'Menus/Submenus', 'library-management-system' ),
			'dashboard'        => __( 'Dashboard', 'library-management-system' ),
			'users'            => __( 'Manage Users', 'library-management-system' ),
			'bookcase_section' => __( 'Manage Bookcase & Section', 'library-management-system' ),
			'books'            => __( 'Manage Books', 'library-management-system' ),
			'transactions'     => __( 'Book Transactions', 'library-management-system' ),
			'reports'         => __( 'Reports', 'library-management-system' ),
			'settings'        => __( 'All Settings', 'library-management-system' ),
			'about'           => __( 'About LMS', 'library-management-system' ),
			'messages'        => __( 'Success/Error Messages', 'library-management-system' ),
		);
	}

	public static function get_labels_definition() {
		return array(
			'menus' => array(
				'Library Management' => 'Library Management',
				'Dashboard' => 'Dashboard',
				'Manage Users' => 'Manage Users',
				'Manage Bookcase & Section' => 'Manage Bookcase & Section',
				'Manage Books' => 'Manage Books',
				'Book Transactions' => 'Book Transactions',
				'Reports' => 'Reports',
				'All Settings' => 'All Settings',
				'About LMS' => 'About LMS',
			),
			'dashboard' => array(
				'Library Management System' => 'Library Management System',
				'Premium' => 'Premium',
				'Book Catalog' => 'Book Catalog',
				'Manage Books' => 'Manage Books',
				'Borrowers' => 'Borrowers',
				'Manage Borrowers' => 'Manage Borrowers',
				'Bookcases & Sections' => 'Bookcases & Sections',
				'Manage Bookcases' => 'Manage Bookcases',
				'Circulation & Reports' => 'Circulation & Reports',
				'View Reports' => 'View Reports',
				'Settings' => 'Settings',
				'Open Settings' => 'Open Settings',
				'Backup & Restore' => 'Backup & Restore',
				'Introduction' => 'Introduction',
				'Features' => 'Features',
				'Premium Library Management System' => 'Premium Library Management System',
				'Basic' => 'Basic',
			),
			'users' => array(
				'Manage Users' => 'Manage Users',
				'Add User' => 'Add User',
				'User List' => 'User List',
				'User ID' => 'User ID',
				'Select Branch' => 'Select Branch',
				'Name' => 'Name',
				'Email' => 'Email',
				'Phone Number' => 'Phone Number',
				'Gender' => 'Gender',
				'Address' => 'Address',
				'Status' => 'Status',
				'Profile Image' => 'Profile Image',
				'Edit' => 'Edit',
				'View' => 'View',
				'Delete' => 'Delete',
				'Back' => 'Back',
				'Submit & Save' => 'Submit & Save',
				'Active' => 'Active',
				'Inactive' => 'Inactive',
				'-- Select Branch --' => '-- Select Branch --',
				'-- Select Status --' => '-- Select Status --',
				'User Details' => 'User Details',
				'Branch' => 'Branch',
				'Created at' => 'Created at',
				'No Branch' => 'No Branch',
			),
			'books' => array(
				'Manage Books' => 'Manage Books',
				'Add New Book' => 'Add New Book',
				'Add Book' => 'Add Book',
				'Book List' => 'Book List',
				'Book ID' => 'Book ID',
				'Category' => 'Category',
				'Bookcase' => 'Bookcase',
				'Section' => 'Section',
				'Book Name' => 'Book Name',
				'Author(s) Name' => 'Author(s) Name',
				'Publication(s) Name' => 'Publication(s) Name',
				'Publication Year' => 'Publication Year',
				'Publication Location' => 'Publication Location',
				'Price' => 'Price',
				'ISBN' => 'ISBN',
				'Book URL' => 'Book URL',
				'Total Copies' => 'Total Copies',
				'Book Language' => 'Book Language',
				'Number of Page(s)' => 'Number of Page(s)',
				'Description' => 'Description',
				'Cover Image' => 'Cover Image',
				'Upload Cover Image' => 'Upload Cover Image',
				'-- Select Category --' => '-- Select Category --',
				'-- Select Bookcase --' => '-- Select Bookcase --',
				'-- Select Section --' => '-- Select Section --',
				'Basic Details' => 'Basic Details',
				'Stock Quantity' => 'Stock Quantity',
				'Total Books' => 'Total Books',
				'Book Details' => 'Book Details',
				'Book Copies' => 'Book Copies',
				'List Book' => 'List Book',
			),
			'bookcases' => array(
				'Manage Bookcase & Section' => 'Manage Bookcase & Section',
				'Add Bookcase' => 'Add Bookcase',
				'List Bookcase' => 'List Bookcase',
				'Name' => 'Name',
				'Status' => 'Status',
				'Edit' => 'Edit',
				'Delete' => 'Delete',
				'Back' => 'Back',
				'Submit & Save' => 'Submit & Save',
				'Active' => 'Active',
				'Inactive' => 'Inactive',
				'-- Select Status --' => '-- Select Status --',
			),
			'sections' => array(
				'Section' => 'Section',
				'Sections' => 'Sections',
				'Add Section' => 'Add Section',
				'List Section' => 'List Section',
				'Bookcase' => 'Bookcase',
				'Name' => 'Name',
				'Status' => 'Status',
				'-- Select Bookcase --' => '-- Select Bookcase --',
				'-- Select Status --' => '-- Select Status --',
				'Edit' => 'Edit',
				'Delete' => 'Delete',
				'Back' => 'Back',
				'Submit & Save' => 'Submit & Save',
				'Active' => 'Active',
				'Inactive' => 'Inactive',
			),
			'categories' => array(
				'Category' => 'Category',
				'Add New Category' => 'Add New Category',
				'Add Category' => 'Add Category',
				'List Category' => 'List Category',
				'Category List' => 'Category List',
				'Name' => 'Name',
				'Status' => 'Status',
				'-- Select Status --' => '-- Select Status --',
				'Edit' => 'Edit',
				'Delete' => 'Delete',
				'Back' => 'Back',
				'Submit & Save' => 'Submit & Save',
				'Active' => 'Active',
				'Inactive' => 'Inactive',
			),
			'branches' => array(
				'Branch' => 'Branch',
				'Add Branch' => 'Add Branch',
				'List Branch' => 'List Branch',
				'Branch Name' => 'Branch Name',
				'Status' => 'Status',
				'-- Select Status --' => '-- Select Status --',
				'Edit' => 'Edit',
				'Delete' => 'Delete',
				'Back' => 'Back',
				'Submit & Save' => 'Submit & Save',
				'Active' => 'Active',
				'Inactive' => 'Inactive',
			),
			'transactions' => array(
				'Book Transactions' => 'Book Transactions',
				'Borrow a Book' => 'Borrow a Book',
				'Books Return' => 'Books Return',
				'Books Borrow History' => 'Books Borrow History',
				'Books Return History' => 'Books Return History',
				'-- Select User --' => '-- Select User --',
				'-- Select Book --' => '-- Select Book --',
				'-- Select Branch --' => '-- Select Branch --',
				'-- Select Days --' => '-- Select Days --',
				'Filter by:' => 'Filter by:',
				'Borrow ID' => 'Borrow ID',
				'Return Date' => 'Return Date',
				'Days' => 'Days',
				'Issued on' => 'Issued on',
				'Return by' => 'Return by',
				'Return Pending' => 'Return Pending',
				'Returned' => 'Returned',
				'Return' => 'Return',
				'Total Days' => 'Total Days',
				'Issued' => 'Issued',
				'Returned with fine' => 'Returned with fine',
				'No fine' => 'No fine',
				'Pay Fine' => 'Pay Fine',
				'Fine Status' => 'Fine Status',
				'Action' => 'Action',
				'Return Books' => 'Return Books',
				'Borrowed Books' => 'Borrowed Books',
				'Borrow Date' => 'Borrow Date',
				'S No' => 'S No',
				'User Details' => 'User Details',
				'Book Details' => 'Book Details',
				'Borrow Details (Y-m-d)' => 'Borrow Details (Y-m-d)',
				'-- All --' => '-- All --',
				'How do you want to select the book?' => 'How do you want to select the book?',
				'Scan Barcode' => 'Scan Barcode',
				'Upload Barcode Image' => 'Upload Barcode Image',
				'Manual Selection' => 'Manual Selection',
				'Scan book barcode with camera' => 'Scan book barcode with camera',
				'Click "Start camera" to scan the barcode on the book.' => 'Click "Start camera" to scan the barcode on the book.',
				'Start camera' => 'Start camera',
				'Stop camera' => 'Stop camera',
				'Upload an image containing the book barcode' => 'Upload an image containing the book barcode',
				'Drag and drop an image here, or click to browse' => 'Drag and drop an image here, or click to browse',
				'Scan From Image' => 'Scan From Image',
			),
			'reports' => array(
				'Reports' => 'Reports',
				'Export' => 'Export',
				'Back' => 'Back',
				'Overview' => 'Overview',
				'Stock' => 'Stock',
				'Transactions' => 'Transactions',
				'Books' => 'Books',
				'Users' => 'Users',
				'Fines' => 'Fines',
				'Total Books' => 'Total Books',
				'Total stock units' => 'Total stock units',
				'Total Users' => 'Total Users',
				'Total Borrows' => 'Total Borrows',
				'This month' => 'This month',
				'Total Returns' => 'Total Returns',
				'Currently Issued' => 'Currently Issued',
				'Pending' => 'Pending',
				'Checkout' => 'Checkout',
				'Return' => 'Return',
				'Late Fines' => 'Late Fines',
				'record(s)' => 'record(s)',
				'Unpaid' => 'Unpaid',
				'Paid' => 'Paid',
				'Books by Category' => 'Books by Category',
				'Count' => 'Count',
				'No categories with books.' => 'No categories with books.',
				'Users by Branch' => 'Users by Branch',
				'No branches with users.' => 'No branches with users.',
				'Per-book stock: how many in stock, how many issued, and how many left (available).' => 'Per-book stock: how many in stock, how many issued, and how many left (available).',
				'In Stock' => 'In Stock',
				'Left' => 'Left',
				'No books in catalog.' => 'No books in catalog.',
				'Recent borrows and returns (latest 100 each).' => 'Recent borrows and returns (latest 100 each).',
				'Recent Borrows' => 'Recent Borrows',
				'Return date' => 'Return date',
				'Borrowed on' => 'Borrowed on',
				'No borrows yet.' => 'No borrows yet.',
				'Recent Returns' => 'Recent Returns',
				'Return ID' => 'Return ID',
				'Returned on' => 'Returned on',
				'No returns yet.' => 'No returns yet.',
			),
			'settings' => array(
				'Settings' => 'Settings',
				'General' => 'General',
				'Appearance' => 'Appearance',
				'Library & Display' => 'Library & Display',
				'Data & Tools' => 'Data & Tools',
				'Permissions' => 'Permissions',
				'Country & Currency' => 'Country & Currency',
				'Loan Periods (Borrow Days)' => 'Loan Periods (Borrow Days)',
				'Late Return Fines' => 'Late Return Fines',
				'Form Field Requirements' => 'Form Field Requirements',
				'Open' => 'Open',
				'Manage Labels / Texts' => 'Manage Labels / Texts',
				'Admin Theme & Colors' => 'Admin Theme & Colors',
				'Public Library Page Settings' => 'Public Library Page Settings',
				'Library Page Layout' => 'Library Page Layout',
				'Book Store Page Layout' => 'Book Store Page Layout',
				'WordPress User Sync & Roles' => 'WordPress User Sync & Roles',
				'Database Health' => 'Database Health',
				'Import Data (CSV)' => 'Import Data (CSV)',
				'Backup & Restore' => 'Backup & Restore',
				'Shortcodes' => 'Shortcodes',
				'Role Permissions' => 'Role Permissions',
				'Required & Optional Fields' => 'Required & Optional Fields',
				'Module' => 'Module',
				'Save' => 'Save',
				'Close' => 'Close',
				'English' => 'English',
				'French' => 'French',
				'Spanish' => 'Spanish',
				'Italian' => 'Italian',
				'Label / Context' => 'Label / Context',
				'Save Labels' => 'Save Labels',
				'— Select module —' => '— Select module —',
				'Labels saved successfully.' => 'Labels saved successfully.',
				'Set which form fields are required or optional for each module (Users, Bookcase, Section, Book, Category, Branch).' => 'Set which form fields are required or optional for each module (Users, Bookcase, Section, Book, Category, Branch).',
				'Manage all labels and texts shown in the plugin (table headers, form labels, buttons). Override per language (English, French, Spanish, Italian).' => 'Manage all labels and texts shown in the plugin (table headers, form labels, buttons). Override per language (English, French, Spanish, Italian).',
				'Select a module below to edit the texts shown in that section. First column is the label context; then enter or override the text for each language (English, French, Spanish, Italian). Saved values override .po/.mo when that language is active.' => 'Select a module below to edit the texts shown in that section. First column is the label context; then enter or override the text for each language (English, French, Spanish, Italian). Saved values override .po/.mo when that language is active.',
				'Edit the text for each language. Leave a language empty to use the default .po/.mo translation.' => 'Edit the text for each language. Leave a language empty to use the default .po/.mo translation.',
				'Reinstall Sample Data' => 'Reinstall Sample Data',
				'Remove Sample Data' => 'Remove Sample Data',
				'Install Sample Data' => 'Install Sample Data',
				'Settings categories' => 'Settings categories',
				'Customize buttons, table headers, and accents in the admin panel.' => 'Customize buttons, table headers, and accents in the admin panel.',
				'Choose which WordPress roles are synced to LMS when you click "Sync WP Users". Only users with selected roles can be added as borrowers.' => 'Choose which WordPress roles are synced to LMS when you click "Sync WP Users". Only users with selected roles can be added as borrowers.',
				'View status of all plugin database tables: columns, rows, size, and health.' => 'View status of all plugin database tables: columns, rows, size, and health.',
				'Configure user-level permissions for LMS roles.' => 'Configure user-level permissions for LMS roles.',
				'Set your library\'s country and default currency for fines, pricing, and display.' => 'Set your library\'s country and default currency for fines, pricing, and display.',
				'Define how many days users can borrow items (e.g. 7, 14, or 21 days per loan).' => 'Define how many days users can borrow items (e.g. 7, 14, or 21 days per loan).',
				'Configure fines or penalties when items are returned after the due date.' => 'Configure fines or penalties when items are returned after the due date.',
				'Control how the public-facing library page looks and behaves (search, filters, display options).' => 'Control how the public-facing library page looks and behaves (search, filters, display options).',
				'Customize layout and sections shown on the main library page (grid, list, columns).' => 'Customize layout and sections shown on the main library page (grid, list, columns).',
				'Customize layout and sections for the book store or shop page.' => 'Customize layout and sections for the book store or shop page.',
				'Import books, users, sections, and other data from CSV files.' => 'Import books, users, sections, and other data from CSV files.',
				'Export and restore your library data (books, users, loans, and more).' => 'Export and restore your library data (books, users, loans, and more).',
				'Copy shortcodes to embed the library, book store, or search on any page or post.' => 'Copy shortcodes to embed the library, book store, or search on any page or post.',
			),
			'messages' => array(
				'Submitted, please wait' => 'Submitted, please wait',
				'Submit' => 'Submit',
				'Success' => 'Success',
				'Error' => 'Error',
				'Upload Image' => 'Upload Image',
				'Select Section' => 'Select Section',
				'Select User' => 'Select User',
				'Select Book' => 'Select Book',
				'The Test Data Importer will install the sample dataset. If sample data was installed earlier, only those tracked sample records will be replaced. This action cannot be undone. Do you want to continue?' => 'The Test Data Importer will install the sample dataset. If sample data was installed earlier, only those tracked sample records will be replaced. This action cannot be undone. Do you want to continue?',
				'Are you sure you want to remove the tracked LMS sample data?' => 'Are you sure you want to remove the tracked LMS sample data?',
				'Are you sure want to pay the fine?' => 'Are you sure want to pay the fine?',
				'Are you sure want to delete?' => 'Are you sure want to delete?',
				'Are you sure want to return this book?' => 'Are you sure want to return this book?',
				'Verify License' => 'Verify License',
				'Submit & Save Permissions' => 'Submit & Save Permissions',
				'Please select a user.' => 'Please select a user.',
				'No data available to export.' => 'No data available to export.',
				'Showing "%s" borrowers' => 'Showing "%s" borrowers',
				'Please select at least one user to sync.' => 'Please select at least one user to sync.',
				'Save Required & Optional Fields' => 'Save Required & Optional Fields',
				'Required & optional fields settings saved successfully.' => 'Required & optional fields settings saved successfully.',
				'You do not have permission to save these settings.' => 'You do not have permission to save these settings.',
				'You do not have permission to perform this action.' => 'You do not have permission to perform this action.',
				'You do not have permission to access this page.' => 'You do not have permission to access this page.',
				'You do not have permission to view these settings.' => 'You do not have permission to view these settings.',
				'Invalid module.' => 'Invalid module.',
				'Failed to save labels.' => 'Failed to save labels.',
				'Labels table does not exist.' => 'Labels table does not exist.',
				'Branch name already taken' => 'Branch name already taken',
				'Successfully, Branch added to LMS' => 'Successfully, Branch added to LMS',
				'Failed to add Branch' => 'Failed to add Branch',
				'Branch value required' => 'Branch value required',
				'Successfully, Branch data updated' => 'Successfully, Branch data updated',
				'Branch not found' => 'Branch not found',
				'Invalid Operation' => 'Invalid Operation',
				'User already exists' => 'User already exists',
				'Successfully, User added to LMS' => 'Successfully, User added to LMS',
				'Failed to add User' => 'Failed to add User',
				'Required fields are missing' => 'Required fields are missing',
				'User ID already taken' => 'User ID already taken',
				'Successfully, User data updated' => 'Successfully, User data updated',
				'WordPress username is required when creating as WordPress user.' => 'WordPress username is required when creating as WordPress user.',
				'WordPress password must be at least 6 characters.' => 'WordPress password must be at least 6 characters.',
				'This WordPress username is already in use.' => 'This WordPress username is already in use.',
				'Invalid copy.' => 'Invalid copy.',
				'Copy not found.' => 'Copy not found.',
				'Failed to generate barcode.' => 'Failed to generate barcode.',
				'Barcode generated.' => 'Barcode generated.',
				'Failed to generate QR code.' => 'Failed to generate QR code.',
				'QR code generated.' => 'QR code generated.',
				'Invalid type.' => 'Invalid type.',
				'Invalid book.' => 'Invalid book.',
				'Book not found.' => 'Book not found.',
				'No copies to process.' => 'No copies to process.',
				'Barcode deleted.' => 'Barcode deleted.',
				'QR code deleted.' => 'QR code deleted.',
				'No copies selected.' => 'No copies selected.',
				'OK' => 'OK',
				'Missing' => 'Missing',
				'Required field "%s" is missing or empty.' => 'Required field "%s" is missing or empty.',
				'Public view settings saved successfully.' => 'Public view settings saved successfully.',
				'Every 30 Minutes' => 'Every 30 Minutes',
				'Upgrade to PRO' => 'Upgrade to PRO',
				'Manage Roles' => 'Manage Roles',
				'Manage Permissions' => 'Manage Permissions',
				'View Status' => 'View Status',
				'Check "Required" for fields that must be filled when creating or editing.' => 'Check "Required" for fields that must be filled when creating or editing.',
				'Select a module below, then check "Required" for each field that must be filled. Unchecked fields are optional. These settings apply to Add/Edit forms for each module.' => 'Select a module below, then check "Required" for each field that must be filled. Unchecked fields are optional. These settings apply to Add/Edit forms for each module.',
				'This will add WordPress users with the following roles to the LMS: %s. Users that are already in the LMS will be skipped. Continue?' => 'This will add WordPress users with the following roles to the LMS: %s. Users that are already in the LMS will be skipped. Continue?',
				'This will add WordPress users (all roles except Administrator) to the LMS. Users that are already in the LMS will be skipped. Continue?' => 'This will add WordPress users (all roles except Administrator) to the LMS. Users that are already in the LMS will be skipped. Continue?',
				'Library Management found <b>%1$s</b>. Please install and activate WooCommerce to <b>%2$s</b> your <b>%3$s</b>.' => 'Library Management found <b>%1$s</b>. Please install and activate WooCommerce to <b>%2$s</b> your <b>%3$s</b>.',
				'WooCommerce is not active' => 'WooCommerce is not active',
				'Setup' => 'Setup',
			),
			'deactivate' => array(
				'Would you like to Deactivate Library System?' => 'Would you like to Deactivate Library System?',
				"Note*: If 'Yes', it will help you to backup LMS data automatically." => "Note*: If 'Yes', it will help you to backup LMS data automatically.",
				'Backup Branches and Users data.' => 'Backup Branches and Users data.',
				'Backup Bookcases and Sections data.' => 'Backup Bookcases and Sections data.',
				'Backup Categories and Books data.' => 'Backup Categories and Books data.',
				'Backup Books Transactions.' => 'Backup Books Transactions.',
				'Backup LMS settings.' => 'Backup LMS settings.',
				'Note*: Backup Time Depends on Amount of LMS Data in Database.' => 'Note*: Backup Time Depends on Amount of LMS Data in Database.',
				'Do you want to create plugin data backup before deactivate?' => 'Do you want to create plugin data backup before deactivate?',
				'Deactivate Now' => 'Deactivate Now',
			),
			'about' => array(
				'Library Management System' => 'Library Management System',
				'Introduction' => 'Introduction',
				'Welcome to the most advanced Library Management System (LMS) Plugin. Our LMS plugin is designed to streamline and enhance the management of libraries, offering an intuitive and powerful solution for organizing books, users, bookcases, transactions, etc. Whether you are running a small community library or a large institutional library, our plugin provides the tools you need to operate efficiently and effectively.' => 'Welcome to the most advanced Library Management System (LMS) Plugin. Our LMS plugin is designed to streamline and enhance the management of libraries, offering an intuitive and powerful solution for organizing books, users, bookcases, transactions, etc. Whether you are running a small community library or a large institutional library, our plugin provides the tools you need to operate efficiently and effectively.',
				'Features' => 'Features',
				'Book Management' => 'Book Management',
				'Manage your library\'s collection with ease. Add, edit, and categorize books effortlessly.' => 'Manage your library\'s collection with ease. Add, edit, and categorize books effortlessly.',
				'User Management' => 'User Management',
				'Keep track of your library members, their borrow history, and contact details.' => 'Keep track of your library members, their borrow history, and contact details.',
				'Transaction Tracking' => 'Transaction Tracking',
				'Monitor all borrow and return transactions with detailed logs and reporting features.' => 'Monitor all borrow and return transactions with detailed logs and reporting features.',
				'Analytics and Reports' => 'Analytics and Reports',
				'Generate insightful reports to understand usage patterns and improve library services.' => 'Generate insightful reports to understand usage patterns and improve library services.',
				'Customizable Settings' => 'Customizable Settings',
				'Tailor the plugin settings to fit the specific needs of your library.' => 'Tailor the plugin settings to fit the specific needs of your library.',
				'Secure and Reliable' => 'Secure and Reliable',
				'Ensure the safety and integrity of your library data with our secure system architecture.' => 'Ensure the safety and integrity of your library data with our secure system architecture.',
				'Data Backup' => 'Data Backup',
				'Safeguard your data by creating backups regularly with our plugin\'s data backup feature.' => 'Safeguard your data by creating backups regularly with our plugin\'s data backup feature.',
				'Late Fine System' => 'Late Fine System',
				'Implement a late fine system to manage overdue books and encourage timely returns.' => 'Implement a late fine system to manage overdue books and encourage timely returns.',
				'Upload CSV and Save Data' => 'Upload CSV and Save Data',
				'Import CSV data files to LMS. Just follow some pattern to prepare data and feed into system.' => 'Import CSV data files to LMS. Just follow some pattern to prepare data and feed into system.',
				'Multilanguage Support' => 'Multilanguage Support',
				'Use the plugin in multiple languages, making it accessible to a wider audience. More than 5 Languages it supports.' => 'Use the plugin in multiple languages, making it accessible to a wider audience. More than 5 Languages it supports.',
				'Book Listing with Filters' => 'Book Listing with Filters',
				'Create a frontend page to list all books with filters, enhancing user browsing experience. It also opens single book page view. Plugin Shortcodes.' => 'Create a frontend page to list all books with filters, enhancing user browsing experience. It also opens single book page view. Plugin Shortcodes.',
				'Book Listing with Pagination Links' => 'Book Listing with Pagination Links',
				'Easy to move here and there to see list of books by pagination links.' => 'Easy to move here and there to see list of books by pagination links.',
				'Check Book Availability' => 'Check Book Availability',
				'Allow users to check the availability of books from the frontend page without login.' => 'Allow users to check the availability of books from the frontend page without login.',
				'Bulk Data Operation' => 'Bulk Data Operation',
				'Allow admin to do bulk operation for Delete, Move to Active Status, Inactive Status for any module like Bookcases, Sections, Categories, Books, etc' => 'Allow admin to do bulk operation for Delete, Move to Active Status, Inactive Status for any module like Bookcases, Sections, Categories, Books, etc',
				'Data Clone Function' => 'Data Clone Function',
				'Allow admin to create a copy of existing data for any module like Bookcases, Sections, Categories, Books, etc' => 'Allow admin to create a copy of existing data for any module like Bookcases, Sections, Categories, Books, etc',
				'Self Checkout / Return Book(s) by User' => 'Self Checkout / Return Book(s) by User',
				'Allow users to checkout any book by themselves. Additionally, they can return in the same way.' => 'Allow users to checkout any book by themselves. Additionally, they can return in the same way.',
				'Book(s) Borrow List / Return List of User' => 'Book(s) Borrow List / Return List of User',
				'A Page to frontend where user can check the complete history of book(s) borrowed and returned.' => 'A Page to frontend where user can check the complete history of book(s) borrowed and returned.',
				'Request to Borrow Book' => 'Request to Borrow Book',
				'Allow users to request or booking to borrow a book. Admin processes the user\'s request.' => 'Allow users to request or booking to borrow a book. Admin processes the user\'s request.',
				'Multiple Librarian to Manage LMS' => 'Multiple Librarian to Manage LMS',
				'LMS can be managed by more than one librarian admin.' => 'LMS can be managed by more than one librarian admin.',
				'Sync WP Users to LMS' => 'Sync WP Users to LMS',
				'You can add WP Users of any role into your LMS Users List in just a click.' => 'You can add WP Users of any role into your LMS Users List in just a click.',
				'Sell Library Books into WooCommerce Store' => 'Sell Library Books into WooCommerce Store',
				'Website owner can market PDF eBooks with WooCommerce store.' => 'Website owner can market PDF eBooks with WooCommerce store.',
			),
		);
	}

	public static function get_labels_for_module( $module ) {
		$definition = self::get_labels_definition();

		// Composite: Manage Bookcase & Section = bookcases + sections
		if ( $module === 'bookcase_section' ) {
			$out = array();
			foreach ( array( 'bookcases', 'sections' ) as $submod ) {
				if ( ! isset( $definition[ $submod ] ) ) {
					continue;
				}
				$keys = $definition[ $submod ];
				$rows = self::get_db_rows_by_module( $submod );
				foreach ( $keys as $msgid => $default_en ) {
					$row = array(
						'text_key'  => $msgid,
						'text_en'   => $default_en,
						'text_fr'   => '',
						'text_es'   => '',
						'text_it'   => '',
						'module'    => $submod,
					);
					if ( isset( $rows[ $msgid ] ) ) {
						$row['text_en'] = $rows[ $msgid ]['text_en'] !== null && $rows[ $msgid ]['text_en'] !== '' ? $rows[ $msgid ]['text_en'] : $default_en;
						$row['text_fr'] = (string) $rows[ $msgid ]['text_fr'];
						$row['text_es'] = (string) $rows[ $msgid ]['text_es'];
						$row['text_it'] = (string) $rows[ $msgid ]['text_it'];
					}
					$out[] = $row;
				}
			}
			return $out;
		}

		if ( ! isset( $definition[ $module ] ) ) {
			return array();
		}
		$keys = $definition[ $module ];
		$rows = self::get_db_rows_by_module( $module );
		$out = array();
		foreach ( $keys as $msgid => $default_en ) {
			$row = array(
				'text_key'  => $msgid,
				'text_en'   => $default_en,
				'text_fr'   => '',
				'text_es'   => '',
				'text_it'   => '',
			);
			if ( isset( $rows[ $msgid ] ) ) {
				$row['text_en'] = $rows[ $msgid ]['text_en'] !== null && $rows[ $msgid ]['text_en'] !== '' ? $rows[ $msgid ]['text_en'] : $default_en;
				$row['text_fr'] = (string) $rows[ $msgid ]['text_fr'];
				$row['text_es'] = (string) $rows[ $msgid ]['text_es'];
				$row['text_it'] = (string) $rows[ $msgid ]['text_it'];
			}
			$out[] = $row;
		}
		return $out;
	}

	private static function get_db_rows_by_module( $module ) {
		if ( ! self::table_exists() ) {
			return array();
		}
		global $wpdb;
		$tbl = self::get_table_name();
		$module = sanitize_text_field( $module );
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT text_key, text_en, text_fr, text_es, text_it FROM `{$tbl}` WHERE module = %s",
				$module
			),
			ARRAY_A
		);
		if ( ! is_array( $results ) ) {
			return array();
		}
		$keyed = array();
		foreach ( $results as $r ) {
			$keyed[ $r['text_key'] ] = $r;
		}
		return $keyed;
	}

	public static function save_labels( $labels ) {
		if ( ! self::table_exists() ) {
			return array( 'success' => false, 'message' => __( 'Labels table does not exist.', self::TEXT_DOMAIN ) );
		}
		global $wpdb;
		$tbl = self::get_table_name();
		// Allow all definition keys (so bookcases, sections etc. when saving from "Manage Bookcase & Section").
		$allowed_modules = array_keys( self::get_labels_definition() );
		$definition = self::get_labels_definition();
		$saved = 0;
		foreach ( $labels as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			$text_key = isset( $item['text_key'] ) ? $item['text_key'] : '';
			$module   = isset( $item['module'] ) ? $item['module'] : '';
			if ( $text_key === '' || $module === '' ) {
				continue;
			}
			if ( ! in_array( $module, $allowed_modules, true ) ) {
				continue;
			}
			if ( ! isset( $definition[ $module ][ $text_key ] ) ) {
				continue;
			}
			$text_en = isset( $item['text_en'] ) ? wp_unslash( $item['text_en'] ) : '';
			$text_fr = isset( $item['text_fr'] ) ? wp_unslash( $item['text_fr'] ) : '';
			$text_es = isset( $item['text_es'] ) ? wp_unslash( $item['text_es'] ) : '';
			$text_it = isset( $item['text_it'] ) ? wp_unslash( $item['text_it'] ) : '';
			$text_key = sanitize_text_field( $text_key );
			$module   = sanitize_text_field( $module );
			$text_en = wp_kses_post( $text_en );
			$text_fr = wp_kses_post( $text_fr );
			$text_es = wp_kses_post( $text_es );
			$text_it = wp_kses_post( $text_it );
			$wpdb->replace(
				$tbl,
				array(
					'text_key' => $text_key,
					'module'   => $module,
					'text_en'  => $text_en,
					'text_fr'  => $text_fr,
					'text_es'  => $text_es,
					'text_it'  => $text_it,
				),
				array( '%s', '%s', '%s', '%s', '%s', '%s' )
			);
			$saved++;
		}
		self::clear_cache();
		return array(
			'success' => true,
			'message'  => __( 'Labels saved successfully.', self::TEXT_DOMAIN ),
			'saved'    => $saved,
		);
	}

	public static function get_custom_translation( $text ) {
		if ( ! is_string( $text ) || $text === '' ) {
			return null;
		}
		if ( ! self::table_exists() ) {
			return null;
		}
		$col = self::get_current_locale_column();
		self::maybe_load_cache();
		$key = $text;
		if ( isset( self::$cache[ $key ][ $col ] ) && self::$cache[ $key ][ $col ] !== '' ) {
			return self::$cache[ $key ][ $col ];
		}
		return null;
	}

	private static function maybe_load_cache() {
		if ( self::$cache !== null ) {
			return;
		}
		self::$cache = array();
		if ( ! self::table_exists() ) {
			return;
		}
		global $wpdb;
		$tbl = self::get_table_name();
		$results = $wpdb->get_results( "SELECT text_key, text_en, text_fr, text_es, text_it FROM `{$tbl}`", ARRAY_A );
		if ( ! is_array( $results ) ) {
			return;
		}
		foreach ( $results as $r ) {
			self::$cache[ $r['text_key'] ] = $r;
		}
	}

	public static function clear_cache() {
		self::$cache = null;
	}

	public static function filter_gettext( $translated_text, $text, $domain ) {
		if ( $domain !== self::TEXT_DOMAIN ) {
			return $translated_text;
		}
		$custom = self::get_custom_translation( $text );
		if ( $custom !== null ) {
			return $custom;
		}
		return $translated_text;
	}

	public static function hook_gettext() {
		add_filter( 'gettext', array( __CLASS__, 'filter_gettext' ), 10, 3 );
		add_filter( 'gettext_with_context', array( __CLASS__, 'filter_gettext_with_context' ), 10, 4 );
	}

	public static function filter_gettext_with_context( $translated_text, $text, $context, $domain ) {
		if ( $domain !== self::TEXT_DOMAIN ) {
			return $translated_text;
		}
		$custom = self::get_custom_translation( $text );
		if ( $custom !== null ) {
			return $custom;
		}
		return $translated_text;
	}
}
