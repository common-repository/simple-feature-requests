<?php
/**
 * The Template for displaying the login form.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="sfr-sidebar-widget sfr-sidebar-widget--login">
	<?php if ( ! is_user_logged_in() ) { ?>
		<form class="sfr-form sfr-form--login" action="" method="post">
			<?php
			/**
			 * sfr_before_main_content hook.
			 *
			 * @hooked SFR_Template_Hooks::login_form_fields() - 10
			 */
			sfr_do_action( 'sfr_login_form' );
			?>

			<?php wp_nonce_field( 'sfr-login', 'sfr-login-nonce' ); ?>
			<button class="sfr-form__button" name="sfr-login" type="submit">
				<span class="sfr-js-toggle-register-login"><?php _e( 'Login', 'simple-feature-requests' ); ?></span>
				<span class="sfr-js-toggle-register-login" style="display: none;"><?php _e( 'Register', 'simple-feature-requests' ); ?></span>
			</button>
		</form>
	<?php } else { ?>
		<?php
		global $current_user;
		$user = new SFR_User( $current_user->ID );
		?>
		<p class="sfr-profile">
			<img src="<?php echo get_avatar_url( $current_user->ID, array( 'size' => 52 ) ); ?>" class="sfr-profile__avatar">
			<strong class="sfr-profile__username"><?php printf( __( 'Hey, %s.', 'simple-feature-requests' ), $user->get_username() ); ?></strong>
			<br>
			<a class="sfr-profile__logout" href="<?php echo wp_logout_url( SFR_Post_Types::get_archive_url() ); ?>"><?php _e( 'Logout', 'simple-feature-requests' ); ?></a>
		</p>
	<?php } ?>
</div>