<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( class_exists( 'SFR_Core_Settings' ) ) {
	return;
}

/**
 * SFR_Core_Settings.
 *
 * @class    SFR_Core_Settings
 */
class SFR_Core_Settings {
	/**
	 * Single instance of the SFR_Core_Settings object.
	 *
	 * @var SFR_Core_Settings
	 */
	public static $single_instance = null;

	/**
	 * Class args.
	 *
	 * @var array
	 */
	public static $args = array();

	/**
	 * Settings framework instance.
	 *
	 * @var SFR_Settings_Framework
	 */
	public static $settings_framework = null;

	/**
	 * Settings.
	 *
	 * @var array
	 */
	public static $settings = array();

	/**
	 * Simple Feature Request logo svg src.
	 *
	 * @var string
	 */
	public static $sfr_logo_svg = "data:image/svg+xml,%3Csvg xmlns:xlink='http://www.w3.org/1999/xlink' class='h-auto w-full md:mx-0' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 63.38 63.38' width='64' height='64'%3E%3Cg id='Layer_2' data-name='Layer 2'%3E%3Cg id='Layer_1-2' data-name='Layer 1'%3E%3Ccircle class='fill-primary-600' cx='31.69' cy='31.69' r='31.69' fill='%23DC1E54'%3E%3C/circle%3E%3Cpath class='fill-white' d='M34.41,15.7c.1-.33.22-.66.35-1s.15-.37.23-.55c.72-1.57,2-3.07,4-3.05,2.9.07,5.08,2.74,6.63,5,5.1,7.51,8.09,17.77,4.45,25.89-.71,1.57-2,3.12-4,3.11-2.08,0-3.73-1.31-5-2.83a32.43,32.43,0,0,0-8.51-1.05c-.09.79-1.11,6.93-4.81,10.19-.36.33-1.82,1.12-1.52-1.78S25,42.71,21.37,42.89a11.71,11.71,0,0,0-3.21.79H18a1.12,1.12,0,0,1-.26.08c-4.81.83-9-7.92-7.1-13.78.45-1.39,1.31-2.93,2.82-3.17a36.22,36.22,0,0,1,3.73-.95A40,40,0,0,0,34.41,15.7Zm5,1c-1.25,3.6-.61,7.79.39,11.71,1,4.1,2.58,8.38,5.72,11.1A12.28,12.28,0,0,0,46.08,37c.68-6.93-1.51-14.62-5.8-19.46C39.93,17.12,39.48,16.57,39.43,16.71Z' fill='%23FFFFFF'%3E%3C/path%3E%3C/g%3E%3C/g%3E%3C/svg%3E";

