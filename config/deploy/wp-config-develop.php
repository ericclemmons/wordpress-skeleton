<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'vagrant');

/** MySQL database password */
define('DB_PASSWORD', 'vagrant');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'e%i;sF31=4c}K{6:jk(Z_Cvw]r+(-kUE7ytk}*M<MonN!B!VXkv;-diPU^1[cI<T');
define('SECURE_AUTH_KEY',  'HUCXDDQsJJ5%:zuOWUi|}@yjIFv?q}DQF@1,u<>4$u3)vSz.ju-1MAU>$h2|T&|E');
define('LOGGED_IN_KEY',    'B&odTF|tq=9_cZ=&wF:|R-5VD!;RgA/rdrwHh-`PVd@1OogAfTkz<0/WH}n<DSN5');
define('NONCE_KEY',        'wsVB9s_8GZu4t6W4%2L#_%3xxtfhUgv-8?aUZt:*Gl<$L[S5D(U0?%-4i|v:~@b+');
define('AUTH_SALT',        '0Vt-?uQJe`-f<9<~$:2ZiW&/8_Z%FWdV46yPQe5bCZg0@JB,)L+N|k{vQ)1V$,Vp');
define('SECURE_AUTH_SALT', 'ppLL/J9Yaq+}-f0(N^rLD7s1v/si]_>^c:Cjb[v;h!B$/3Dq V)zKJ{+-gC#mvJ|');
define('LOGGED_IN_SALT',   'aBW-}vLy_W<b+%:p}5zR6Az8i<&Ifn~>n+oSPhV_|BY{<UG41<9e,KNk@-H4-!b9');
define('NONCE_SALT',       'cq@bRLgNtRiOKq,wwLs_D$k4Qu,B-PL6|Xd#&+|Zx-`U/wG]=Cz8J>8hm3OVh(we');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *æ
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', true);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
