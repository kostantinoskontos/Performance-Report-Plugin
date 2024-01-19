<?php
// Helper function to calculate sales in a specific date range
function calculate_sales_in_range($start_date, $end_date) {
    $orders = wc_get_orders(array(
        'date_created' => '>=' . $start_date,
        'date_created' => '<=' . $end_date,
        'status' => 'completed', // Adjust status as needed
    ));

    $total_sales = 0;

    foreach ($orders as $order) {
        $total_sales += $order->get_total();
    }

    return $total_sales;
}

// Helper function to calculate total sales
function calculate_total_sales() {
    $orders = wc_get_orders(array('status' => 'completed'));

    $total_sales = 0;

    foreach ($orders as $order) {
        $total_sales += $order->get_total();
    }

    return $total_sales;
}

// Shortcode function to display sales information
function display_sales_information() {
    // Get total sales
    $total_sales = calculate_total_sales();

    // Get monthly sales
    $start_of_month = strtotime('first day of this month midnight');
    $end_of_month   = strtotime('last day of this month 23:59:59');
    $monthly_sales = calculate_sales_in_range($start_of_month, $end_of_month);

    // Get weekly sales
    $start_of_week = strtotime('last monday midnight');
    $end_of_week   = strtotime('next sunday 23:59:59');
    $weekly_sales = calculate_sales_in_range($start_of_week, $end_of_week);

    // Output the sales information
    echo '<p>Total Sales: ' . wc_price($total_sales) . '</p>';
    echo '<p>Monthly Sales: ' . wc_price($monthly_sales) . '</p>';
    echo '<p>Weekly Sales: ' . wc_price($weekly_sales) . '</p>';
}

function record_order() {
    // Increase the total order count
    $total_order_count = get_option('website_total_order_count', 0);
    update_option('website_total_order_count', $total_order_count + 1);

    // Increase the monthly order count
    $monthly_order_count = get_option('website_monthly_order_count', 0);
    update_option('website_monthly_order_count', $monthly_order_count + 1);

    // Increase the weekly order count
    $weekly_order_count = get_option('website_weekly_order_count', 0);
    update_option('website_weekly_order_count', $weekly_order_count + 1);
}

function get_total_order_count() {
    return get_option('website_total_order_count', 0);
}

function get_monthly_order_count() {
    return get_option('website_monthly_order_count', 0);
}

function get_weekly_order_count() {
    return get_option('website_weekly_order_count', 0);
}

?>