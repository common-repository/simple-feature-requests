<?php
/**
 * The Template for displaying the filters.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$filters = SFR_Template_Methods::get_filters();
$search  = filter_input( INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS );

if ( empty( $filters ) ) {
	return;
}
?>

<ul class="sfr-filters" <?php if ( $search ) { echo 'style="display: none;"'; } ?>>
	<?php foreach ( $filters as $key => $filter ) { ?>
		<?php $filter['key'] = $key; ?>
		<li class="sfr-filters__filter-item sfr-filters__filter-item--<?php echo esc_attr( $key ); ?> sfr-filters__filter-item--<?php esc_attr_e( $filter['type'] ); ?>">
			<?php echo SFR_Template_Methods::get_filter_html( $filter ); ?>
		</li>
	<?php } ?>
</ul>