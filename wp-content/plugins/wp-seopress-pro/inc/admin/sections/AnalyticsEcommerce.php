<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_print_section_info_google_analytics_ecommerce()
{
    $docs     = function_exists('seopress_get_docs_links') ? seopress_get_docs_links() : ''; ?>
    <hr>
    <h3 id="seopress-analytics-ecommerce">
        <?php esc_html_e('Ecommerce', 'wp-seopress-pro'); ?>
    </h3>
    <p>
        <?php esc_html_e('Track your ecommerce metrics with Google Analytics Enhanced Ecommerce.', 'wp-seopress-pro'); ?>
    </p>

    <p class="seopress-help">
        <a href="<?php echo esc_url($docs['analytics']['ecommerce']); ?>" target="_blank">
            <?php esc_html_e('Learn how to setup Google Analytics Enhanced Ecommerce', 'wp-seopress-pro'); ?>
        </a>
        <span class="dashicons dashicons-external"></span>
    </p>

    <?php if (!is_plugin_active('woocommerce/woocommerce.php')) { ?>
        <div class="seopress-notice is-warning">
            <p>
                <?php echo wp_kses_post(__('You need to enable <strong>WooCommerce</strong> to apply these settings.', 'wp-seopress-pro')); ?>
            </p>
        </div>
<?php
    }
}
