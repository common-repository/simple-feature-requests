<?php
/**
 * The Template for displaying the taxonomies.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$taxonomies = isset( $taxonomies ) ? $taxonomies : SFR_Post_Types::get_taxonomies__premium_only();

if ( empty( $taxonomies ) ) {
	return;
}
?>

<?php foreach ( $taxonomies as $key => $taxonomy_args ) { ?>
	<?php if ( empty( $taxonomy_args['show_in_sidebar'] ) || empty( $taxonomy_args['filter']['slug'] ) ) {
		continue;
	}

	$hide_empty = isset( $taxonomy_args['filter']['hide_empty'] ) && $taxonomy_args['filter']['hide_empty'];
	$options    = sfr_get_term_options( $key, $hide_empty );
	$base_url   = sfr_get_archive_url_with_filters( array( $taxonomy_args['filter']['slug'], 'search' ) );
	$selected   = filter_input( INPUT_GET, $taxonomy_args['filter']['slug'] );

	if ( empty( $options ) ) {
		continue;
	} ?>

	<div class="sfr-sidebar-widget sfr-sidebar-widget--<?php echo esc_attr( $key ); ?>">
		<h3 class="sfr-sidebar-widget__title"><?php echo $taxonomy_args['labels']['name']; ?></h3>
		<ul class="sfr-sidebar-widget__list">
			<?php foreach ( $options as $slug => $label ) { ?>
				<?php $url = add_query_arg( $taxonomy_args['filter']['slug'], $slug, $base_url ); ?>
				<li class="sfr-sidebar-widget__list-item <?php if ( $selected === $slug ) {
					echo 'sfr-sidebar-widget__list-item--selected';
				} ?>">
					<a href="<?php echo esc_url( $url ); ?>">
						<?php echo $label; ?>
					</a>
				</li>
			<?php } ?>
		</ul>
	</div>
<?php } ?>

<?php unset( $taxonomies ); ?>
