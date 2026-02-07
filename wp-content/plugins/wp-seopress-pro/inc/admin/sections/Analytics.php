<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_print_section_info_google_analytics_dashboard()
{
    $docs     = function_exists('seopress_get_docs_links') ? seopress_get_docs_links() : ''; ?>
    <hr>
    <h3 id="seopress-analytics-stats">
        <?php esc_html_e('Stats in dashboard', 'wp-seopress-pro'); ?>
    </h3>

    <p>
        <?php esc_html_e('Connect your WordPress site with Google Analytics API and get statistics right in your Dashboard.', 'wp-seopress-pro'); ?>
    </p>

    <p>
        <?php esc_html_e('This feature is completely independent of user tracking. For example, statistical data will be collected even if you have not entered your API keys below.', 'wp-seopress-pro'); ?>
    </p>

    <span class="seopress-help dashicons dashicons-external"></span>
    <a class="seopress-help" href="<?php echo esc_url($docs['analytics']['connect']); ?>" target="_blank">
        <?php esc_html_e('Watch our video guide to connect your WordPress site with Google Analytics API + common errors', 'wp-seopress-pro'); ?>
    </a>

    <div class="seopress-notice">
        <p>
            <?php
            /* translators: %1$s documentation URL, %2$s documentation URL */
            echo wp_kses_post(sprintf(__('No stats in the <strong>dashboard widget?</strong> Make sure to have activated these 2 Google APIs from Google Console: <span class="seopress-help dashicons dashicons-external"></span><a href="%1$s" target="_blank"><strong>Google Analytics API</strong></a> and <span class="seopress-help dashicons dashicons-external"></span><a href="%2$s" target="_blank"><strong>Google Analytics Data API</strong></a>.', 'wp-seopress-pro'), esc_url($docs['analytics']['api']['analytics']), esc_url($docs['analytics']['api']['data'])));
            ?>
        </p>
        <p>
            <?php esc_html_e('You must save your settings after selecting your GA property.', 'wp-seopress-pro'); ?>
        </p>
    </div>

<?php
}
