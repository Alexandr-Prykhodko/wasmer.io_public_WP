<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//White Label
//=================================================================================================
//Remove SEOPress admin header
if ('1' === seopress_pro_get_service('OptionPro')->getWhiteLabelAdminHeader()) {
    if ( ! defined('SEOPRESS_WL_ADMIN_HEADER')) {
        define('SEOPRESS_WL_ADMIN_HEADER', false);
    }
}

//Filter SEO admin menu dashicons
if (!empty(seopress_pro_get_service('OptionPro')->getWhiteLabelAdminMenu())) {
    function sp_seo_admin_menu_hook($html) {
        return seopress_pro_get_service('OptionPro')->getWhiteLabelAdminMenu();
    }
    add_filter('seopress_seo_admin_menu', 'sp_seo_admin_menu_hook');
}

//Change / remove SEOPress icon in admin bar
if (!empty(seopress_pro_get_service('OptionPro')->getWhiteLabelAdminBarIcon())) {
    function sp_adminbar_icon_hook($html) {
        $html = seopress_pro_get_service('OptionPro')->getWhiteLabelAdminBarIcon();

        return $html;
    }
    add_filter('seopress_adminbar_icon', 'sp_adminbar_icon_hook');
}

//Change / remove SEOPress title from main menu
if (!empty(seopress_pro_get_service('OptionPro')->getWhiteLabelAdminTitle())) {
    function sp_white_label_admin_title_hook($html) {
        $html = seopress_pro_get_service('OptionPro')->getWhiteLabelAdminTitle();

        return $html;
    }
    add_filter('seopress_seo_admin_menu_title', 'sp_white_label_admin_title_hook');
}

//Remove SEOPress help links
if ('1' === seopress_pro_get_service('OptionPro')->getWhiteLabelHelpLinks()) {
    function seopress_white_label_css() {
        $get_current_screen = get_current_screen();
        $get_current_screen = $get_current_screen->id;
        if (true === is_seopress_page() || 'seopress_404' === $get_current_screen || 'seopress_bot' === $get_current_screen || 'seopress_backlinks' === $get_current_screen || 'seopress_schemas' === $get_current_screen) {
            echo '<style>.seopress-help, .seopress-doc, .seopress-your-schema .notice-info{display:none !important;}</style>';

            wp_dequeue_script('seopress-pro-chatbot');
        }
    }
    add_action('admin_head', 'seopress_white_label_css');
}

//Remove SEOPress menu/submenu pages (multisite only)
if (is_multisite()) {
    if ( ! empty(seopress_pro_get_service('OptionPro')->getWhiteLabelMenuPages())) {
        if ( ! is_super_admin()) {
            add_action('admin_menu', 'seopress_remove_menu_page_hook');
            function seopress_remove_menu_page_hook() {
                $seopress_menu_pages_array = seopress_pro_get_service('OptionPro')->getWhiteLabelMenuPages();

                if (array_key_exists('seopress-option', $seopress_menu_pages_array)) {
                    remove_menu_page('seopress-option'); //SEO
                }
            }

            add_action('admin_menu', 'seopress_remove_submenu_page_hook', 999);
            function seopress_remove_submenu_page_hook() {
                $seopress_menu_pages_array = seopress_pro_get_service('OptionPro')->getWhiteLabelMenuPages();

                foreach ($seopress_menu_pages_array as $key => $value) {
                    remove_submenu_page('seopress-option', $key);

                    //Remove feature from Dashboard
                    $map = [
                        'seopress-titles'           => 'titles',
                        'seopress-xml-sitemap'      => 'xml_sitemap',
                        'seopress-social'           => 'social',
                        'seopress-google-analytics' => 'google_analytics',
                        'seopress-advanced'         => 'advanced',
                        'seopress-import-export'    => 'tools',
                        'seopress-pro-page'         => [
                            'woocommerce',
                            'edd',
                            'local_business',
                            'dublin_core',
                            'breadcrumbs',
                            'schemas',
                            'page_speed',
                            'robots',
                            'news',
                            'rewrite',
                            'htaccess',
                            'rss',
                            'redirects',
                        ],
                        'edit.php?post_type=seopress_404'     => 'redirects',
                        'edit.php?post_type=seopress_bot'     => 'bot',
                        'edit.php?post_type=seopress_schemas' => 'schemas',
                        'seopress-bot-batch'                  => 'bot',
                        'seopress-license'                    => 'license',
                    ];

                    if (array_key_exists($key, $map)) {
                        add_filter('seopress_remove_feature_' . $map[$key], '__return_false');
                        if ('seopress-pro-page' == $key) {
                            foreach ($map['seopress-pro-page'] as $_value) {
                                add_filter('seopress_remove_feature_' . $_value, '__return_false');
                            }
                        }
                    }
                }
            }
        }
    }
}

