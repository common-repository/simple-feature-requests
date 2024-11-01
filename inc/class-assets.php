<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}
/**
 * Setup assets.
 */
class SFR_Assets {
    /**
     * Run class.
     */
    public static function run() {
        add_action( 'wp_enqueue_scripts', array(__CLASS__, 'enqueue_scripts') );
    }

    /**
     * Enqueue scripts.
     */
    public static function enqueue_scripts() {
        if ( is_admin() ) {
            return;
        }
        if ( !SFR_Post_Types::is_type( 'archive' ) && !SFR_Post_Types::is_type( 'single' ) && !sfr_apply_filters( 'sfr_enqueue_scripts', false ) ) {
            return;
        }
        $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );
        wp_enqueue_script(
            'sfr-main',
            SFR_ASSETS_URL . 'frontend/js/main' . $suffix . '.js',
            array('jquery'),
            SFR_VERSION,
            true
        );
        wp_enqueue_style(
            'sfr-main',
            SFR_ASSETS_URL . 'frontend/css/main' . $suffix . '.css',
            array(),
            SFR_VERSION
        );
        wp_localize_script( 'sfr-main', 'sfr_vars', array(
            'nonce'    => wp_create_nonce( 'sfr-nonce' ),
            'ajax_url' => admin_url( 'admin-ajax.php', 'relative' ),
            'paged'    => ( get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1 ),
            'il8n'     => array(
                'vote'   => __( 'Vote', 'simple-feature-requests' ),
                'voting' => __( 'Voting', 'simple-feature-requests' ),
                'voted'  => __( 'Voted', 'simple-feature-requests' ),
            ),
        ) );
    }

}
