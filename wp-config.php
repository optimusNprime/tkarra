<?php

// BEGIN iThemes Security - Ne modifiez pas ou ne supprimez pas cette ligne
// iThemes Security Config Details: 2
define( 'DISALLOW_FILE_EDIT', true ); // Désactivez l’éditeur de code - iThemes Security > Réglages > Ajustements WordPress > Éditeur de code
// END iThemes Security - Ne modifiez pas ou ne supprimez pas cette ligne

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'tkarra');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
/** Leave blank password on dev WIN **/
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         '8-r kM9~>SV:T[D#O$yt;UK^iO(;_nB>Elt?:g(*d%r6TvKY)$)P.qQW{bEx]>_j');
define('SECURE_AUTH_KEY',  '>`UTbesPh`E8q(ua:PI<_I3|Xy}PK.fD}:WRK36^Q`>|1dSrd]A,fEg <`Rky`1|');
define('LOGGED_IN_KEY',    '&|nLSLR!z1Gi8h2@J[719~-z>SmL0v]A&QB(@@*/TiQPPja6;Z x<<bph s(]C2z');
define('NONCE_KEY',        '}#iq_b=(<m;_o >r&63nNlXd!`,DLx>mY1q[A+ZgF|$`>;}J<BIZk) !I=.jsKRK');
define('AUTH_SALT',        'JmO|dwd28d%9;jluBj(g:xFE{T,2JyPw#BQ1#?HN;Zv0|dgINPxvD;6 )9x]*y[N');
define('SECURE_AUTH_SALT', 'N*cl4qD4&8aYif9r+e+_slRsu?Hi< axT%8g*E%^B:9Y*~|{h{6,ZSVm{0h~QaE5');
define('LOGGED_IN_SALT',   '##WEH)yG=E?:tHf]Yvcw:wbr>,mwTB`aOt*-{bIF1=o_2GvNs^}`x0%xK= U$#_#');
define('NONCE_SALT',       '9_U77Q)SHgjK84id,M>Sz)MKaE%o?I5$.~v,@?RM*Kf(G,`Wj0D}/b<)fwf##~?b');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'ic_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

// Remove contact form to use js and ajax, fixes the errors on safari and iOs
// define ('WPCF7_LOAD_JS', false);

/* That's all, stop editing! Happy blogging. */

define('FORCE_SSL_ADMIN', true);

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
