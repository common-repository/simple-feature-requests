<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}
/**
 * Setup submission methods.
 */
class SFR_Submission {
    /**
     * Run class.
     */
    public static function run() {
        add_action( 'template_redirect', array(__CLASS__, 'handle_submission'), 10 );
    }

    /**
     * Handle feature request submission.
     */
    public static function handle_submission() {
        if ( !isset( $_POST['sfr-submission'] ) ) {
            return;
        }
        $notices = SFR_Notices::instance();
        $nonce = filter_input( INPUT_POST, 'sfr-submission-nonce', FILTER_SANITIZE_SPECIAL_CHARS );
        $title = self::trim_if_string( filter_input( INPUT_POST, 'sfr-submission-title', FILTER_SANITIZE_SPECIAL_CHARS ) );
        $description = self::trim_if_string( filter_input( INPUT_POST, 'sfr-submission-description', FILTER_SANITIZE_SPECIAL_CHARS ) );
        $username = self::trim_if_string( filter_input( INPUT_POST, 'sfr-login-username', FILTER_SANITIZE_SPECIAL_CHARS ) );
        $email = self::trim_if_string( filter_input( INPUT_POST, 'sfr-login-email', FILTER_SANITIZE_SPECIAL_CHARS ) );
        $password = self::trim_if_string( filter_input( INPUT_POST, 'sfr-login-password', FILTER_SANITIZE_SPECIAL_CHARS ) );
        $repeat_password = self::trim_if_string( filter_input( INPUT_POST, 'sfr-login-repeat-password', FILTER_SANITIZE_SPECIAL_CHARS ) );
        $user_type = self::trim_if_string( filter_input( INPUT_POST, 'sfr-login-user-type', FILTER_SANITIZE_SPECIAL_CHARS ) );
        if ( !wp_verify_nonce( $nonce, 'sfr-submission' ) ) {
            $notices->add( __( 'There was an error submitting your request.', 'simple-feature-requests' ), 'error' );
        }
        if ( empty( $title ) ) {
            $notices->add( __( 'Please enter a request title.', 'simple-feature-requests' ), 'error' );
        }
        if ( empty( $description ) ) {
            $notices->add( __( 'Please enter a request description.', 'simple-feature-requests' ), 'error' );
        }
        if ( $user_type === 'register' ) {
            $user = SFR_User::register(
                $username,
                $email,
                $password,
                $repeat_password
            );
        } else {
            $user = SFR_User::login( $email, $password );
        }
        sfr_do_action( 'sfr_submission_notices', $notices );
        if ( $notices->has_notices() ) {
            return;
        }
        $args = sfr_apply_filters( 'sfr_submission_args', array(
            'title'       => $title,
            'description' => $description,
            'user'        => $user,
        ) );
        if ( isset( $_GET['board'] ) ) {
            if ( !empty( $_GET['board'] ) ) {
                $board = sanitize_text_field( $_GET['board'] );
                if ( $term = get_term_by( 'slug', $board, SFR_BOARD_TAXONOMY_NAME ) ) {
                    $args['board'] = $board;
                }
            }
        }
        $request_id = SFR_Factory::create( $args );
        if ( !$request_id ) {
            $notices->add( __( 'Sorry, there was an issue adding that request. Please try again.', 'simple-feature-requests' ), 'error' );
            return;
        }
        wp_safe_redirect( get_permalink( $request_id ), 302 );
        exit;
    }

    public static function trim_if_string( $value ) {
        return ( is_string( $value ) ? trim( $value ) : '' );
    }

}
