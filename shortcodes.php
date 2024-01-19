<?php
function register_shortcodes() {
    add_shortcode('custom_sales_information', 'display_sales_information');
    add_shortcode('website_visitor_count', 'display_visitor_count');
    add_shortcode('website_conversion_rate', 'display_conversion_rate');
    add_shortcode('distinct_product_info', 'distinct_product_info_shortcode');
    add_shortcode('best_selling_categories', 'display_best_selling_categories');
}
add_action('init', 'register_shortcodes');



?>