<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

add_action('seopress_settings_sitemaps_image_after','seopress_pro_settings_sitemaps_image_after');
function seopress_pro_settings_sitemaps_image_after() {
    //Video sitemap
    add_settings_field(
        'seopress_xml_sitemap_video_enable', // ID
        __('Enable XML Video Sitemap', 'wp-seopress-pro'), // Title
        'seopress_pro_xml_sitemap_video_enable_callback', // Callback
        'seopress-settings-admin-xml-sitemap-general', // Page
        'seopress_setting_section_xml_sitemap_general' // Section
    );
}
