<?php
/**
 * The Template for displaying the login form fields.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$username = filter_input( INPUT_POST, 'sfr-login-username', FILTER_SANITIZE_SPECIAL_CHARS );
$email    = filter_input( INPUT_POST, 'sfr-login-email', FILTER_SANITIZE_SPECIAL_CHARS );
?>

<p>
	<span class="sfr-js-toggle-register-login">
		<?php _e( "Don't have an account?", 'simple-feature-requests' ); ?>
		<a href="javascript: void( 0 );" data-sfr-toggle="register-login" data-sfr-toggle-submission-user-type="register">
			<?php _e( "Register", 'simple-feature-requests' ); ?>
		</a>
	</span>
	<span class="sfr-js-toggle-register-login" style="display: none;">
		<?php _e( "Already have an account?", 'simple-feature-requests' ); ?>
		<a href="javascript: void( 0 );" data-sfr-toggle="register-login" data-sfr-toggle-submission-user-type="login">
			<?php _e( "Login", 'simple-feature-requests' ); ?>
		</a>
	</span>
</p>

<label class="sfr-form__row sfr-js-toggle-register-login" style="display: none;">
	<strong><?php _e( 'Username', 'simple-feature-requests' ); ?></strong>
	<input name="sfr-login-username" class="sfr-form__field sfr-form__field--input" type="text" value="<?php echo esc_attr( $username ); ?>">
</label>

<label class="sfr-form__row">
	<strong><?php _e( 'Email', 'simple-feature-requests' ); ?><span class="sfr-js-toggle-register-login"> / <?php _e( 'Username', 'simple-feature-requests' ); ?></span></strong>
	<input name="sfr-login-email" class="sfr-form__field sfr-form__field--input" type="text" value="<?php echo esc_attr( $email ); ?>">
</label>

<label class="sfr-form__row">
	<strong><?php _e( 'Password', 'simple-feature-requests' ); ?></strong>
	<input name="sfr-login-password" class="sfr-form__field sfr-form__field--input" type="password">
</label>

<label class="sfr-form__row sfr-js-toggle-register-login" style="display: none;">
	<strong><?php _e( 'Repeat Password', 'simple-feature-requests' ); ?></strong>
	<input name="sfr-login-repeat-password" class="sfr-form__field sfr-form__field--input" type="password">
</label>

<input type="hidden" name="sfr-login-user-type" value="login">