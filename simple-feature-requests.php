<?php

/**
 * Plugin Name: Simple Feature Requests Pro
 * Plugin URI: https://simplefeaturerequests.com
 * Description: Collect and manage user feedback using your existing WordPress website. Prioritize the product features important to you and your customers.
 * Version: 2.4.2
 * Author: Mindsize
 * Author URI: https://mindsize.com
 * Text Domain: simple-feature-requests
 *
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}
require_once 'vendor/autoload.php';
class Simple_Feature_Requests {
    /**
     * Version
     *
     * @var string
     */
    public static $version = "2.4.2";

    /**
     * Full name
     *
     * @var string
     */
    public $name = 'Simple Feature Requests';

    /**
     * @var null|SFR_Core_Settings
     */
    public $settings = null;

    /**
     * @var null|Freemius
     */
    public $freemius = null;

    /**
     * Class prefix
     *
     * @since  4.5.0
     * @access protected
     * @var string $class_prefix
     */
    protected $class_prefix = "SFR_";

    /**
     * Pro link.
     *
     * @var string
     */
    public static $pro_link = 'https://simplefeaturerequests.com/pricing/?utm_source=SFR&utm_medium=Plugin&utm_campaign=free-nudge';

    /**
     * Construct
     */
    function __construct() {
        $this->define_constants();
        self::load_files();
        $this->load_classes();
        $this->actions();
    }

    /**
     * Define Constants.
     */
    private function define_constants() {
        $this->define( 'SFR_PATH', plugin_dir_path( __FILE__ ) );
        $this->define( 'SFR_URL', plugin_dir_url( __FILE__ ) );
        $this->define( 'SFR_RELPATH', plugin_basename( SFR_PATH ) );
        $this->define( 'SFR_INC_PATH', SFR_PATH . 'inc/' );
        $this->define( 'SFR_VENDOR_PATH', SFR_INC_PATH . 'vendor/' );
        $this->define( 'SFR_TEMPLATES_PATH', SFR_PATH . 'templates/' );
        $this->define( 'SFR_ASSETS_URL', SFR_URL . 'assets/' );
        $this->define( 'SFR_BASENAME', plugin_basename( __FILE__ ) );
        $this->define( 'SFR_VERSION', self::$version );
        $this->define( 'SFR_BOARD_TAXONOMY_NAME', 'request_board' );
        $this->define( 'SFR_ENABLE_DATASTORE', false );
        // @todo remove feature flag after launch of datastore
    }

    public function actions() {
        add_action( 'init', array($this, 'localization') );
        add_filter(
            'sfr_statuses',
            array($this, 'add_custom_statuses'),
            10,
            1
        );
        add_filter(
            'sfr_single_request_name',
            array($this, 'add_custom_labels_single'),
            10,
            2
        );
        add_filter(
            'sfr_plural_request_name',
            array($this, 'add_custom_labels_plural'),
            10,
            2
        );
        add_filter(
            'sfr_status_descriptions',
            array($this, 'add_custom_status_descriptions'),
            10,
            1
        );
        add_filter(
            'sfr_status_colors',
            array($this, 'add_custom_status_colors'),
            10,
            1
        );
    }

    public function localization() {
        load_plugin_textdomain( 'simple-feature-requests', false, SFR_RELPATH . '/languages/' );
    }

    public function add_custom_statuses( $statuses = array() ) {
        $custom_statuses = sfr_get_custom_statuses();
        $custom_statuses = maybe_unserialize( $custom_statuses );
        if ( !empty( $custom_statuses ) && is_array( $custom_statuses ) ) {
            foreach ( $custom_statuses as $status ) {
                $slug = sfr_get_status_slug( $status['status_title'] );
                if ( !array_key_exists( $slug, $statuses ) ) {
                    $statuses[$slug] = $status['status_title'];
                }
            }
        }
        return $statuses;
    }

    public function add_custom_status_descriptions( $descriptions = array() ) {
        $custom_statuses = sfr_get_custom_statuses();
        $custom_statuses = maybe_unserialize( $custom_statuses );
        if ( !empty( $custom_statuses && is_array( $custom_statuses ) ) ) {
            foreach ( $custom_statuses as $status ) {
                $slug = sfr_get_status_slug( $status['status_title'] );
                if ( !array_key_exists( $slug, $descriptions ) ) {
                    $descriptions[$slug] = $status['status_description'];
                }
            }
        }
        return $descriptions;
    }

    public function add_custom_status_colors( $colors = array() ) {
        $custom_statuses = sfr_get_custom_statuses();
        $custom_statuses = maybe_unserialize( $custom_statuses );
        $default_bg_color = sfr_apply_filters( 'sfr_custom_status_default_bg_color', '#d16060' );
        $default_text_color = sfr_apply_filters( 'sfr_custom_status_default_text_color', '#FFFFFF' );
        if ( !empty( $custom_statuses && is_array( $custom_statuses ) ) ) {
            foreach ( $custom_statuses as $status ) {
                $slug = sfr_get_status_slug( $status['status_title'] );
                if ( !array_key_exists( $slug, $colors ) ) {
                    $colors[$slug]['color'] = ( !empty( $status['status_text_color'] ) ? $status['status_text_color'] : $default_text_color );
                    $colors[$slug]['background'] = ( !empty( $status['status_bg_color'] ) ? $status['status_bg_color'] : $default_bg_color );
                }
            }
        }
        return $colors;
    }

    /**
     * Custom labels
     */
    public function add_custom_labels_single( $string, $capitalize ) {
        $custom_label_single = sfr_get_custom_labels( 'single' );
        if ( $capitalize ) {
            return ucfirst( $custom_label_single );
        } else {
            return strtolower( $custom_label_single );
        }
    }

    public function add_custom_labels_plural( $string, $capitalize ) {
        $custom_label_plural = sfr_get_custom_labels( 'plural' );
        if ( $capitalize ) {
            return ucfirst( $custom_label_plural );
        } else {
            return strtolower( $custom_label_plural );
        }
    }

    /**
     * Define constant if not already set.
     *
     * @param string      $name
     * @param string|bool $value
     */
    private function define( $name, $value ) {
        if ( !defined( $name ) ) {
            define( $name, $value );
        }
    }

    /**
     * Load files.
     */
    private static function load_files() {
        require_once SFR_INC_PATH . 'deprecated.php';
        require_once SFR_INC_PATH . 'functions.php';
    }

    /**
     * Load classes
     */
    private function load_classes() {
        global $simple_feature_requests_licence;
        require_once SFR_INC_PATH . 'class-core-autoloader.php';
        SFR_Core_Autoloader::run( array(
            'prefix'   => 'SFR_',
            'inc_path' => SFR_INC_PATH,
        ) );
        ( new \SFR\App() )->run();
        // Activate multisite network integration.
        if ( !defined( 'WP_FS__PRODUCT_1577_MULTISITE' ) ) {
            define( 'WP_FS__PRODUCT_1577_MULTISITE', true );
        }
        $simple_feature_requests_licence = SFR_Core_Licence::run( array(
            'basename' => SFR_BASENAME,
            'urls'     => array(
                'product'  => 'https://www.simplefeaturerequests.com/',
                'settings' => admin_url( 'admin.php?page=sfr-settings' ),
                'account'  => admin_url( 'admin.php?page=sfr-settings-account' ),
            ),
            'paths'    => array(
                'inc'    => SFR_INC_PATH,
                'plugin' => SFR_PATH,
            ),
            'freemius' => array(
                'id'                  => '1577',
                'slug'                => 'simple-feature-requests',
                'type'                => 'plugin',
                'public_key'          => 'pk_021142a45de2c0bcd8dc427adc8f7',
                'is_premium'          => true,
                'is_premium_only'     => false,
                'has_premium_version' => true,
                'has_addons'          => false,
                'has_paid_plans'      => true,
                'menu'                => array(
                    'slug'   => 'sfr-settings',
                    'parent' => false,
                ),
            ),
        ) );
        $this->freemius = $simple_feature_requests_licence::$freemius;
        $this->settings = SFR_Core_Settings::run( array(
            'vendor_path'   => SFR_VENDOR_PATH,
            'title'         => __( 'Simple Feature Requests', 'simple-feature-requests' ),
            'version'       => self::$version,
            'menu_title'    => SFR_Post_Types::get_menu_title(),
            'parent_slug'   => false,
            'capability'    => 'manage_options',
            'settings_path' => SFR_INC_PATH . 'admin/overrides.php',
            'option_group'  => 'sfr',
            'docs'          => array(
                'collection'      => '/collection/134-woocommerce-attribute-swatches',
                'troubleshooting' => '',
                'getting-started' => false,
            ),
        ) );
        SFR_Settings::run();
        SFR_Assets::run();
        SFR_Post_Types::run( $this->settings );
        SFR_Shortcodes::run();
        SFR_AJAX::run();
        SFR_User::run();
        SFR_Submission::run();
        SFR_Query::run();
        SFR_Template_Hooks::run();
        SFR_Factory::run();
        SFR_Notifications::run();
        SFR_Compat_Elementor::run();
        SFR_Compat_Astra::run();
    }

    /**
     * Get pro button.
     *
     * @return string
     */
    public static function get_pro_button() {
        return '<a href="' . esc_url( self::$pro_link ) . '" target="_blank" class="button" style="margin-top: 5px;">' . __( 'Available in Pro', 'simple-feature-requests' ) . '</a>';
    }

}

$simple_feature_requests_class = new Simple_Feature_Requests();