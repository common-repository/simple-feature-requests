<?php

namespace SFR\Providers;

class JobsProvider extends ServiceProvider {

    public function run() {
            $this->run_in_admin();
    }

    public function run_in_admin() {
        if( !is_admin() ) {
            return;
        }

        if( defined( 'SFR_ENABLE_DATASTORE' ) && SFR_ENABLE_DATASTORE ) {
            add_action( 'init', [ __CLASS__, 'legacy_data_sync' ], 99 );
        } 
    }

    /**
     * Kick off the process to sync the legacy data.
     * 
     * This has to be done via a hook here or the background processor will fatal.
     *
     * @return void
     */
    public static function legacy_data_sync() {
        \SFR\Jobs\LegacyDataSync::run();
    }
}