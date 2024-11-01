<?php
/**
 * The Template for displaying the back to archive link.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! SFR_Post_Types::is_type( 'single' ) ) {
	return;
}
?>

<div class="sfr-sidebar-widget sfr-sidebar-widget--back">
	<?php SFR_Template_Methods::back_to_archive_link(); ?>
</div>
