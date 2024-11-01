<?php

namespace SFR\Jobs;

use WP_Background_Process;

/**
 * Syncs the legacy data storage in WordPress Custom Post Types into the modern table storage.
 * Use the wp-cron system to schedule jobs.
 * No job will be scheduled if there is nothing to sync.
 */
class LegacyDataSync extends WP_Background_Process {

    protected $prefix = 'sfr_data_sync';
    protected $identifier = 'sfr_data_sync';

    /**
	 * Perform task with queued item.
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param int $post_id Post ID in wp_posts of the request needing sync'd.
	 *
	 * @return mixed
	 */
	protected function task( $post ) {
        (new \SFR\Models\LegacyRequest())->sync( $post );

		return false;
	}

    public static function run() {
        /*
         * Verify this is only running in wp-admin, not ajax or wp-cron
         * Check if there are any CPTs not yet sync'd. They will be marked with a piece of meta
         * Schedule a job to process 10 of the CPTs. It should start in one second
         * At the end of that job run, check again if there are any additional CPTs
         */
        if( !is_admin() ) {
            return;
        }

        add_filter( 'sfr_data_sync_post_args', '\SFR\Jobs\LegacyDataSync::remote_post_args' );
        $legacy_requests = new \SFR\Models\LegacyRequest();
        $job = new \SFR\Jobs\LegacyDataSync();

        $posts = $legacy_requests->not_syncd();

        if( empty( $posts ) ) {
            return;
        }

        foreach( $posts as $post ) {
            $job->push_to_queue( $post );
        }

        $job->save()->dispatch();
    }

    public static function remote_post_args( $args ) {
        $args['timeout'] = 1;
        return $args;
    }


}