<?php
/**
 * Upgrade to PRO – Feature comparison page.
 *
 * @link       https://onlinewebtutorblog.com
 * @since      4.2
 * @package    Library_Management_System
 * @subpackage Library_Management_System/admin/views/lms
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Sanjay Kumar
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="owt7-lms owt7-lms-upgrade-pro">
	<div class="owt7-upgrade-pro-wrap">
		<header class="owt7-upgrade-header">
			<div class="owt7-upgrade-header-inner">
				<span class="owt7-upgrade-kicker"><?php esc_html_e( 'Upgrade to PRO', 'library-management-system' ); ?></span>
				<h1>
					<?php esc_html_e( 'Library Management System', 'library-management-system' ); ?>
					<sup>v<?php echo esc_html( LIBMNS_VERSION ); ?></sup>
					<span class="owt7-edition-badge"><?php esc_html_e( 'Free Version', 'library-management-system' ); ?></span>
				</h1>
				<p class="owt7-upgrade-header-sub">
					<?php esc_html_e( 'Explore a beautifully crafted comparison of every edition and discover the right solution for your library, from essential day-to-day management to fully advanced automation and staff workflows.', 'library-management-system' ); ?>
				</p>
			</div>
		</header>

		<section class="owt7-plan-overview">
			<div class="owt7-plan-card owt7-plan-card--free">
				<div class="owt7-plan-card-head">
					<h2><?php esc_html_e( 'Free Version', 'library-management-system' ); ?></h2>
					<span class="owt7-plan-chip"><?php esc_html_e( 'Current Entry Plan', 'library-management-system' ); ?></span>
				</div>
				<p><?php esc_html_e( 'A solid starting point for libraries that need essential book circulation, borrower records, fines, frontend catalogue visibility, and a dedicated member dashboard.', 'library-management-system' ); ?></p>
				<ul class="owt7-plan-points">
					<li><?php esc_html_e( 'Essential library management tools', 'library-management-system' ); ?></li>
					<li><?php esc_html_e( 'Public catalogue with filters and settings', 'library-management-system' ); ?></li>
					<li><?php esc_html_e( 'Controlled single-book borrowing workflow', 'library-management-system' ); ?></li>
				</ul>
			</div>

			<div class="owt7-plan-card owt7-plan-card--basic">
				<div class="owt7-plan-card-head">
					<h2><?php esc_html_e( 'Basic PRO', 'library-management-system' ); ?></h2>
					<span class="owt7-plan-chip owt7-plan-chip--basic"><?php esc_html_e( 'Growth Plan', 'library-management-system' ); ?></span>
				</div>
				<p><?php esc_html_e( 'Designed for growing libraries that need fewer limitations, smarter borrowing controls, richer reports, and dependable backup and monitoring tools.', 'library-management-system' ); ?></p>
				<ul class="owt7-plan-points">
					<li><?php esc_html_e( 'Unlimited operational data handling', 'library-management-system' ); ?></li>
					<li><?php esc_html_e( 'Reports, backups, health checks, and shortcodes', 'library-management-system' ); ?></li>
					<li><?php esc_html_e( 'Flexible multi-book borrowing rules', 'library-management-system' ); ?></li>
				</ul>
				<button type="button" class="owt7-btn-upgrade owt7-btn-upgrade--basic" data-plan="basic">
					<?php esc_html_e( 'Upgrade to Basic PRO', 'library-management-system' ); ?>
				</button>
			</div>

			<div class="owt7-plan-card owt7-plan-card--full">
				<div class="owt7-plan-card-head">
					<h2><?php esc_html_e( 'Full PRO', 'library-management-system' ); ?></h2>
					<span class="owt7-plan-chip owt7-plan-chip--full"><?php esc_html_e( 'Best Value', 'library-management-system' ); ?></span>
				</div>
				<p><?php esc_html_e( 'Created for advanced libraries that want barcode-driven circulation, multiple staff roles, multilingual interface control, email automation, and deep workflow customization.', 'library-management-system' ); ?></p>
				<ul class="owt7-plan-points">
					<li><?php esc_html_e( 'Barcode generation, scanning, and bulk downloads', 'library-management-system' ); ?></li>
					<li><?php esc_html_e( 'Advanced staff roles and permission controls', 'library-management-system' ); ?></li>
					<li><?php esc_html_e( 'Email templates, alerts, and multilingual tools', 'library-management-system' ); ?></li>
				</ul>
				<button type="button" class="owt7-btn-upgrade owt7-btn-upgrade--full" data-plan="full">
					<?php esc_html_e( 'Upgrade to Full PRO', 'library-management-system' ); ?>
				</button>
			</div>
		</section>

		<section class="owt7-comparison-section">
			<div class="owt7-section-heading">
				<div>
					<h2 class="owt7-section-title"><?php esc_html_e( 'Detailed Feature Comparison', 'library-management-system' ); ?></h2>
					<p class="owt7-section-subtitle"><?php esc_html_e( 'Every edition has been outlined below in a clearer and more meaningful way, so you can instantly see which version delivers the experience your library truly needs.', 'library-management-system' ); ?></p>
				</div>
			</div>

			<div class="owt7-comparison-table-wrap">
				<table class="owt7-comparison-table">
					<thead>
						<tr>
							<th class="col-feature"><?php esc_html_e( 'Feature', 'library-management-system' ); ?></th>
							<th class="col-free"><?php esc_html_e( 'Free', 'library-management-system' ); ?></th>
							<th class="col-basic-pro"><span class="owt7-plan-label"><?php esc_html_e( 'Basic PRO', 'library-management-system' ); ?></span></th>
							<th class="col-full-pro">
								<span class="owt7-plan-label owt7-plan-label--full"><?php esc_html_e( 'Full PRO', 'library-management-system' ); ?></span>
								<span class="owt7-badge-popular"><?php esc_html_e( 'Most Popular', 'library-management-system' ); ?></span>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr class="owt7-row-category">
							<td colspan="4"><?php esc_html_e( 'Free Version Features', 'library-management-system' ); ?></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Smart dashboard with card-based navigation', 'library-management-system' ); ?></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Essential user management', 'library-management-system' ); ?></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Core bookcase and section management', 'library-management-system' ); ?></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Everyday book management', 'library-management-system' ); ?></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Multiple copy handling with accession number', 'library-management-system' ); ?></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Smooth checkout and return management', 'library-management-system' ); ?></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Single-book checkout rule for each borrower', 'library-management-system' ); ?></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-cross">&#10007;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Built-in late fine calculation system', 'library-management-system' ); ?></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Late fine receipt generation', 'library-management-system' ); ?></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Customizable settings to match your workflow', 'library-management-system' ); ?></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Data import and export in CSV and Excel formats', 'library-management-system' ); ?></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'One-click table export in PDF, CSV, and Excel', 'library-management-system' ); ?></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Dedicated Library User dashboard experience', 'library-management-system' ); ?></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Library User role only access model', 'library-management-system' ); ?></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-cross">&#10007;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Frontend public page to showcase library books', 'library-management-system' ); ?></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Search filters and display controls for the public catalogue', 'library-management-system' ); ?></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Quick sample data installation for demo or testing', 'library-management-system' ); ?></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Fixed 30-day loan period', 'library-management-system' ); ?></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-cross">&#10007;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Switchable admin themes', 'library-management-system' ); ?></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>

						<tr class="owt7-row-category owt7-row-category--basic">
							<td colspan="4"><?php esc_html_e( 'Basic PRO Features', 'library-management-system' ); ?></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Everything included in the Free Version', 'library-management-system' ); ?></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Unlimited management of users, books, imports, exports, and other library data', 'library-management-system' ); ?></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Flexible loan period management', 'library-management-system' ); ?></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Allow members to borrow multiple books at the same time', 'library-management-system' ); ?></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Dedicated reports section with operational insights', 'library-management-system' ); ?></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Database health monitoring tools', 'library-management-system' ); ?></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Backup and restore for safer library management', 'library-management-system' ); ?></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Ready-to-use shortcodes', 'library-management-system' ); ?></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>

						<tr class="owt7-row-category owt7-row-category--full">
							<td colspan="4"><?php esc_html_e( 'Full PRO Features', 'library-management-system' ); ?></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Everything included in Basic PRO', 'library-management-system' ); ?></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Module-wise bulk delete operations', 'library-management-system' ); ?></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'One-click data cloning', 'library-management-system' ); ?></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Barcode generation for books and copies', 'library-management-system' ); ?></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Barcode-based checkout and return workflow', 'library-management-system' ); ?></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Bulk barcode downloads for book copies', 'library-management-system' ); ?></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'WordPress user sync by selected roles', 'library-management-system' ); ?></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Multiple staff roles: Librarian, Circulation Staff, and Library User', 'library-management-system' ); ?></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature">
								<?php esc_html_e( 'Advanced return fines for late returns, damaged books, and missing pages', 'library-management-system' ); ?>
							</td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Form field settings management', 'library-management-system' ); ?></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Software labels and interface text management', 'library-management-system' ); ?></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Multilingual support for Hindi, English, Italian, Spanish, and French', 'library-management-system' ); ?></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Book store layout settings management', 'library-management-system' ); ?></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Granular role permission controls', 'library-management-system' ); ?></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Professional email templates', 'library-management-system' ); ?></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Email integration to notify and engage users', 'library-management-system' ); ?></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>
						<tr>
							<td class="col-feature"><?php esc_html_e( 'Built-in announcements module', 'library-management-system' ); ?></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-cross">&#10007;</span></td>
							<td><span class="owt7-check">&#10003;</span></td>
						</tr>

						<tr class="owt7-row-cta">
							<td class="col-feature"><?php esc_html_e( 'Choose the edition that best matches your library size, workflow, and future growth', 'library-management-system' ); ?></td>
							<td><span class="owt7-plan-current"><?php esc_html_e( 'Current Plan', 'library-management-system' ); ?></span></td>
							<td>
								<button type="button" class="owt7-btn-upgrade owt7-btn-upgrade--basic" data-plan="basic">
									<?php esc_html_e( 'Upgrade to Basic PRO', 'library-management-system' ); ?>
								</button>
							</td>
							<td>
								<button type="button" class="owt7-btn-upgrade owt7-btn-upgrade--full" data-plan="full">
									<?php esc_html_e( 'Upgrade to Full PRO', 'library-management-system' ); ?>
								</button>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</section>

		<section class="owt7-custom-dev-section">
			<div class="owt7-custom-dev-inner">
				<div class="owt7-custom-dev-icon">
					<span class="dashicons dashicons-admin-tools"></span>
				</div>
				<div class="owt7-custom-dev-content">
					<h2><?php esc_html_e( 'Need Custom Development for Your Library?', 'library-management-system' ); ?></h2>
					<p>
						<?php esc_html_e( 'If your library needs something beyond the standard editions, we also offer custom development services. From unique workflows and tailored reports to third-party integrations, multilingual enhancements, and branded interface changes, we can shape the system around your exact goals.', 'library-management-system' ); ?>
					</p>
					<ul class="owt7-custom-dev-list">
						<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Custom modules and library-specific workflow improvements', 'library-management-system' ); ?></li>
						<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Third-party API, CRM, and system integrations', 'library-management-system' ); ?></li>
						<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Custom reports, notifications, multilingual content, and automation', 'library-management-system' ); ?></li>
						<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Branding, layout polishing, and UX enhancements', 'library-management-system' ); ?></li>
					</ul>
					<button type="button" class="owt7-btn-contact" id="owt7-open-contact-modal">
						<?php esc_html_e( 'Contact Us for Custom Development', 'library-management-system' ); ?>
					</button>
				</div>
			</div>
		</section>
	</div>
</div>

<div id="owt7-contact-modal" class="owt7-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="owt7-modal-title" style="display:none;">
	<div class="owt7-modal-box">
		<button type="button" class="owt7-modal-close" id="owt7-close-modal" aria-label="<?php esc_attr_e( 'Close', 'library-management-system' ); ?>">&#10005;</button>

		<div class="owt7-modal-header">
			<span class="dashicons dashicons-email-alt owt7-modal-icon"></span>
			<h2 id="owt7-modal-title"><?php esc_html_e( 'Get in Touch', 'library-management-system' ); ?></h2>
			<p id="owt7-modal-plan-desc" class="owt7-modal-plan-desc"></p>
		</div>

		<div class="owt7-modal-body">
			<div class="owt7-contact-card">
				<div class="owt7-contact-row">
					<span class="dashicons dashicons-admin-users owt7-contact-icon"></span>
					<div>
						<span class="owt7-contact-label"><?php esc_html_e( 'Author Name', 'library-management-system' ); ?></span>
						<span class="owt7-contact-value">Sanjay Kumar</span>
					</div>
				</div>
				<div class="owt7-contact-row">
					<span class="dashicons dashicons-email owt7-contact-icon"></span>
					<div>
						<span class="owt7-contact-label"><?php esc_html_e( 'Email', 'library-management-system' ); ?></span>
						<a href="mailto:onlinewebtutorhub@gmail.com" class="owt7-contact-value">onlinewebtutorhub@gmail.com</a>
					</div>
				</div>
				<div class="owt7-contact-row">
					<span class="dashicons dashicons-admin-site-alt3 owt7-contact-icon"></span>
					<div>
						<span class="owt7-contact-label"><?php esc_html_e( 'Website', 'library-management-system' ); ?></span>
						<a href="https://onlinewebtutorblog.com/" target="_blank" rel="noopener noreferrer" class="owt7-contact-value">onlinewebtutorblog.com</a>
					</div>
				</div>
			</div>

			<p class="owt7-modal-note">
				<?php esc_html_e( 'Share your library requirements with us and we will help you choose the right edition or build a custom solution for your needs.', 'library-management-system' ); ?>
			</p>

			<a href="mailto:onlinewebtutorhub@gmail.com?subject=LMS%20Plugin%20Upgrade%20Enquiry" class="owt7-btn-modal-email">
				<?php esc_html_e( 'Send Email', 'library-management-system' ); ?>
			</a>
		</div>
	</div>
</div>

<script>
(function() {
	'use strict';

	var modal = document.getElementById('owt7-contact-modal');
	var closeBtn = document.getElementById('owt7-close-modal');
	var contactBtn = document.getElementById('owt7-open-contact-modal');
	var planDesc = document.getElementById('owt7-modal-plan-desc');
	var upgradesBtns = document.querySelectorAll('.owt7-btn-upgrade');

	var planMessages = {
		basic: '<?php echo esc_js( __( "You are interested in Basic PRO. Contact us to get pricing, upgrade guidance, and activation details.", 'library-management-system' ) ); ?>',
		full: '<?php echo esc_js( __( "You are interested in Full PRO. Contact us to unlock advanced barcode, role, email, and automation features.", 'library-management-system' ) ); ?>',
		custom: '<?php echo esc_js( __( "Need custom development? Share your exact requirements and we will craft a solution for your library.", 'library-management-system' ) ); ?>'
	};

	function openModal(plan) {
		planDesc.textContent = planMessages[plan] || planMessages.custom;
		modal.style.display = 'flex';
		document.body.classList.add('owt7-modal-open');
		closeBtn.focus();
	}

	function closeModal() {
		modal.style.display = 'none';
		document.body.classList.remove('owt7-modal-open');
	}

	upgradesBtns.forEach(function(btn) {
		btn.addEventListener('click', function() {
			openModal(this.getAttribute('data-plan'));
		});
	});

	if (contactBtn) {
		contactBtn.addEventListener('click', function() {
			openModal('custom');
		});
	}

	closeBtn.addEventListener('click', closeModal);

	modal.addEventListener('click', function(e) {
		if (e.target === modal) {
			closeModal();
		}
	});

	document.addEventListener('keydown', function(e) {
		if (e.key === 'Escape' && modal.style.display === 'flex') {
			closeModal();
		}
	});
}());
</script>
