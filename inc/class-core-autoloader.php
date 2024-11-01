<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( class_exists( 'SFR_Core_Autoloader' ) ) {
	return;
}

/**
 * SFR_Core_Autoloader.
 *
 * @class    SFR_Core_Autoloader
 */
class SFR_Core_Autoloader {
	/**
	 * Single instance of the SFR_Core_Autoloader object.
	 *
	 * @var SFR_Core_Autoloader
	 */
	public static $single_instance = null;

	/**
	 * Class args.
	 *
	 * @var array
	 */
	public static $args = array();

	/**
	 * Creates/returns the single instance SFR_Core_Autoloader object.
	 *
	 * @return SFR_Core_Autoloader
	 */
	public static function run( $args = array() ) {
		if ( null === self::$single_instance ) {
			self::$args            = $args;
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Construct.
	 */
	private function __construct() {
		spl_autoload_register( array( __CLASS__, 'autoload' ) );
	}

	/**
	 * Autoloader
	 *
	 * Classes should reside within /inc and follow the format of
	 */
	private static function autoload( $class_name ) {
		/**
		 * If the class being requested does not start with our prefix,
		 * we know it's not one in our project
		 */
		if ( 0 !== strpos( $class_name, self::$args['prefix'] ) ) {
			return;
		}

		$file_name = strtolower( str_replace(
			array( self::$args['prefix'], '_' ), // Prefix | Underscores
			array( '', '-' ),                    // Remove | Replace with hyphens
			$class_name
		) );

		$file = self::$args['inc_path'] . 'class-' . $file_name . '.php';

		// Include found file.
		if ( file_exists( $file ) ) {
			require( $file );

			return;
		}
	}
}
