<?php
namespace SFR\Database\Migrations;

/**
 * Creates tables for WordPress installs
 */
class LogTable {

    /**
     * The WordPress table name prefix. Commonly 'wp_'
     *
     * @var string
     */
    private $wp_prefix;

    public static function run() {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        
        (new self())->create();
    }

    private function prefix( $table ) {
        if ( empty ( $this->wp_prefix ) ) {
            global $wpdb;
            $this->wp_prefix = $wpdb->prefix . 'sfr_';
        }
        return $this->wp_prefix . $table;
    }

    public function create() {
        $table = $this->prefix( 'logs' ); 
        $sql = 'CREATE TABLE ' . $table . ' (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `user_id` bigint unsigned,
            `level` text,
            `action` text,
            `message` longtext,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (`id`)
        );'; 
        \maybe_create_table( $table, $sql );
    }
}