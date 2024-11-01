<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}
/**
 * Template Hooks.
 */
class SFR_Template_Hooks {
    /**
     * Run class.
     */
    public static function run() {
        $action_hooks = array(
            'sfr_before_main_content'  => array(array(
                'function' => array('SFR_Notices', 'print_notices'),
                'priority' => 10,
            ), array(
                'function' => array(__CLASS__, 'submission_form'),
                'priority' => 20,
            ), array(
                'function' => array(__CLASS__, 'filters'),
                'priority' => 30,
            )),
            'sfr_before_single_loop'   => array(array(
                'function' => array('SFR_Notices', 'print_notices'),
                'priority' => 10,
            )),
            'sfr_loop'                 => array(array(
                'function' => array(__CLASS__, 'loop_content'),
                'priority' => 10,
            )),
            'sfr_loop_item_vote_badge' => array(array(
                'function' => array('SFR_Template_Methods', 'loop_item_vote_badge'),
                'priority' => 10,
            )),
            'sfr_loop_item_title'      => array(array(
                'function' => array('SFR_Template_Methods', 'loop_item_title'),
                'priority' => 10,
            )),
            'sfr_loop_item_text'       => array(array(
                'function' => array(__CLASS__, 'loop_item_text'),
                'priority' => 10,
            )),
            'sfr_loop_item_meta'       => array(array(
                'function' => array('SFR_Template_Methods', 'loop_item_status_badge'),
                'priority' => 10,
            ), array(
                'function' => array('SFR_Template_Methods', 'loop_item_author'),
                'priority' => 20,
            ), array(
                'function' => array('SFR_Template_Methods', 'loop_item_comment_count'),
                'priority' => 30,
            )),
            'sfr_loop_item_after_meta' => array(array(
                'function' => array('SFR_Template_Methods', 'comments'),
                'priority' => 10,
            ), array(
                'function' => array(__CLASS__, 'disable_theme_comments'),
                'priority' => 10,
            )),
            'sfr_no_requests_found'    => array(array(
                'function' => array(__CLASS__, 'no_requests_found'),
                'priority' => 10,
            )),
            'sfr_after_main_content'   => array(array(
                'function' => array(__CLASS__, 'pagination'),
                'priority' => 10,
            )),
            'sfr_sidebar'              => array(
                array(
                    'function' => array(__CLASS__, 'back_to_archive_link'),
                    'priority' => 10,
                ),
                array(
                    'function' => array(__CLASS__, 'login'),
                    'priority' => 20,
                ),
                array(
                    'function' => array(__CLASS__, 'top_requests__premium_only'),
                    'priority' => 30,
                ),
                array(
                    'function' => array(__CLASS__, 'taxonomies__premium_only'),
                    'priority' => 40,
                )
            ),
            'sfr_login_form'           => array(array(
                'function' => array(__CLASS__, 'login_form_fields'),
                'priority' => 10,
            )),
            'sfr_submission_form'      => array(array(
                'function' => array(__CLASS__, 'login_form_fields'),
                'priority' => 20,
            )),
        );
        foreach ( $action_hooks as $hook => $actions ) {
            foreach ( $actions as $action ) {
                $defaults = array(
                    'priority' => 10,
                    'args'     => 1,
                );
                $action = wp_parse_args( $action, $defaults );
                if ( !method_exists( $action['function'][0], $action['function'][1] ) ) {
                    continue;
                }
                if ( !has_action( $hook, [$action['function'][0], $action['function'][1]] ) ) {
                    add_action(
                        $hook,
                        $action['function'],
                        $action['priority'],
                        $action['args']
                    );
                }
            }
        }
    }

    /**
     * Include template.
     *
     * @param string $name
     * @param array  $args
     */
    public static function include_template( $name, $args = array() ) {
        $path = sprintf( '%s%s.php', SFR_TEMPLATES_PATH, $name );
        if ( !file_exists( $path ) ) {
            return;
        }
        extract( $args );
        include $path;
    }

    /**
     * Submission form.
     */
    public static function submission_form( $args = array() ) {
        self::include_template( 'archive/submission-form', $args );
    }

    /**
     * Filters.
     */
    public static function filters() {
        self::include_template( 'archive/filters' );
    }

    /**
     * Loop content.
     */
    public static function loop_content() {
        self::include_template( 'loop/content' );
    }

    /**
     * Roadmap loop content.
     */
    public static function roadmap_loop_content() {
        self::include_template( 'loop/roadmap-content' );
    }

    /**
     * Loop item text.
     *
     * @param SFR_Feature_Request $feature_request
     */
    public static function loop_item_text( $feature_request ) {
        if ( $feature_request->is_single() ) {
            the_content();
        } else {
            the_excerpt();
        }
    }

    /**
     * No requests found.
     */
    public static function no_requests_found() {
        self::include_template( 'loop/no-requests-found' );
    }

    /**
     * Pagination.
     */
    public static function pagination() {
        self::include_template( 'loop/pagination' );
    }

    /**
     * Login.
     */
    public static function login() {
        self::include_template( 'sidebar/login' );
    }

    /**
     * Login form fields.
     */
    public static function login_form_fields() {
        if ( is_user_logged_in() ) {
            return;
        }
        self::include_template( 'components/login-form-fields' );
    }

    /**
     * Back to archive link.
     */
    public static function back_to_archive_link() {
        self::include_template( 'sidebar/back-to-archive-link' );
    }

    /**
     * Disable default theme comments.
     */
    public static function disable_theme_comments() {
        add_filter(
            'comments_open',
            function ( $open, $post_id ) {
                if ( SFR_Post_Types::$key === get_post_type( $post_id ) ) {
                    return false;
                }
                return $open;
            },
            20,
            2
        );
    }

    public static function add_photoswipe_template() {
        if ( !function_exists( 'wpsf_get_setting' ) ) {
            return;
        }
        $post_type = get_post_type( get_the_ID() );
        $archive_page = wpsf_get_setting( 'sfr', 'general_setup', 'archive_page_id' );
        $attachments_allowed = wpsf_get_setting( 'sfr', 'general_attachments', 'allow_attachments' );
        if ( is_page( $archive_page ) && $attachments_allowed == 1 || $attachments_allowed == 1 && $post_type == 'cpt_feature_requests' ) {
            self::include_template( 'photoswipe' );
        }
    }

}
