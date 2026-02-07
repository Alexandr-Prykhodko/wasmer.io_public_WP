<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_print_section_info_advanced_security_ga()
{
?>
    <hr>
    <h3>
        <?php esc_html_e('Google Analytics Stats in Dashboard widget', 'wp-seopress-pro'); ?>
    </h3>

    <div class="seopress-notice">
        <p>
            <?php echo wp_kses_post(__('By default, only users with <code>edit_dashboard</code> capability can view and configure the Google Analytics widget.', 'wp-seopress-pro')); ?>
        </p>
    </div>

    <p>
        <?php esc_html_e('Check a user role below to allow it to view and configure the GA widget:', 'wp-seopress-pro'); ?>
    </p>

<?php
}

function seopress_print_section_info_advanced_security_matomo()
{
?>
    <hr>
    <h3>
        <?php esc_html_e('Matomo Analytics Stats in Dashboard widget', 'wp-seopress-pro'); ?>
    </h3>

    <div class="seopress-notice">
        <p>
            <?php echo wp_kses_post(__('By default, only users with <code>edit_dashboard</code> capability can view and configure the Matomo Analytics widget.', 'wp-seopress-pro')); ?>
        </p>
    </div>

    <p>
        <?php esc_html_e('Check a user role below to allow it to view and configure the Matomo widget:', 'wp-seopress-pro'); ?>
    </p>

<?php
}
