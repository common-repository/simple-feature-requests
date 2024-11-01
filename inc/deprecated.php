<?php

/**
 * Contains functionality to deal with deprecated items that are not yet ready to be completely removed.
 */



/**
 * Backwards compatibility support for deprecated action hooks.
 *
 * @param string $action_name
 * @param mixed ...$args
 * @return void
 */
function sfr_do_action($action_name, ...$args)
{
    $deprecated_action = 'jck_' . $action_name;

    \do_action($action_name, ...$args);

    if (\has_action($deprecated_action)) {
        \_deprecated_hook($deprecated_action, '2.4.0', $action_name);
        \do_action($deprecated_action, ...$args);
    }
}

/**
 * Backwards compatibility support for deprecated filter hooks.
 *
 * @param string $filter_name
 * @param string $value
 * @param mixed ...$args
 * @return mixed
 */
function sfr_apply_filters($filter_name, $value, ...$args)
{
    $deprecated_filter = 'jck_' . $filter_name;

    $value = \apply_filters($filter_name, $value, ...$args);

    if (\has_filter($deprecated_filter)) {
        \_deprecated_hook($deprecated_filter, '2.4.0', $filter_name);
        $value = \apply_filters($deprecated_filter, $value, ...$args);
    }

    return $value;
}

/**
 * Update the database to stop using deprecated naming conventions.
 */
add_action('init', function () {
    if (get_option('sfr_db_names_updated')) {
        return;
    }

    global $wpdb;

    // Remove old transients to prevent fatal errors from renamed objects.
    $wpdb->query("DELETE FROM " . $wpdb->options . " WHERE option_name LIKE '%transient%' AND option_name LIKE '%sfr%'");

    // Fix the naming conventions in the other tables.
    $wpdb->query("UPDATE " . $wpdb->usermeta . " SET meta_key=REPLACE(meta_key, 'jck_', '') WHERE meta_key LIKE '%jck%'");
    $wpdb->query("UPDATE " . $wpdb->postmeta . " SET meta_key=REPLACE(meta_key, 'jck_', '') WHERE meta_key LIKE '%jck%'");
    $wpdb->query("UPDATE " . $wpdb->options . " SET option_name=REPLACE(option_name, 'jck_', '') WHERE option_name LIKE '%jck%'");

    update_option('sfr_db_names_updated', date('Y-m-d'));
}, 1);
