<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'Zit(W:i6hB>&g/;3NTY[:>UjL[XUi&>)~T/`~q4aQ3q.Blz;n&e4{nW0~3Ddi{e7' );
define( 'SECURE_AUTH_KEY',   'dsF$q#K_UrsdVn)#*rcK$x3NOnhguG-[d7*hLB~2oKV6-wa!hOAV(vXsmXc>E)}y' );
define( 'LOGGED_IN_KEY',     'wA=Iiq{euD_0&WLyL*Ls#(97R{XA2a,lZ^SLYEzEGtDO+1@jzX92{Sxh[Opx4>78' );
define( 'NONCE_KEY',         'T0Au{{$<cJ,`9Qm>8o/WMu;Jb,dniK3mqZxs1s?@PffT(8Wuc:c9.^gS/R)1e~g`' );
define( 'AUTH_SALT',         'h6ZAqGO6! bdGw~OK.EUXO}w=].:b*}]NQp4R$Zk!>GXOidvHU3-Nl=nOB~y*%]/' );
define( 'SECURE_AUTH_SALT',  '_<KnEW=4SB9*^x[].?i3.YD`j.ZW5*!1q9rsDn*k6~7O.BlCno5$wt-r2T=0`k2W' );
define( 'LOGGED_IN_SALT',    'ZJDTX]GD${D}qwajCNiJ)cGr)|CD&:k52aZYn(` tmL/1R`hP)w5P?5w1$;=f1|6' );
define( 'NONCE_SALT',        'Xd]0F 0#C,tLuy$SmD>DVn`i:*$NOZ.nOHW6epDcGoWZ&?D(lRQ@ZI<>r2]|RNwx' );
define( 'WP_CACHE_KEY_SALT', 'yP=7i!|IK}&bYY!M!jSb-x$DbPWcRsE3#P9)J/Hrm?(|3>Ra7v{x~V:)yb.&R6PV' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}



define( 'WP_ALLOW_MULTISITE', true );
define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', false );
$base = '/';
define( 'DOMAIN_CURRENT_SITE', 'top-bookshelf.local' );
define( 'PATH_CURRENT_SITE', '/' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
