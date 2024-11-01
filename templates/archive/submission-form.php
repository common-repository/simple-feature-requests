<?php
/**
 * The Template for displaying the submission form.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $sfr_requests;

$title       = empty( $_POST ) ? filter_input( INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS ) : filter_input( INPUT_POST, 'sfr-submission-title', FILTER_SANITIZE_SPECIAL_CHARS );
$description = filter_input( INPUT_POST, 'sfr-submission-description', FILTER_SANITIZE_SPECIAL_CHARS );
$search      = filter_input( INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS );
$submission  = isset( $submission ) ? $submission : true;
?>

<form class="sfr-form sfr-form--submission" action="" method="post" autocomplete="off">
	<input style="display:none" type="text" name="sfr-ignore-autocomplete" />
	<input style="display:none" type="password" name="sfr-ignore-autocomplete-password" />

	<label for="sfr-input-title" class="sfr-form__row">
		<?php $default_single_name = sfr_apply_filters( 'sfr_single_request_name', 'Request', true ); ?>
		<strong><?php printf( __( 'Your %s', 'simple-feature-requests' ), $default_single_name ); ?></strong>
		<div class="sfr-search-field">
			<?php $default_single_name = sfr_apply_filters('sfr_single_request_name', 'request', false ); ?>
			<input id="sfr-input-title" name="sfr-submission-title" class="sfr-form__field sfr-form__field--input sfr-form__title" type="text" placeholder="<?php printf( __( 'Enter your %s...', 'simple-feature-requests' ), $default_single_name ); ?>" value="<?php echo esc_attr( $title ); ?>" autocomplete="sfr-ac-off">
			<i class="sfr-search-field__icon sfr-search-field__icon--loader"></i>
			<i class="sfr-search-field__icon sfr-search-field__icon--clear sfr-js-clear-search-field" <?php if ( $search ) {
				echo 'style="display: block;"';
			} ?>></i>
		</div>
	</label>

	<?php if ( $submission ) { ?>
		<div class="sfr-form__reveal" <?php if ( $search && $sfr_requests->found_posts <= 0 ) {
			echo 'style="display: block;"';
		} ?>>
			<label for="sfr-input-description" class="sfr-form__row">
				<strong><?php _e( 'Description', 'simple-feature-requests' ); ?></strong>
				<textarea id="sfr-input-description" name="sfr-submission-description" class="sfr-form__field sfr-form__field--textarea"><?php echo $description; ?></textarea>
			</label>

			<?php
			/**
			 * sfr_submission_form hook.
			 *
			 * @hooked SFR_Template_Hooks::login_form_fields() - 20
			 */
			sfr_do_action( 'sfr_submission_form' );
			?>

			<?php wp_nonce_field( 'sfr-submission', 'sfr-submission-nonce' ); ?>
			<button class="sfr-form__button" name="sfr-submission" type="submit"><?php _e( 'Submit', 'simple-feature-requests' ); ?></button>
		</div>
		<div class="sfr-form__choices" <?php if ( ! $search || $sfr_requests->found_posts <= 0 ) {
			echo 'style="display: none;"';
		} ?>>
		<?php $default_single_name = sfr_apply_filters('sfr_single_request_name', 'request', false ); ?>
			<span class="sfr-form__choices-vote"><?php printf( __( 'Vote for an existing %s (%s)', 'simple-feature-requests' ), $default_single_name, sprintf( '<span class="sfr-form__choices-count">%s</span>', $sfr_requests->found_posts ) ); ?></span>
			<span class="sfr-form__choices-or">or</span>
			<a href="#" class="sfr-form__choices-post"><?php echo sprintf( __( 'Post a new %s', 'simple-feature-requests' ), $default_single_name ); ?></a>
		</div>
	<?php } ?>
</form>