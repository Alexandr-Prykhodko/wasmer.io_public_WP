<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//WooCommerce
function seopress_woocommerce_cart_page_no_index_callback() {
    $options = get_option('seopress_pro_option_name');

    $check = isset($options['seopress_woocommerce_cart_page_no_index']); ?>

<label for="seopress_woocommerce_cart_page_no_index">
    <input id="seopress_woocommerce_cart_page_no_index"
        name="seopress_pro_option_name[seopress_woocommerce_cart_page_no_index]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php esc_html_e('noindex', 'wp-seopress-pro'); ?>
</label>

<p class="description">
    <?php esc_html_e('If your theme or plugin displays the cart across your entire WordPress site, don\'t enable this option.', 'wp-seopress-pro'); ?>
</p>

<?php if (isset($options['seopress_woocommerce_cart_page_no_index'])) {
        esc_attr($options['seopress_woocommerce_cart_page_no_index']);
    }
}

function seopress_woocommerce_checkout_page_no_index_callback() {
    $options = get_option('seopress_pro_option_name');

    $check = isset($options['seopress_woocommerce_checkout_page_no_index']); ?>

<label for="seopress_woocommerce_checkout_page_no_index">
    <input id="seopress_woocommerce_checkout_page_no_index"
        name="seopress_pro_option_name[seopress_woocommerce_checkout_page_no_index]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php esc_html_e('noindex', 'wp-seopress-pro'); ?>
</label>

<?php if (isset($options['seopress_woocommerce_checkout_page_no_index'])) {
        esc_attr($options['seopress_woocommerce_checkout_page_no_index']);
    }
}

function seopress_woocommerce_customer_account_page_no_index_callback() {
    $options = get_option('seopress_pro_option_name');

    $check = isset($options['seopress_woocommerce_customer_account_page_no_index']); ?>

<label for="seopress_woocommerce_customer_account_page_no_index">
    <input id="seopress_woocommerce_customer_account_page_no_index"
        name="seopress_pro_option_name[seopress_woocommerce_customer_account_page_no_index]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php esc_html_e('noindex', 'wp-seopress-pro'); ?>
</label>

<?php if (isset($options['seopress_woocommerce_customer_account_page_no_index'])) {
        esc_attr($options['seopress_woocommerce_customer_account_page_no_index']);
    }
}

function seopress_woocommerce_product_og_price_callback() {
    $options = get_option('seopress_pro_option_name');

    $check = isset($options['seopress_woocommerce_product_og_price']); ?>

<label for="seopress_woocommerce_product_og_price">
    <input id="seopress_woocommerce_product_og_price"
        name="seopress_pro_option_name[seopress_woocommerce_product_og_price]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>
    <?php esc_html_e('Add product:price:amount meta for product', 'wp-seopress-pro'); ?>
</label>

<pre><?php echo esc_html('<meta property="product:price:amount" content="99" />'); ?></pre>

<?php if (isset($options['seopress_woocommerce_product_og_price'])) {
        esc_attr($options['seopress_woocommerce_product_og_price']);
    }
}

function seopress_woocommerce_product_og_currency_callback() {
    $options = get_option('seopress_pro_option_name');

    $check = isset($options['seopress_woocommerce_product_og_currency']); ?>

<label for="seopress_woocommerce_product_og_currency">
    <input id="seopress_woocommerce_product_og_currency"
        name="seopress_pro_option_name[seopress_woocommerce_product_og_currency]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>
    <?php esc_html_e('Add product:price:currency meta for product', 'wp-seopress-pro'); ?>
</label>

<pre><?php echo esc_html('<meta property="product:price:currency" content="EUR" />'); ?></pre>

<?php if (isset($options['seopress_woocommerce_product_og_currency'])) {
        esc_attr($options['seopress_woocommerce_product_og_currency']);
    }
}

function seopress_woocommerce_meta_generator_callback() {
    $options = get_option('seopress_pro_option_name');

    $check = isset($options['seopress_woocommerce_meta_generator']); ?>

<label for="seopress_woocommerce_meta_generator">
    <input id="seopress_woocommerce_meta_generator" name="seopress_pro_option_name[seopress_woocommerce_meta_generator]"
        type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php esc_html_e('Remove WooCommerce meta generator', 'wp-seopress-pro'); ?>
</label>

<pre><?php echo esc_html('<meta name="generator" content="WooCommerce 7.5" />'); ?></pre>

<?php if (isset($options['seopress_woocommerce_meta_generator'])) {
        esc_attr($options['seopress_woocommerce_meta_generator']);
    }
}

function seopress_woocommerce_schema_output_callback() {
    $options = get_option('seopress_pro_option_name');

    $check = isset($options['seopress_woocommerce_schema_output']); ?>

<label for="seopress_woocommerce_schema_output">
    <input id="seopress_woocommerce_schema_output" name="seopress_pro_option_name[seopress_woocommerce_schema_output]"
        type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php esc_html_e('Remove default JSON-LD structured data (WooCommerce 3+)', 'wp-seopress-pro'); ?>
</label>

<p class="description">
    <?php
        /* translators: %s: admin URL of schemas post type */
        echo wp_kses_post(sprintf(__('The default product schema added by WooCommerce generates errors in Google Search Console. Disable it and create your own <a href="%s">automatic product schema</a>.', 'wp-seopress-pro'), esc_url(admin_url('edit.php?post_type=seopress_schemas'))));
    ?>
</p>

<?php if (isset($options['seopress_woocommerce_schema_output'])) {
        esc_attr($options['seopress_woocommerce_schema_output']);
    }
}

function seopress_woocommerce_schema_breadcrumbs_output_callback() {
    $options = get_option('seopress_pro_option_name');

    $check = isset($options['seopress_woocommerce_schema_breadcrumbs_output']); ?>

<label for="seopress_woocommerce_schema_breadcrumbs_output">
    <input id="seopress_woocommerce_schema_breadcrumbs_output"
        name="seopress_pro_option_name[seopress_woocommerce_schema_breadcrumbs_output]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php esc_html_e('Remove default breadcrumbs JSON-LD structured data (WooCommerce 3+)', 'wp-seopress-pro'); ?>
</label>

<p class="description">
    <?php esc_html_e('If "Remove default JSON-LD structured data (WooCommerce 3+)" option is already checked, the breadcrumbs schema is already removed from your source code.', 'wp-seopress-pro'); ?>
</p>

<?php if (isset($options['seopress_woocommerce_schema_breadcrumbs_output'])) {
        esc_attr($options['seopress_woocommerce_schema_breadcrumbs_output']);
    }
}
