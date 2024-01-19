<?php
function add_sales_information_to_dashboard() {
    wp_add_dashboard_widget(
        'custom_sales_dashboard_widget',
        'Sales Information',
        'display_sales_information'
    );
}
add_action('wp_dashboard_setup', 'add_sales_information_to_dashboard');

function add_visitor_count_to_dashboard() {
    wp_add_dashboard_widget(
        'website_visitor_count_widget',
        'Visitor Count',
        'display_visitor_count'
    );
}
add_action('wp_dashboard_setup', 'add_visitor_count_to_dashboard');

function add_conversion_rate_to_dashboard() {
    wp_add_dashboard_widget(
        'website_conversion_rate_widget',
        'Conversion Rate',
        'display_conversion_rate'
    );
}
add_action('wp_dashboard_setup', 'add_conversion_rate_to_dashboard');

function register_distinct_product_info_dashboard_widget() {
    wp_add_dashboard_widget(
        'distinct_product_info_dashboard_widget',
        'Distinct Product Information',
        'display_distinct_product_info_dashboard'
    );
}
add_action('wp_dashboard_setup', 'register_distinct_product_info_dashboard_widget');


function add_best_selling_categories_widget() {
    wp_add_dashboard_widget(
        'best_selling_categories_widget',
        'Best Selling Categories',
        'display_best_selling_categories'
    );
}
add_action('wp_dashboard_setup', 'add_best_selling_categories_widget');
?>
