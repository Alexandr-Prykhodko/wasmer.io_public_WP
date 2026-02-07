<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_print_section_info_monitor_404()
{
    seopress_print_pro_section('404'); ?>

    <div class="seopress-notice">
        <p>
            <?php esc_html_e('404 URLS are bad for:', 'wp-seopress-pro'); ?>
        </p>
        <ul class="list-none">
            <li><span class="dashicons dashicons-minus"></span><?php esc_html_e('User experience', 'wp-seopress-pro'); ?>
            </li>
            <li><span class="dashicons dashicons-minus"></span><?php esc_html_e('Performances', 'wp-seopress-pro'); ?>
            </li>
            <li><span class="dashicons dashicons-minus"></span><?php esc_html_e('Crawl budget allocated by Google', 'wp-seopress-pro'); ?>
            </li>
        </ul>

        <p>
            <?php esc_html_e('All these reasons degrade your SEO AND your conversion.', 'wp-seopress-pro'); ?>
        </p>

        <p>
            <?php echo wp_kses_post(__('We recommend to enable this feature after a site migration for example, or after major content changes on your site for <strong>about 1 month max to avoid false positives (robots, SPAMS...)</strong>.', 'wp-seopress-pro')); ?>
        </p>

        <p>
            <a href="<?php echo esc_url(admin_url('edit.php?post_type=seopress_404&post_status=redirects')); ?>" class="btn btnPrimary">
                <?php esc_html_e('View your redirects', 'wp-seopress-pro'); ?>
            </a>
            <a href="<?php echo esc_url(admin_url('edit.php?post_type=seopress_404&action=-1&m=0&redirect-cat=0&redirection-type=404&redirection-enabled&filter_action=Filter&paged=1&action2=-1&post_status=404')); ?>" class="btn btnTertiary">
                <?php esc_html_e('View your 404 errors', 'wp-seopress-pro'); ?>
            </a>
        </p>
    </div>

<?php
}
