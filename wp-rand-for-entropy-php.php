<?php

/*
 * Plugin Name: wp_rand() For Entropy PHP
 * Description: On some 32bit hosts, the Entropy builds of PHP can truncate ints instead of overflowing as floats. That misbehavior breaks wp_rand(), wp_generate_password(), and Jetpack
 * Plugin URI: http://wordpress.org/extend/plugins/wp-rand-for-entropy-php/
 * Author: Automattic
 * Author URI: http://automattic.com/
 * Version: 0.1
 * License: GPL2+
 */

if ( !function_exists('wp_rand') ) :
/**
 * Generates a random number
 *
 * @since 2.6.2
 *
 * @param int $min Lower limit for the generated number
 * @param int $max Upper limit for the generated number
 * @return int A random number between min and max
 */
function wp_rand( $min = 0, $max = 0 ) {
	global $rnd_value;

	// Reset $rnd_value after 14 uses
	// 32(md5) + 40(sha1) + 40(sha1) / 8 = 14 random numbers from $rnd_value
	if ( strlen($rnd_value) < 8 ) {
		if ( defined( 'WP_SETUP_CONFIG' ) )
			static $seed = '';
		else
			$seed = get_transient('random_seed');
		$rnd_value = md5( uniqid(microtime() . mt_rand(), true ) . $seed );
		$rnd_value .= sha1($rnd_value);
		$rnd_value .= sha1($rnd_value . $seed);
		$seed = md5($seed . $rnd_value);
		if ( ! defined( 'WP_SETUP_CONFIG' ) )
			set_transient('random_seed', $seed);
	}

	// Take the first 8 digits for our value
	$value = substr($rnd_value, 0, 8);

	// Strip the first eight, leaving the remainder for the next call to wp_rand().
	$rnd_value = substr($rnd_value, 8);

	$value = abs(hexdec($value));

	// Some misconfigured 32bit environments (Etropy PHP, for example) truncate integers larger than PHP_INT_MAX to PHP_INT_MAX rather than overflowing them to floats.
	$max_random_number = 3000000000 === 2147483647 ? (float) "4294967295" : 4294967295; // 4294967295 = 0xffffffff

	// Reduce the value to be within the min - max range
	if ( $max != 0 )
		$value = $min + ( $max - $min + 1 ) * $value / ( $max_random_number + 1 );

	return abs(intval($value));
}
endif;
