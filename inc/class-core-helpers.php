<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( class_exists( 'SFR_Core_Helpers' ) ) {
	return;
}

/**
 * SFR_Core_Helpers.
 *
 * @class    SFR_Core_Helpers
 */
class SFR_Core_Helpers {
	/**
	 * Woo version compare.
	 *
	 * @param $version
	 * @param $operator
	 *
	 * @return mixed
	 */
	public static function woo_version_compare( $version, $operator ) {
		$woo_version = self::get_woo_version_number();

		return version_compare( $woo_version, $version, $operator );
	}

	/**
	 * Get plugin version.
	 *
	 * @return null
	 */
	public static function get_woo_version_number() {
		static $version = null;

		if ( ! is_null( $version ) ) {
			return $version;
		}

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' );
		$version     = ! empty( $plugin_data['Version'] ) ? $plugin_data['Version'] : null;

		return $version;
	}

	/**
	 * Recursive parse args.
	 *
	 * @param $a
	 * @param $b
	 *
	 * @return array
	 */
	public static function parse_args( &$a, $b ) {
		$a      = (array) $a;
		$b      = (array) $b;
		$result = $b;
		foreach ( $a as $k => &$v ) {
			if ( is_array( $v ) && isset( $result[ $k ] ) ) {
				$result[ $k ] = self::parse_args( $v, $result[ $k ] );
			} else {
				$result[ $k ] = $v;
			}
		}

		return $result;
	}

	/**
	 * Check whether the plugin is active.
	 *
	 * @param string $plugin Base plugin path from plugins directory.
	 *
	 * @return bool True if active.
	 */
	public static function is_plugin_active( $plugin ) {
		$active = in_array( $plugin, (array) get_option( 'active_plugins', array() ) ) || self::is_plugin_active_for_network( $plugin );

		if ( $active ) {
			return true;
		}
		
		return false;
	}

	/**
	 * Check whether the plugin is active for the entire network.
	 *
	 * Only plugins installed in the plugins/ folder can be active.
	 *
	 * Plugins in the mu-plugins/ folder can't be "activated," so this function will
	 * return false for those plugins.
	 *
	 * @since 3.0.0
	 *
	 * @param string $plugin Base plugin path from plugins directory.
	 *
	 * @return bool True, if active for the network, otherwise false.
	 */
	public static function is_plugin_active_for_network( $plugin ) {
		if ( ! is_multisite() ) {
			return false;
		}

		$plugins = get_site_option( 'active_sitewide_plugins' );

		if ( isset( $plugins[ $plugin ] ) ) {
			return true;
		}

		return false;
	}
}
