<?php
/**
 * The Template for displaying feature request archives.
 *
 * @var WP_Query $sfr_requests
 * @var bool     $sidebar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $sfr_requests; ?>

<?php
/**
 * sfr_before_wrapper hook.
 */
sfr_do_action( 'sfr_before_wrapper', $args );
?>

	<div class="sfr-container">
		<?php
		/**
		 * sfr_before_columns hook.
		 */
		sfr_do_action( 'sfr_before_columns', $args );
		?>

		<div class="sfr-container__col sfr-container__col--<?php echo ! $sidebar ? 'no-sidebar' : '1'; ?>">
			<?php
			/**
			 * sfr_before_main_content hook.
			 *
			 * @hooked SFR_Notices::print_notices() - 10
			 * @hooked SFR_Template_Hooks::submission_form() - 20
			 * @hooked SFR_Template_Hooks::filters() - 30
			 */
			sfr_do_action( 'sfr_before_main_content', $args );
			?>

			<div class="sfr-content">
				<?php if ( $sfr_requests->have_posts() ) : ?>
					<?php while ( $sfr_requests->have_posts() ) : $sfr_requests->the_post(); ?>
						<?php
						/**
						 * sfr_loop hook.
						 *
						 * @hooked SFR_Template_Hooks::loop_content() - 10
						 */
						sfr_do_action( 'sfr_loop', $args );
						?>
					<?php endwhile;
					wp_reset_postdata(); ?>
				<?php else: ?>

					<?php
					/**
					 * sfr_no_requests_found hook.
					 *
					 * @hooked SFR_Template_Hooks::no_requests_found() - 10
					 */
					sfr_do_action( 'sfr_no_requests_found', $args );
					?>

				<?php endif; ?>
			</div>

			<?php
			/**
			 * sfr_after_main_content hook.
			 *
			 * @hooked SFR_Template_Hooks::pagination() - 10
			 */
			sfr_do_action( 'sfr_after_main_content', $args );
			?>
		</div>

		<?php if ( $sidebar ) { ?>
			<div class="sfr-container__col sfr-container__col--2">
				<?php
				/**
				 * sfr_sidebar hook.
				 */
				sfr_do_action( 'sfr_sidebar', $args );
				?>
			</div>
		<?php } ?>

		<?php
		/**
		 * sfr_after_columns hook.
		 */
		sfr_do_action( 'sfr_after_columns', $args );
		?>
	</div>

<?php
/**
 * sfr_after_wrapper hook.
 */
sfr_do_action( 'sfr_after_wrapper', $args );
?>