<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//Inspect URL SECTION==============================================================================
add_settings_section(
    'seopress_setting_section_inspect_url', // ID
    '',
    //__("Google Search Console","wp-seopress-pro"), // Title
    'seopress_print_section_info_inspect_url', // Callback
    'seopress-settings-admin-inspect-url' // Page
);

add_settings_field(
    'seopress_pro_inspect_url_api', // ID
    __('Google Search Console API key (JSON file)', 'wp-seopress-pro'), // Title
    'seopress_pro_inspect_url_api_callback', // Callback
    'seopress-settings-admin-inspect-url', // Page
    'seopress_setting_section_inspect_url' // Section
);

add_settings_field(
    'seopress_gsc_domain_property', // ID
    __('Domain property', 'wp-seopress-pro'), // Title
    'seopress_gsc_domain_property_callback', // Callback
    'seopress-settings-admin-inspect-url', // Page
    'seopress_setting_section_inspect_url' // Section
);

add_settings_field(
    'seopress_gsc_date_range', // ID
    __('Date range', 'wp-seopress-pro'), // Title
    'seopress_gsc_date_range_callback', // Callback
    'seopress-settings-admin-inspect-url', // Page
    'seopress_setting_section_inspect_url' // Section
);