	/**
	 * Creates/returns the single instance SFR_Core_Settings object.
	 *
	 * @return SFR_Core_Settings
	 */
	public static function run( $args = array() ) {
		if ( null === self::$single_instance ) {
			self::$args                            = $args;
			self::$args['option_group_underscore'] = str_replace( '-', '_', self::$args['option_group'] );
			self::$single_instance                 = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Construct.
	 */
	private function __construct() {
		add_action( 'init', array( __CLASS__, 'init' ) );
		add_action( 'admin_menu', array( __CLASS__, 'add_settings_page' ), 20 );
		add_action( 'in_admin_header', array( __CLASS__, 'clean_notices' ), 9999 );
		add_filter( 'woocommerce_allow_marketplace_suggestions', '__return_false' );
	}

	/**
	 * Init.
	 */
	public static function init() {
		require_once self::$args['vendor_path'] . 'wp-settings-framework/wp-settings-framework.php';

		add_filter( 'wpsf_register_settings_' . self::$args['option_group'], array( __CLASS__, 'setup_dashboard' ) );

		self::$settings_framework = new SFR_Settings_Framework( self::$args['settings_path'], self::$args['option_group'] );
		self::$settings           = self::$settings_framework->get_settings();
	}

	/**
	 * Get setting.
	 *
	 * @param $setting
	 *
	 * @return mixed
	 */
	public static function get_setting( $setting ) {
		if ( empty( self::$settings ) ) {
			return null;
		}

		if ( ! isset( self::$settings[ $setting ] ) ) {
			return null;
		}

		return self::$settings[ $setting ];
	}

	/**
	 * Add settings page.
	 */
	public static function add_settings_page() {
		$default_title = sprintf( '<div style="padding-bottom: 15px;"><img width="24" height="28" style="display: inline-block; vertical-align: text-bottom; margin: 0 8px 0 0" src="%s"> %s by <a href="https://mindsize.com/?utm_source=SFR&utm_medium=Plugin&utm_campaign=simple-feature-requests&utm_content=settings-title" target="_blank">Mindsize</a> <em style="opacity: 0.6; font-size: 80%%;">(v%s)</em></div>', esc_attr( self::$sfr_logo_svg ), self::$args['title'], self::$args['version'] );

		self::$settings_framework->add_settings_page( array(
			'parent_slug' => isset( self::$args['parent_slug'] ) ? self::$args['parent_slug'] : 'woocommerce',
			'page_title'  => isset( self::$args['page_title'] ) ? self::$args['page_title'] : $default_title,
			'menu_title'  => self::$args['menu_title'],
			'capability'  => self::get_settings_page_capability(),
		) );

		do_action( 'admin_menu_' . self::$args['option_group'] );
	}

	/**
	 * Get settings page capability.
	 *
	 * @return mixed
	 */
	public static function get_settings_page_capability() {
		$capability = isset( self::$args['capability'] ) ? self::$args['capability'] : 'manage_woocommerce';

		return apply_filters( self::$args['option_group'] . '_settings_page_capability', $capability );
	}

	/**
	 * Is settings page?
	 *
	 * @param string $suffix
	 *
	 * @return bool
	 */
	public static function is_settings_page( $suffix = '' ) {
		if ( ! is_admin() ) {
			return false;
		}

		$path = str_replace( '_', '-', self::$args['option_group'] ) . '-settings' . $suffix;

		if ( empty( $_GET['page'] ) || $_GET['page'] !== $path ) {
			return false;
		}

		return true;
	}
	
	/**
	 * Removes all notices from the admin page. This includes the standard WordPress upgrade notices, as well as notices set by other plugins.
	 * 
	 * As a side effect, notices this plugin produces only on this page will be removed too. Of which, Freemius does have one it supplies.
	 */
	public static function clean_notices() {
		if ( ! self::is_settings_page() && ! self::is_settings_page( '-account' ) ) {
			return;
		}

		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );
	}

	/**
	 * Get doc links.
	 *
	 * @return array
	 */
	public static function get_doc_links() {
		$transient_name = self::$args['option_group'] . '_getting_started_links';
		$saved_return   = get_transient( $transient_name );

		if ( false !== $saved_return ) {
			return $saved_return;
		}

		$return = array();
		$url    = self::get_docs_url( 'getting-started' );
		$html   = file_get_contents( $url );

		if ( ! $html ) {
			set_transient( $transient_name, $return, 12 * HOUR_IN_SECONDS );

			return $return;
		}

		$dom = new DOMDocument();

		@$dom->loadHTML( $html );

		$lists = $dom->getElementsByTagName( 'ul' );

		if ( empty( $lists ) ) {
			set_transient( $transient_name, $return, 12 * HOUR_IN_SECONDS );

			return $return;
		}

		foreach ( $lists as $list ) {
			$classes = $list->getAttribute( 'class' );

			if ( strpos( $classes, 'articleList' ) === false ) {
				continue;
			}

			$links = $list->getElementsByTagName( 'a' );

			foreach ( $links as $link ) {
				$return[] = array(
					'href'  => $link->getAttribute( 'href' ),
					'title' => $link->nodeValue,
				);
			}
		}

		set_transient( $transient_name, $return, 30 * DAY_IN_SECONDS );

		return $return;
	}

	/**
	 * Get docs URL.
	 *
	 * @param bool $type
	 *
	 * @return mixed|string
	 */
	public static function get_docs_url( $type = false ) {
		return SFR_Settings::documentation_link();
	}

	/**
	 * Configure settings dashboard.
	 *
	 * @param $settings
	 *
	 * @return mixed
	 */
	public static function setup_dashboard( $settings ) {
		if ( ! self::is_settings_page() ) {
			return $settings;
		}

		$settings['tabs']     = isset( $settings['tabs'] ) ? $settings['tabs'] : array();
		$settings['sections'] = isset( $settings['sections'] ) ? $settings['sections'] : array();

		$settings['tabs'][] = array(
			'id'    => 'dashboard',
			'title' => __( 'Dashboard', 'simple-feature-requests' ),
		);

		if ( current_user_can( 'manage_options' ) ) {
			$settings['sections']['licence'] = array(
				'tab_id'              => 'dashboard',
				'section_id'          => 'general',
				'section_title'       => __( 'License &amp; Account Settings', 'simple-feature-requests' ),
				'section_description' => '',
				'section_order'       => 10,
				'fields'              => array(
					array(
						'id'       => 'licence',
						'title'    => __( 'License &amp; Billing', 'simple-feature-requests' ),
						'subtitle' => __( 'Activate or sync your license, cancel your subscription, print invoices, and manage your account information.', 'simple-feature-requests' ),
						'type'     => 'custom',
						'default'  => SFR_Core_Licence::admin_account_link(),
					),
				),

			);
		}

		$settings['sections']['support'] = array(
			'tab_id'              => 'dashboard',
			'section_id'          => 'support',
			'section_title'       => __( 'Support', 'simple-feature-requests' ),
			'section_description' => '',
			'section_order'       => 30,
			'fields'              => array(
				array(
					'id'       => 'support',
					'title'    => __( 'Support', 'simple-feature-requests' ),
					'subtitle' => __( 'Get premium support with a valid license.', 'simple-feature-requests' ),
					'type'     => 'custom',
					'default'  => SFR_Settings::support_link(),
				),
				array(
					'id'       => 'documentation',
					'title'    => __( 'Documentation', 'simple-feature-requests' ),
					'subtitle' => __( 'Read the plugin documentation.', 'simple-feature-requests' ),
					'type'     => 'custom',
					'default'  => SFR_Settings::documentation_link(),
				),
			),
		);

		return $settings;
	}
}
