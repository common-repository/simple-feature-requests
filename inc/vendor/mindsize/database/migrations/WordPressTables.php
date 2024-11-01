<?php
namespace SFR\Database\Migrations;

/**
 * Creates tables for WordPress installs
 */
class WordPressTables {

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
        $this->boards();
        $this->requests();
        $this->request_boards();
        $this->request_comments();
        $this->votes();
    }

    private function boards() {
        $table = $this->prefix( 'boards' ); 
        $sql = 'CREATE TABLE ' . $table . ' (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `name` text NOT NULL,
            `description` longtext,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (`id`)
        );'; 
        \maybe_create_table( $table, $sql );
    }

    private function requests() {
        $table = $this->prefix( 'requests' ); 
        $sql = 'CREATE TABLE ' . $table . ' (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `user_id` bigint unsigned NOT NULL,
            `name` text NOT NULL,
            `description` longtext,
            `status` text,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (`id`),
            FOREIGN KEY (user_id) REFERENCES wp_users(ID)
        );'; 
    
        \maybe_create_table( $table, $sql );
    }

    private function request_boards() {
        $table = $this->prefix( 'request_boards' ); 
        $sql = 'CREATE TABLE ' . $table . ' (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `board_id` bigint unsigned DEFAULT NULL,
            `request_id` bigint unsigned NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (`id`),
            FOREIGN KEY (request_id) REFERENCES ' . $this->prefix( 'requests' ) . '(id),
            FOREIGN KEY (board_id) REFERENCES ' . $this->prefix( 'boards' ) . '(id)
        );'; 
    
        \maybe_create_table( $table, $sql );
    }

    private function request_comments() {
        $table = $this->prefix( 'request_comments' ); 
        $sql = 'CREATE TABLE ' . $table . ' (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `request_id` bigint unsigned NOT NULL,
            `user_id` bigint unsigned NOT NULL,
            `comment` longtext NOT NULL,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (`id`),
            FOREIGN KEY (user_id) REFERENCES wp_users(ID),
            FOREIGN KEY (request_id) REFERENCES ' . $this->prefix( 'requests' ) . '(id)
        );'; 
    
        \maybe_create_table( $table, $sql );
    }

    private function votes() {
        // user_id can be blank for guest voting.
        $table = $this->prefix( 'votes' ); 
        $sql = 'CREATE TABLE ' . $table . ' (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `request_id` bigint unsigned NOT NULL,
            `user_id` bigint unsigned,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (`id`),
            FOREIGN KEY (user_id) REFERENCES wp_users(ID),
            FOREIGN KEY (request_id) REFERENCES ' . $this->prefix( 'requests' ) . '(id)
        );'; 
    
        \maybe_create_table( $table, $sql );
    }
}