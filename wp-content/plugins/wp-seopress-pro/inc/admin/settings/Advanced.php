<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//Advanced SECTION=========================================================================
add_settings_field(
    'seopress_advanced_appearance_ps_col', // ID
    __('Show Google Page Speed column in post types', 'wp-seopress-pro'), // Title
    'seopress_pro_advanced_appearance_ps_col_callback', // Callback
    'seopress-settings-admin-advanced-appearance', // Page
    'seopress_setting_section_advanced_appearance_col' // Section
);

add_settings_field(
    'seopress_advanced_appearance_search_console', // ID
    __('Show search console data', 'wp-seopress-pro'), // Title
    'seopress_pro_advanced_appearance_search_console_callback', // Callback
    'seopress-settings-admin-advanced-appearance', // Page
    'seopress_setting_section_advanced_appearance_col' // Section
);

add_action('seopress_settings_advanced_after', 'seopress_pro_settings_advanced_after');
function seopress_pro_settings_advanced_after()
{
    $versions = get_option('seopress_versions');
    $actual_version = isset($versions['pro']) ? $versions['pro'] : 0;

    if (version_compare($actual_version, '5.6', '>=')) {
        add_settings_section(
            'seopress_setting_section_advanced_security_ga', // ID
            '',
            //__("Security","wp-seopress-pro"), // Title
            'seopress_print_section_info_advanced_security_ga', // Callback
            'seopress-settings-admin-advanced-security' // Page
        );
    }

    if (version_compare($actual_version, '6.1', '>=')) {
        add_settings_section(
            'seopress_setting_section_advanced_security_matomo', // ID
            '',
            //__("Security","wp-seopress-pro"), // Title
            'seopress_print_section_info_advanced_security_matomo', // Callback
            'seopress-settings-admin-advanced-security' // Page
        );
    }
}

add_action('seopress_settings_advanced_url_rewriting', 'seopress_pro_settings_advanced_url_rewriting');
function seopress_pro_settings_advanced_url_rewriting()
{
    add_settings_field(
        'seopress_rewrite_search', // ID
        __('Custom URL for search results', 'wp-seopress-pro'), // Title
        'seopress_rewrite_search_callback', // Callback
        'seopress-settings-admin-advanced-advanced', // Page
        'seopress_setting_section_advanced_advanced_crawling' // Section
    );
}
