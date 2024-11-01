=== wp_rand() for Entropy PHP ===
Contributors: mdawaffe, automattic
Tags: wp_rand, wp_generate_password, jetpack, entropy
Requires at least: 3.0
Tested up to: 3.4
Stable tag: trunk

This plugin is not needed as of WordPress 3.5.

The Entropy builds of PHP can truncate ints instead of overflowing as floats. That misbehavior breaks wp_rand(), wp_generate_password(), and Jetpack.

== Description ==

This plugin is not needed as of WordPress 3.5: https://core.trac.wordpress.org/changeset/21685

On some 32bit hosts, the Entropy builds of PHP truncate integers larger than PHP_INT_MAX to PHP_INT_MAX rather than overflowing them as floats.

This can cause `wp_rand()` to return a value outside the requested range.  That unexpected value in turn breaks `wp_generate_password()`, which can have security ramifications.

Of particular note to this plugin's authors, the bug prevents [Jetpack](http://jetpack.me) from functioning.

This plugin works around the bug by redefining the pluggable `wp_rand()` function.  In the redefinition, large integers are expressed as strings and cast to floats, rather than as ints.

== Changelog ==

= 0.1 =
* Initial release.
