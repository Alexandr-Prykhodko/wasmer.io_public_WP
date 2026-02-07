<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//SEO Alerts SECTION===============================================================================
add_settings_section(
    'seopress_setting_section_alerts', // ID
    '',
    //__("SEO Alerts","wp-seopress-pro"), // Title
    'seopress_print_section_info_alerts', // Callback
    'seopress-settings-admin-alerts' // Page
);

add_settings_field(
    'seopress_seo_alerts_noindex', // ID
    __('Alert if noindex on homepage', 'wp-seopress-pro'), // Title
    'seopress_seo_alerts_noindex_callback', // Callback
    'seopress-settings-admin-alerts', // Page
    'seopress_setting_section_alerts' // Section
);

add_settings_field(
    'seopress_seo_alerts_robots_txt', // ID
    __('Alert if robots.txt failed to load', 'wp-seopress-pro'), // Title
    'seopress_seo_alerts_robots_txt_callback', // Callback
    'seopress-settings-admin-alerts', // Page
    'seopress_setting_section_alerts' // Section
);

add_settings_field(
    'seopress_seo_alerts_xml_sitemaps', // ID
    __('Alert if XML sitemaps failed to load', 'wp-seopress-pro'), // Title
    'seopress_seo_alerts_xml_sitemaps_callback', // Callback
    'seopress-settings-admin-alerts', // Page
    'seopress_setting_section_alerts' // Section
);

add_settings_field(
    'seopress_seo_alerts_recipients', // ID
    __('Recipients', 'wp-seopress-pro'), // Title
    'seopress_seo_alerts_recipients_callback', // Callback
    'seopress-settings-admin-alerts', // Page
    'seopress_setting_section_alerts' // Section
);

add_settings_field(
    'seopress_seo_alerts_slack_webhook_url', // ID
    __('Slack Webhook URL', 'wp-seopress-pro'), // Title
    'seopress_seo_alerts_slack_webhook_url_callback', // Callback
    'seopress-settings-admin-alerts', // Page
    'seopress_setting_section_alerts' // Section
);