if (!empty(seopress_pro_get_service('OptionPro')->getWhiteLabelListTitle())
|| !empty(seopress_pro_get_service('OptionPro')->getWhiteLabelListTitlePro())
|| !empty(seopress_pro_get_service('OptionPro')->getWhiteLabelListDesc())
|| !empty(seopress_pro_get_service('OptionPro')->getWhiteLabelListDesc())
|| !empty(seopress_pro_get_service('OptionPro')->getWhiteLabelListAuthor())
|| !empty(seopress_pro_get_service('OptionPro')->getWhiteLabelListWebsite())
) {
    add_filter('all_plugins', 'seopress_filter_plugins_list', 10, 1);
    function seopress_filter_plugins_list($data) {
        //SEOPress
        if (array_key_exists('wp-seopress/seopress.php', $data)) {
            //Title
            if (!empty(seopress_pro_get_service('OptionPro')->getWhiteLabelListTitle())) {
                $data['wp-seopress/seopress.php']['Name']  = seopress_pro_get_service('OptionPro')->getWhiteLabelListTitle();
                $data['wp-seopress/seopress.php']['Title'] = seopress_pro_get_service('OptionPro')->getWhiteLabelListTitle();
            }

            //Description
            if (!empty(seopress_pro_get_service('OptionPro')->getWhiteLabelListDesc())) {
                $data['wp-seopress/seopress.php']['Description'] = seopress_pro_get_service('OptionPro')->getWhiteLabelListDesc();
            }

            //Author
            if (!empty(seopress_pro_get_service('OptionPro')->getWhiteLabelListAuthor())) {
                $data['wp-seopress/seopress.php']['Author']     = seopress_pro_get_service('OptionPro')->getWhiteLabelListAuthor();
                $data['wp-seopress/seopress.php']['AuthorName'] = seopress_pro_get_service('OptionPro')->getWhiteLabelListAuthor();
            }

            //Website
            if (!empty(seopress_pro_get_service('OptionPro')->getWhiteLabelListWebsite())) {
                $data['wp-seopress/seopress.php']['AuthorURI'] = seopress_pro_get_service('OptionPro')->getWhiteLabelListWebsite();
            }
        }

        //SEOPress PRO
        if (array_key_exists('wp-seopress-pro/seopress-pro.php', $data)) {
            //Title
            if (!empty(seopress_pro_get_service('OptionPro')->getWhiteLabelListTitlePro())) {
                $data['wp-seopress-pro/seopress-pro.php']['Name']  = seopress_pro_get_service('OptionPro')->getWhiteLabelListTitlePro();
                $data['wp-seopress-pro/seopress-pro.php']['Title'] = seopress_pro_get_service('OptionPro')->getWhiteLabelListTitlePro();
            }

            //Description
            if (!empty(seopress_pro_get_service('OptionPro')->getWhiteLabelListDescPro())) {
                $data['wp-seopress-pro/seopress-pro.php']['Description'] = seopress_pro_get_service('OptionPro')->getWhiteLabelListDescPro();
            }

            //Author
            if (!empty(seopress_pro_get_service('OptionPro')->getWhiteLabelListAuthor())) {
                $data['wp-seopress-pro/seopress-pro.php']['Author']     = seopress_pro_get_service('OptionPro')->getWhiteLabelListAuthor();
                $data['wp-seopress-pro/seopress-pro.php']['AuthorName'] = seopress_pro_get_service('OptionPro')->getWhiteLabelListAuthor();
            }

            //Website
            if (!empty(seopress_pro_get_service('OptionPro')->getWhiteLabelListWebsite())) {
                $data['wp-seopress-pro/seopress-pro.php']['AuthorURI'] = seopress_pro_get_service('OptionPro')->getWhiteLabelListWebsite();
            }
        }

        return $data;
    }
}

if (!empty(seopress_pro_get_service('OptionPro')->getWhiteLabelListViewDetails())) {
    // Remove View details modal from plugins list
    add_filter('plugin_row_meta', 'seopress_filter_plugins_list_meta', 10, 2);
    function seopress_filter_plugins_list_meta($links, $file) {
        // Do not enable for super admin users and/or users with update_plugins cap
        if (is_super_admin() === true || current_user_can( 'update_plugins' )) {
            return $links;
        }

        if (false !== strpos($file, 'wp-seopress/seopress.php') || false !== strpos($file, 'wp-seopress-pro/seopress-pro.php')) {
            unset($links[2]);
        }

        return $links;
    }

    // Remove update notification from plugins list
    function seopress_remove_plugin_update_notification($value) {
        // Do not enable for super admin users and/or users with update_plugins cap
        if (is_super_admin() === true || current_user_can( 'update_plugins' )) {
            return $value;
        }

        if (isset($value->response['wp-seopress/seopress.php']) || isset($value->response['wp-seopress-pro/seopress-pro.php'])) {
            unset($value->response['wp-seopress/seopress.php']);
            unset($value->response['wp-seopress-pro/seopress-pro.php']);
        }
        return $value;
    }
    add_filter('site_transient_update_plugins', 'seopress_remove_plugin_update_notification');
}


