<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//Matomo Analytics SECTION=========================================================================
add_settings_section(
    'seopress_setting_section_google_analytics_matomo_widget', // ID
    '',
    //__("Matomo Analytics","wp-seopress-pro"), // Title
    'seopress_print_section_info_google_analytics_matomo_widget', // Callback
    'seopress-settings-admin-google-analytics-matomo-widget' // Page
);

add_settings_field(
    'seopress_google_analytics_matomo_widget_auth_token', // ID
    __('Matomo Authentication Token', 'wp-seopress-pro'), // Title
    'seopress_google_analytics_matomo_widget_auth_token_callback', // Callback
    'seopress-settings-admin-google-analytics-matomo-widget', // Page
    'seopress_setting_section_google_analytics_matomo_widget' // Section
);

add_settings_field(
    'seopress_google_analytics_matomo_dashboard_widget', // ID
    __('Remove Matomo dashboard widget', 'wp-seopress-pro'), // Title
    'seopress_google_analytics_matomo_dashboard_widget_callback', // Callback
    'seopress-settings-admin-google-analytics-matomo-widget', // Page
    'seopress_setting_section_google_analytics_matomo_widget' // Section
);
