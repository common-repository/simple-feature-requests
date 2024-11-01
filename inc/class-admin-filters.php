<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}
class SFR_Admin_Filters {
    public static function run() {
        if ( !is_admin() || empty( $_GET['post_type'] ) || 'cpt_feature_requests' != $_GET['post_type'] ) {
            return;
        }
        add_action( 'restrict_manage_posts', array(__CLASS__, 'add_admin_filters') );
        add_filter( 'pre_get_posts', array(__CLASS__, 'parse_admin_query_status') );
        add_filter( 'pre_get_posts', array(__CLASS__, 'parse_admin_query_date_range') );
        add_action( 'admin_enqueue_scripts', array(__CLASS__, 'admin_scripts_date_range') );
        add_action( 'admin_head', array(__CLASS__, 'admin_head_date_range') );
        add_filter( 'months_dropdown_results', array(__CLASS__, 'months_dropdown_remove_it') );
    }

    public static function add_admin_filters( $post_type ) {
        global $simple_feature_requests_class;
        echo '<div id="request-filters" class="sfr-admin-filters ">';
        echo '<h2 style="padding-bottom: 10px">Request Filters</h2>';
        if ( $simple_feature_requests_class->freemius->can_use_premium_code() ) {
            self::add_admin_filter_category__premium_only( $post_type );
        }
        self::add_admin_filter_status( $post_type );
        echo '<span class="sfr-admin-filters-date">';
        self::add_admin_filter_date_range( $post_type );
        echo '</span>';
        echo '</div>';
    }

    /**
     * Render the filter to the admin Requests page for request Status
     *
     * @param string $post_type
     * @return void
     */
    public static function add_admin_filter_status( $post_type ) {
        global $wpdb;
        $status_meta_key = 'sfr_status';
        $selected_status = filter_input( INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS );
        $result = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT pm.meta_value\n\t\t\t\tFROM {$wpdb->postmeta} pm\n\t\t\t\tLEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id\n\t\t\t\tWHERE pm.meta_key = '%s'\n\t\t\t\tORDER BY pm.meta_value", $status_meta_key ) );
        echo '<select id="status" name="status">';
        echo '<option value="0">' . __( 'Show all Statuses', 'simple-feature-requests' ) . ' </option>';
        foreach ( $result as $status ) {
            echo '<option value="' . $status . '"' . selected( $status, $selected_status ) . '>' . ucfirst( $status ) . ' </option>';
        }
        echo '</select>';
    }

    /**
     * Filter the admin Requests page query and add the Status.
     *
     * @param WP_Query $query
     * @return void
     */
    public static function parse_admin_query_status( $query ) {
        // Make sure we are on the feature requests admin page
        if ( !$query->is_main_query() || 'cpt_feature_requests' !== $query->query['post_type'] ) {
            return $query;
        }
        $status = filter_input( INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS );
        // Maybe filter by request status
        if ( !empty( $status ) && 0 != $status ) {
            $meta_query = (array) $query->get( 'meta_query' );
            $meta_query[] = array(
                'key'     => 'sfr_status',
                'value'   => $status,
                'compare' => '=',
            );
            $query->set( 'meta_query', $meta_query );
        }
        return $query;
    }

    /**
     * Filter the admin Requests page query with a date range.
     *
     * @param WP_Query $query
     * @return void
     */
    public static function parse_admin_query_date_range( $query ) {
        // Make sure we are on the feature requests admin page
        if ( !$query->is_main_query() || 'cpt_feature_requests' !== $query->query['post_type'] ) {
            return $query;
        }
        $start_date = filter_input( INPUT_GET, 'date-start', FILTER_SANITIZE_SPECIAL_CHARS );
        $end_date = filter_input( INPUT_GET, 'date-end', FILTER_SANITIZE_SPECIAL_CHARS );
        $date_query = array(
            'inclusive' => true,
        );
        if ( !empty( $start_date ) && DateTime::createFromFormat( 'Y-m-d', $start_date ) ) {
            $date_query['after'] = $start_date;
        }
        if ( !empty( $end_date ) && DateTime::createFromFormat( 'Y-m-d', $end_date ) ) {
            $date_query['before'] = $end_date;
        }
        $dates = (array) $query->get( 'date_query' );
        $dates[] = $date_query;
        $query->set( 'date_query', $dates );
        return $query;
    }

    /**
     * Render the filter to the admin Requests page for the date picker 
     *
     * @param string $post_type
     * @return void
     */
    public static function add_admin_filter_date_range( $post_type ) {
        $start_date = filter_input( INPUT_GET, 'date-start', FILTER_SANITIZE_SPECIAL_CHARS );
        $end_date = filter_input( INPUT_GET, 'date-end', FILTER_SANITIZE_SPECIAL_CHARS );
        $start = $end = '';
        if ( !empty( $start_date ) && DateTime::createFromFormat( 'Y-m-d', $start_date ) ) {
            $start = $start_date;
        }
        if ( !empty( $end_date ) && DateTime::createFromFormat( 'Y-m-d', $end_date ) ) {
            $end = $end_date;
        }
        echo '<strong>Date Range: </strong>';
        echo '<input type="text" class="request-datepicker" size="10" name="date-start" placeholder="Start Date" value="' . $start . '" />';
        echo '<input type="text" class="request-datepicker" size="10" name="date-end" placeholder="End Date" value="' . $end . '" />';
    }

    public static function admin_scripts_date_range() {
        wp_enqueue_script( 'jquery-ui-datepicker' );
    }

    public static function admin_head_date_range() {
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                jQuery('.request-datepicker').datepicker({
                    dateFormat: 'yy-mm-dd'
                });
            });
        </script>

        <style>
            .tablenav .actions {
                width: 100% !important;
            }

            .sfr-admin-filters {
                margin: 10px 25px;
                padding: 25px;
                background-color: #fefefe;
                border: 1px solid #333;
                border-radius: 5px;

            }

            .sfr-admin-filters-date {
                border: 1px solid #333;
                border-radius: 5px;
                padding: 10px;
                background-color: #f9f9f9;
            }

            .sfr-filter-full-width {
                width: 100vh;
            }

            #post-query-submit {
                margin-bottom: 10px;
            }

            .ui-datepicker {
                background: #fefefe;
                border: 1px solid #ca3656;
            }

            .ui-datepicker-header {
                background-color: #ca3656 !important;
                border: none;
                color: #333;
                font-weight: bold;
                text-align: center;
                padding-top: 10px;
            }

            .ui-datepicker-header a {
                color: #f2f2f2;
            }

            .ui-datepicker-header .ui-corner-all {
                padding-left: 5px;
                padding-right: 5px;
            }

            .ui-datepicker-title {
                color: #f2f2f2;
                align: center;
                text-align: center;
                padding: 10px;
            }

            .ui-datepicker-calendar .ui-state-active {
                background-color: #337ab7;
                color: #fff;
            }

            .ui-datepicker-week-end {
                color: red;
            }

            .ui-datepicker-calendar tbody td:hover {
                background-color: #f5f5f5;
                cursor: pointer;
            }
        </style>
<?php 
    }

    public static function months_dropdown_remove_it( $months ) {
        return array();
    }

}
