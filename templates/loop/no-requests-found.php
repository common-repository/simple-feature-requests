<?php
/**
 * The Template for displaying no requests found message.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<?php $default_plural_name = sfr_apply_filters('sfr_plural_request_name', 'feature requests', false ); ?>
<p class="sfr-no-requests-found"><?php printf( __( 'Sorry, no %s were found.', 'simple-feature-requests' ), $default_plural_name ); ?></p>