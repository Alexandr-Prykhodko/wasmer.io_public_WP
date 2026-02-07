<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_print_section_info_page_speed()
{
    seopress_print_pro_section('page-speed');
    $options = get_option('seopress_pro_option_name');
    $url = isset($options['seopress_ps_url']) ? $options['seopress_ps_url'] : get_home_url();
?>

    <p>
        <?php esc_html_e('Learn how your site has performed, based on data from your actual users around the world.', 'wp-seopress-pro'); ?>
    </p>

    <p>
        <button type="button" class="seopress-request-page-speed btn btnPrimary" data_permalink="<?php if (isset($url)) { echo esc_html($url); } else { echo get_home_url(); } ?>">
            <?php esc_html_e('Analyse with PageSpeed Insights', 'wp-seopress-pro'); ?>
        </button>

        <a href="javascript:window.print()" class="btn btnTertiary">
            <?php esc_html_e('Save as PDF', 'wp-seopress-pro'); ?>
        </a>

        <button type="button" id="seopress-clear-page-speed-cache" class="btn btnTertiary is-deletable">
            <?php esc_html_e('Remove last analysis', 'wp-seopress-pro'); ?>
        </button>

        <span class="spinner"></span>
    </p>
<?php
}
