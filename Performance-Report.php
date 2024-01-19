<?php
/*
Plugin Name: Performance Report
Description: Display total sales, monthly sales, and weekly sales with a single shortcode.
Version: 1.0
Author: Konstantinos Kontos
*/

// Include separate files
include_once(plugin_dir_path(__FILE__) . 'sales-functions.php');
include_once(plugin_dir_path(__FILE__) . 'visitor-functions.php');
include_once(plugin_dir_path(__FILE__) . 'shortcodes.php');
include_once(plugin_dir_path(__FILE__) . 'dashboard-widgets.php');

// Hook into WooCommerce to record orders
add_action('woocommerce_new_order', 'record_order');
// Hook into WordPress to track visitors
add_action('init', function () {
    if (is_order_received_page()) {
        $order_id = wc_get_order_id_by_order_key($_GET['key']);
        track_visitor($order_id);
    }
});

?>