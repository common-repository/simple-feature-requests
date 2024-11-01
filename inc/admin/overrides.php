<?php

/**
 * Override the order of tabs on the Settings page.
 *
 * @param $settings
 *
 * @return array
 */
function sfr_override_settings( $settings ) {
	if ( ! SFR_Core_Settings::is_settings_page() ) {
		return $settings;
	}

	sort( $settings['tabs'] );

	return $settings;
}

add_filter( 'wpsf_register_settings_sfr', 'sfr_override_settings' );