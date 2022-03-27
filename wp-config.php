<?php
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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '4x8xLfZ7uAa1iZKdZhsWYVudrWVKWXzQSrgMyopY8VSmu63YS2RAbfMThd2xlV3cSXx2dI5plgKXgiPqnnmurQ==');
define('SECURE_AUTH_KEY',  'QH539XsKTaoCVq9Oz1DVLTvZPvsWx+jSKBXw6pANtGN3pU08mwQPlUZlOUSZJj0boh78YM7u1v61V0TmwuR0/A==');
define('LOGGED_IN_KEY',    'lBV7SV21beQIs2rVDe1uqxeKm94yd3b1UEyO8Pl3unpkDS3nMWtAlozCfhiRgncZ4njtwH8qimWL8hDq3ZMgjw==');
define('NONCE_KEY',        'KCYv0/fcydQAoNhdNfOfIlFO+EdAqIcoy07R7TJAy9ccgXje558c5IxppdEKmU2PohSDvrUDXhlJoSf8r/UoAA==');
define('AUTH_SALT',        'PLH+35xY9bDz7d6co5LZTkXGCY7ZEpoRfRPCqiTZP+iSRBx5RaCPR1+VQKzvWY8yAYyQxZqTrvQ6LPH+0XomTg==');
define('SECURE_AUTH_SALT', 'YymPMOfpioCeaCR+zGLTlYqrGDlWHFbWPfPl+xioeU3HYYZlz+ka2mu3Yp5f4XFM60f9neaSZqhMY1jm6pIcfQ==');
define('LOGGED_IN_SALT',   'Mbd30ZLgkySFPo73s+x7uktAF7gIj1XHzYhtVooIBd7KJW/9EuQmWLWXjzZKnWx8Te9uYmdKwZKVwMuLVd5Bng==');
define('NONCE_SALT',       'sh8on9lGc9z6JlUaC42jawFlPfIzj07oTBfQ5ri/h/YhRQsHmjvRV8frxp0+uk+QnmK9F4i2rfsr1PoUo6maSg==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

define('JWT_AUTH_SECRET_KEY', 'eyJhbGciOiJIUzI1NiJ9.eyJSb2xlIjoiQWRtaW4iLCJJc3N1ZXIiOiJJc3N1ZXIiLCJVc2VybmFtZSI6IkphdmFJblVzZSIsImV4cCI6MTY0NzUwODgzNiwiaWF0IjoxNjQ3NTA4ODM2fQ.HKK3gUccLYGMsY_C5zZVx4GYf80KRWJWtzwByJc53j4');
define('JWT_AUTH_CORS_ENABLE', true);



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
