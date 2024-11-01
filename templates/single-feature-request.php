<?php
/**
 * The Template for displaying a single feature request.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
} ?>

<?php
/**
 * sfr_before_wrapper hook.
 */
sfr_do_action( 'sfr_before_wrapper' );
?>

	<div class="sfr-container">
		<?php
		/**
		 * sfr_before_columns hook.
		 */
		sfr_do_action( 'sfr_before_columns' );
		?>

		<div class="sfr-container__col sfr-container__col--<?php echo ! $sidebar ? 'no-sidebar' : '1'; ?>">
			<?php if ( $request_query->have_posts() ) : ?>
				<?php while ( $request_query->have_posts() ) : $request_query->the_post(); ?>
					<?php
					/**
					 * sfr_before_single_loop hook.
					 *
					 * @hooked SFR_Notices::print_notices() - 10
					 */
					sfr_do_action( 'sfr_before_single_loop' );

					/**
					 * sfr_loop hook.
					 *
					 * @hooked SFR_Template_Hooks::loop_content() - 10
					 */
					sfr_do_action( 'sfr_loop' );

					/**
					 * sfr_after_single_loop hook.
					 *
					 * @hooked SFR_Template_Hooks::comments() - 10
					 */
					sfr_do_action( 'sfr_after_single_loop' );
					?>
				<?php endwhile; ?>
			<?php endif; ?>
		</div>

		<?php if ( $sidebar ) { ?>
			<div class="sfr-container__col sfr-container__col--2">
				<?php
				/**
				 * sfr_sidebar hook.
				 */
				sfr_do_action( 'sfr_sidebar' );
				?>
			</div>
		<?php } ?>

		<?php
		/**
		 * sfr_after_columns hook.
		 */
		sfr_do_action( 'sfr_after_columns' );
		?>
	</div>

<?php
/**
 * sfr_after_wrapper hook.
 */
sfr_do_action( 'sfr_after_wrapper' );
?>