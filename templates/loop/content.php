<?php
/**
 * The Template for displaying the loop content.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

$feature_request = new SFR_Feature_Request( $post ); 

/**
 * sfr_before_loop_item hook.
 */
sfr_do_action( 'sfr_before_loop_item', $feature_request );
?>

<article <?php $feature_request->wrapper_class(); ?>>
	<?php
	/**
	 * sfr_loop_item_vote_badge hook.
	 *
	 * @hooked SFR_Template_Methods::loop_item_vote_badge() - 10
	 */
	sfr_do_action( 'sfr_loop_item_vote_badge', $feature_request );
	?>

	<div <?php $feature_request->item_class(); ?>>
		<?php
		/**
		 * sfr_loop_item_title hook.
		 *
		 * @hooked SFR_Template_Methods::loop_item_title() - 10
		 */
		sfr_do_action( 'sfr_loop_item_title', $feature_request );
		?>

		<div class="sfr-loop-item__text">
			<?php
			/**
			 * sfr_loop_item_text hook.
			 *
			 * @hooked SFR_Template_Hooks::loop_item_text() - 10
			 */
			sfr_do_action( 'sfr_loop_item_text', $feature_request );
			?>
		</div>

		<?php
		/**
		 * sfr_loop_item_after_text hook.
		 * 
		 * @hooked SFR_Template_Hooks::add_attachments_to_loop__premium_only() - 10
		 */
		sfr_do_action( 'sfr_loop_item_after_text', $feature_request );
		?>

		<div class="sfr-loop-item__meta">
			<?php
			/**
			 * sfr_loop_item_meta hook.
			 *
			 * @hooked SFR_Template_Methods::loop_item_status_badge() - 10
			 * @hooked SFR_Template_Methods::loop_item_author() - 20
			 * @hooked SFR_Template_Methods::loop_item_posted_date() - 30
			 * @hooked SFR_Template_Methods::loop_item_comment_count() - 40
			 * @hooked SFR_Template_Methods::loop_item_attachment_indicator__premium_only() - 50
			 */
			sfr_do_action( 'sfr_loop_item_meta', $feature_request );
			?>
		</div>

		<?php
		/**
		 * sfr_loop_item_after_meta hook.
		 *
		 * @hooked SFR_Template_Methods::comments() - 10
		 */
		sfr_do_action( 'sfr_loop_item_after_meta', $feature_request );
		?>
	</div>
</article>

<?php
/**
 * sfr_after_loop_item hook.
 */
sfr_do_action( 'sfr_after_loop_item', $feature_request );
?>