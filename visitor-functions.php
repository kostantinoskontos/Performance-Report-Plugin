<?php
function track_visitor($order_id) {
    // Check if the user has a custom field indicating they've been tracked
    $visited = isset($_COOKIE['visited']) ? true : false;

    if (!$visited) {
        // Increase the total visitor count
        $total_visitor_count = get_option('website_total_visitor_count', 0);
        update_option('website_total_visitor_count', $total_visitor_count + 1);

        // Increase the monthly visitor count
        $monthly_visitor_count = get_option('website_monthly_visitor_count', 0);
        update_option('website_monthly_visitor_count', $monthly_visitor_count + 1);

        // Increase the weekly visitor count
        $weekly_visitor_count = get_option('website_weekly_visitor_count', 0);
        update_option('website_weekly_visitor_count', $weekly_visitor_count + 1);

        // Set a cookie to prevent counting the same visitor multiple times
        setcookie('visited', 'yes', time() + 3600 * 24 * 30); // Set to expire in 30 days

    }
}


function get_total_visitor_count() {
    return get_option('website_total_visitor_count', 0);
}

function get_monthly_visitor_count() {
    return get_option('website_monthly_visitor_count', 0);
}

function get_weekly_visitor_count() {
    return get_option('website_weekly_visitor_count', 0);
}


function display_visitor_count() {
    $total_visitor_count = get_total_visitor_count();
    $monthly_visitor_count = get_monthly_visitor_count();
    $weekly_visitor_count = get_weekly_visitor_count();

    echo '<p>Total Visitors: ' . $total_visitor_count . '</p>';
    echo '<p>Monthly Visitors: ' . $monthly_visitor_count . '</p>';
    echo '<p>Weekly Visitors: ' . $weekly_visitor_count . '</p>';
}

// Hook into WordPress to track visitors
add_action('init', 'track_visitor');

function get_conversion_rate() {
    // Calculate the conversion rate (percentage of visitors who made a purchase)
    $total_visitor_count = get_total_visitor_count();
    $total_order_count = get_total_order_count();

    return $total_visitor_count > 0 ? ($total_order_count / $total_visitor_count) * 100 : 0;
}

function display_conversion_rate() {
    $conversion_rate = get_conversion_rate();

    echo '<p>Conversion Rate: ' . number_format($conversion_rate, 2) . '%</p>';
}


function display_distinct_product_info_dashboard() {
    global $wpdb;

    $distinct_product_info = $wpdb->get_results(
        "
        SELECT
            order_items.order_item_name AS product_name,
            COUNT(order_items.order_item_name) AS item_count
        FROM {$wpdb->prefix}woocommerce_order_items AS order_items
        WHERE order_items.order_item_type = 'line_item'
        GROUP BY order_items.order_item_name
        ORDER BY item_count DESC
        "
    );

    if (!empty($distinct_product_info)) {
        echo '<h1>Best Selling Products</h1>';
        echo '<ul>';

        foreach ($distinct_product_info as $product) {
            $product_name = esc_html($product->product_name);
            $item_count = esc_html($product->item_count);

            // Get product ID based on product name
            $product_id = $wpdb->get_var(
                $wpdb->prepare(
                    "
                    SELECT ID
                    FROM {$wpdb->prefix}posts
                    WHERE post_type = 'product'
                    AND post_title = %s
                    LIMIT 1
                    ",
                    $product_name
                )
            );

            // Check if product ID is valid
            if ($product_id) {
                // Display product information
                echo '<li>';
                echo '<strong>' . $product_name . '</strong><br>';

                // Display product image
                $product_image = get_the_post_thumbnail_url($product_id, 'thumbnail');
                if ($product_image) {
                    echo '<img src="' . esc_url($product_image) . '" alt="' . $product_name . '"><br>';
                }

                // Display product price
                $product = wc_get_product($product_id);
                if ($product && method_exists($product, 'get_price')) {
                    $product_price = $product->get_price();
                    echo 'Price: ' . wc_price($product_price) . '<br>';

                    // Display add-to-cart button
                    echo '<form action="' . esc_url(wc_get_cart_url()) . '" method="post">';
                    echo '<input type="hidden" name="add-to-cart" value="' . esc_attr($product_id) . '">';
                    echo '<input type="hidden" name="quantity" value="1">';
                    echo '<button type="submit" class="button">Add to Cart</button>';
                    echo '</form>';
                } else {
                    echo '<p>Product price not available.</p>';
                }

                echo '</li>';
            } else {
                echo '<p>Invalid product ID for ' . $product_name . '</p>';
            }
        }

        echo '</ul>';
    } else {
        echo '<p>No distinct product information found.</p>';
    }
}

function distinct_product_info_shortcode() {
    ob_start();
    display_distinct_product_info_dashboard();
    return ob_get_clean();
}

//Best selling categories 
function display_best_selling_categories() {
    global $wpdb;

    $query = "
        SELECT
            terms.name AS category_name,
            terms.term_id AS category_id,
            SUM(order_items_order_itemmeta.meta_value) AS total_revenue
        FROM
            {$wpdb->prefix}woocommerce_order_items AS order_items
        JOIN
            {$wpdb->prefix}woocommerce_order_itemmeta AS itemmeta ON order_items.order_item_id = itemmeta.order_item_id
        JOIN
            {$wpdb->term_relationships} AS relationships ON itemmeta.meta_value = relationships.object_id
        JOIN
            {$wpdb->terms} AS terms ON relationships.term_taxonomy_id = terms.term_id
        LEFT JOIN
            (
                SELECT order_item_id, meta_value
                FROM {$wpdb->prefix}woocommerce_order_itemmeta
                WHERE meta_key = '_line_subtotal'
            ) AS order_items_order_itemmeta ON order_items.order_item_id = order_items_order_itemmeta.order_item_id
        WHERE
            order_items.order_item_type = 'line_item'
            AND itemmeta.meta_key = '_product_id'
        GROUP BY
            terms.term_id
        HAVING
            category_name <> 'simple'
        ORDER BY
            total_revenue DESC
    ";

    $results = $wpdb->get_results($query);

    if ($results) {
        echo '<h2>Best Selling Categories</h2>';
        echo '<ul>';
        foreach ($results as $result) {
            echo '<li>';
            echo '<strong>' . esc_html($result->category_name) . '</strong>: ' . wc_price($result->total_revenue);
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No data available</p>';
    }
}







?>
