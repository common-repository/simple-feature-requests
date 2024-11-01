<?php
namespace SFR\Models;

class Log extends WpModel {
    /**
     * Columns that can be edited - IE not primary key or timestamps if being used
     */
    protected $fillable = [
        'user_id',
        'level',
        'action',
        'message'
    ];

    public function makeIt($data) {
        if( (!defined( 'WP_DEBUG' ) || !WP_DEBUG) && (!defined('SFR_DEBUG_LOG') || !SFR_DEBUG_LOG)) {
            return;
        }

        $user_id = get_current_user_id();

        if( defined( 'WP_DEBUG_LOG' ) ) {
            $log = 'SFR: ' . $data['level'] . ' | ' . $data['action'] . ' | user_id=' . $user_id . ' | ' . $data['message'];
            error_log( $log );
        }

        \SFR\Models\Log::create([
            'user_id' => $user_id,
            'level' => $data['level'],
            'action' => $data['action'],
            'message' => $data['message']
        ]);
    }

    public static function error($action, $message) {
        (new self)->makeIt([
            'level'     => 'error',
            'action'    => $action,
            'message'   => $message
        ]);
    }
}