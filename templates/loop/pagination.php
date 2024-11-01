<?php
/**
 * The template for displaying pagination.
 *
 * @var $sfr_requests WP_Query
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $sfr_requests; ?>

<nav class="sfr-pagination">
	<?php
	if ( $sfr_requests->max_num_pages > 1 ) {
		echo paginate_links( sfr_apply_filters( 'sfr_pagination_args', array(
			'base'      => esc_url_raw( sprintf( '%s%%_%%', SFR_Post_Types::get_archive_url() ) ),
			'add_args'  => isset( $add_args ) ? $add_args : false,
			'current'   => isset( $sfr_requests->query_vars['paged'] ) ? $sfr_requests->query_vars['paged'] : 1,
			'total'     => $sfr_requests->max_num_pages,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'type'      => 'list',
			'end_size'  => 3,
			'mid_size'  => 3,
		), $sfr_requests ) );
	}
	?>
</nav>
