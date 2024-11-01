<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly.
}
/**
 * Shortcodes.
 */
class SFR_Shortcodes {
    /**
     * Init shortcodes.
     */
    public static function run() {
        if ( is_admin() && !wp_doing_ajax() ) {
            return;
        }
        add_shortcode( 'simple-feature-requests', array(__CLASS__, 'output') );
        add_shortcode( 'simple-feature-requests-sidebar', array(__CLASS__, 'sidebar_output') );
    }

    /**
     * Output archive and single templates.
     *
     * @param array $args Shortcode args.
     *
     * @return string
     */
    public static function output( $args = array() ) {
        $queried_object = get_queried_object();
        if ( !$queried_object ) {
            return '';
        }
        $queried_object_id = ( isset( $queried_object->page_id ) ? $queried_object->page_id : $queried_object->ID );
        if ( $queried_object_id !== SFR_Post_Types::get_archive_page_id() ) {
            return '<p>' . sprintf( __( 'Please select this page (%s) as the <strong>Archive Page</strong> in <a href="%s">the settings</a>, under the "General" tab.', 'simple-feature-requests' ), get_the_title(), admin_url( 'admin.php?page=sfr-settings' ) ) . '</p>';
        }
        $defaults = array(
            'sidebar'    => true,
            'submission' => true,
        );
        $args = wp_parse_args( $args, $defaults );
        $args['sidebar'] = filter_var( $args['sidebar'], FILTER_VALIDATE_BOOLEAN );
        $args['submission'] = filter_var( $args['submission'], FILTER_VALIDATE_BOOLEAN );
        $page_type = SFR_Post_Types::get_page_type();
        // If the page was not found.
        if ( '404' === $page_type ) {
            wp_safe_redirect( get_home_url() );
            // redirect to archive.
            die;
        }
        ob_start();
        if ( 'single' === $page_type ) {
            $args['request_query'] = SFR_Post_Types::get_current_request_query();
            SFR_Template_Hooks::include_template( 'single-feature-request', $args );
        } else {
            global $sfr_requests;
            $sfr_requests = SFR_Query::get_requests();
            SFR_Template_Hooks::include_template( 'archive-feature-requests', $args );
        }
        return ob_get_clean();
    }

    /**
     * Output sidebar.
     */
    public static function sidebar_output() {
        ob_start();
        /**
         * sfr_sidebar hook.
         */
        sfr_do_action( 'sfr_sidebar' );
        return ob_get_clean();
    }

}
