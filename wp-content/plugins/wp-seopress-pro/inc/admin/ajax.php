<?php
defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

require_once __DIR__ . '/export/csv.php';

///////////////////////////////////////////////////////////////////////////////////////////////////
//SEOPress Bot
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_request_bot() {
    check_ajax_referer('seopress_request_bot_nonce');

    if (current_user_can(seopress_capability('manage_options', 'bot')) && is_admin()) {
        //Init
        $data = [];

        //Cleaning seopress_bot post type
        if ('1' === seopress_pro_get_service('OptionBot')->getBotScanSettingsCleaning() && isset($_POST['offset']) && 0 == $_POST['offset']) {
            global $wpdb;

            // delete all posts by post type.
            $sql = 'DELETE `posts`, `pm`
				FROM `' . $wpdb->prefix . 'posts` AS `posts`
				LEFT JOIN `' . $wpdb->prefix . 'postmeta` AS `pm` ON `pm`.`post_id` = `posts`.`ID`
				WHERE `posts`.`post_type` = \'seopress_bot\'';
            $wpdb->query($sql);
        }

        if (isset($_POST['offset'])) {
            $offset = absint($_POST['offset']);
        }

        if (!empty(seopress_pro_get_service('OptionBot')->getBotScanSettingsPostTypes())) {
            $seopress_bot_post_types_cpt_array = [];
            foreach (seopress_pro_get_service('OptionBot')->getBotScanSettingsPostTypes() as $cpt_key => $cpt_value) {
                foreach ($cpt_value as $_cpt_key => $_cpt_value) {
                    if ('1' == $_cpt_value) {
                        array_push($seopress_bot_post_types_cpt_array, $cpt_key);
                    }
                }
            }

            if ('' != seopress_pro_get_service('OptionBot')->getBotScanSettingsNumber() && seopress_pro_get_service('OptionBot')->getBotScanSettingsNumber() >= 10) {
                $limit = seopress_pro_get_service('OptionBot')->getBotScanSettingsNumber();
            } else {
                $limit = 100;
            }

            global $post;

            if ($offset > $limit) {
                wp_reset_postdata();
                //Log date
                update_option('seopress_bot_log', current_time('Y-m-d H:i'), 'yes');

                $offset = 'done';
            } else {
                $args = [
                    'posts_per_page' => 1,
                    'offset' => $offset,
                    'cache_results' => false,
                    'order' => 'DESC',
                    'orderby' => 'date',
                    'post_type' => $seopress_bot_post_types_cpt_array,
                    'post_status' => 'publish',
                    'fields' => 'ids',
                ];
                $args = apply_filters('seopress_bot_query', $args, $offset, $seopress_bot_post_types_cpt_array);
                $bot_query = get_posts($args);

                if ($bot_query) {
                    //DOM
                    $dom = new DOMDocument();
                    $internalErrors = libxml_use_internal_errors(true);
                    $dom->preserveWhiteSpace = false;

                    //Get source code
                    if ('' != seopress_pro_get_service('OptionBot')->getBotScanSettingsTimeout()) {
                        $timeout = seopress_pro_get_service('OptionBot')->getBotScanSettingsTimeout();
                    } else {
                        $timeout = 5;
                    }

                    //Get cookies
                    if (isset($_COOKIE)) {
                        $cookies = [];

                        foreach ($_COOKIE as $name => $value) {
                            if ('PHPSESSID' !== $name) {
                                $cookies[] = new WP_Http_Cookie(['name' => $name, 'value' => $value]);
                            }
                        }
                    }

                    $args = [
                        'blocking' => true,
                        'timeout' => $timeout,
                        'sslverify' => false,
                        'compress' => true,
                        'redirection' => 4,
                    ];

                    if (isset($cookies) && ! empty($cookies)) {
                        $args['cookies'] = $cookies;
                    }

                    $args = apply_filters( 'seopress_bot_query_dom_args', $args );

                    foreach ($bot_query as $post) {
                        if ('' === seopress_pro_get_service('OptionBot')->getBotScanSettingsWhere() || 'post_content' === seopress_pro_get_service('OptionBot')->getBotScanSettingsWhere()) {//post content
                            //this code will not run shortcodes
                            $response = get_post_field('post_content', $post);
                        } else { //body page
                            $response = wp_remote_get(get_permalink($post), $args);

                            //Check for error
                            if (is_wp_error($response) || '404' === wp_remote_retrieve_response_code($response)) {
                                $data['post_title'] = __('Unable to request page: ', 'wp-seopress-pro') . get_the_title($post);
                            } else {
                                $response = wp_remote_retrieve_body($response);
                            }
                        }

                        if ( ! is_wp_error($response) || '404' !== wp_remote_retrieve_response_code($response)) {
                            if (get_the_title($post)) {
                                $data['post_title'] = get_the_title($post) . ' (' . get_permalink($post) . ')';

                                if (is_string($response)) {
                                    if ($dom->loadHTML('<?xml encoding="utf-8" ?>' . $response)) {
                                        $xpath = new DOMXPath($dom);

                                        //Links
                                        $links = $xpath->query('//a');

                                        if ( ! empty($links)) {
                                            foreach ($links as $key => $link) {
                                                $links2 = [];
                                                $links3 = [];

                                                $href = $link->getAttribute('href');
                                                $text = esc_attr($link->textContent);

                                                //remove anchors
                                                if ('#' != $href) {
                                                    $links2[$text] = $href;
                                                }

                                                //remove duplicates
                                                $links2 = array_unique($links2);

                                                $bot_status_code = ''; // Maybe use "domain not found" instead of empty string

                                                foreach ($links2 as $_key => $_value) {
                                                    $args = [
                                                        'timeout' => $timeout,
                                                        'blocking' => true,
                                                        'sslverify' => false,
                                                        'compress' => true,
                                                    ];

                                                    $response = wp_remote_get($_value, $args);

                                                    $bot_status_code = wp_remote_retrieve_response_code($response);

                                                    if ( ! $bot_status_code) {
                                                        $bot_status_code = __('domain not found', 'wp-seopress-pro');
                                                    }

                                                    if ('1' === seopress_pro_get_service('OptionBot')->getBotScanSettingsType()) {
                                                        $bot_status_type = wp_remote_retrieve_header($response, 'content-type');
                                                    }

                                                    if ('1' === seopress_pro_get_service('OptionBot')->getBotScanSettings404()) {
                                                        if ('404' == $bot_status_code || strpos(wp_json_encode($response), 'cURL error 6')) {
                                                            $links3[] = $_value;
                                                        }
                                                    } else {
                                                        $links3[] = $_value;
                                                    }
                                                }

                                                foreach ($links3 as $_key => $_value) {
                                                    $check_page_id = seopress_pro_get_service('Redirection')->getPageByTitle($_value, '', 'seopress_bot');

                                                    if (is_bool($check_page_id)) {
                                                        wp_insert_post(
                                                            [
                                                                'post_title' => $_value,
                                                                'post_type' => 'seopress_bot',
                                                                'post_status' => 'publish',
                                                                'meta_input' => [
                                                                    'seopress_bot_type' => $bot_status_type,
                                                                    'seopress_bot_status' => $bot_status_code,
                                                                    'seopress_bot_source_url' => get_permalink($post),
                                                                    'seopress_bot_source_id' => $post,
                                                                    'seopress_bot_cpt' => get_post_type($post),
                                                                    'seopress_bot_source_title' => get_the_title($post),
                                                                    'seopress_bot_a_title' => $text,
                                                                ],
                                                            ]
                                                        );
                                                    } elseif (!is_bool($check_page_id) && $check_page_id->post_title == $_value) {
                                                        $seopress_bot_count = get_post_meta($check_page_id->ID, 'seopress_bot_count', true);
                                                        update_post_meta($check_page_id->ID, 'seopress_bot_count', ++$seopress_bot_count);
                                                    }

                                                    $data['link'][] = $_value;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }//End foreach
                    libxml_use_internal_errors($internalErrors);
                    ++$offset;
                } else {
                    wp_reset_postdata();
                    //Log date
                    update_option('seopress_bot_log', current_time('Y-m-d H:i'), 'yes');

                    $offset = 'done';
                }
            }
        }
        $data['offset'] = $offset;

        //Return
        wp_send_json_success($data);
    }
}
add_action('wp_ajax_seopress_request_bot', 'seopress_request_bot');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Admin Columns PRO
///////////////////////////////////////////////////////////////////////////////////////////////////
if (is_plugin_active('admin-columns-pro/admin-columns-pro.php')) {
    if ( defined('ACP_VERSION') &&  ( version_compare( ACP_VERSION, '6.0', '<' ) || version_compare( ACP_VERSION, '7.0', '>=' ) ) ) {
        return;
    }

    add_action('ac/column_groups', 'ac_register_seopress_column_group');
    function ac_register_seopress_column_group(AC\Groups $groups) {
        $groups->register_group('seopress', 'SEOPress');
    }

    add_action('ac/column_types', 'ac_register_seopress_columns');
    function ac_register_seopress_columns(AC\ListScreen $list_screen) {
        if ($list_screen instanceof ACP\ListScreen\Post) {
            require_once plugin_dir_path(__FILE__) . 'thirds/admin-columns/acp-column-sp_title.php';
            require_once plugin_dir_path(__FILE__) . 'thirds/admin-columns/acp-column-sp_desc.php';
            require_once plugin_dir_path(__FILE__) . 'thirds/admin-columns/acp-column-sp_noindex.php';
            require_once plugin_dir_path(__FILE__) . 'thirds/admin-columns/acp-column-sp_nofollow.php';
            require_once plugin_dir_path(__FILE__) . 'thirds/admin-columns/acp-column-sp_target_kw.php';
            require_once plugin_dir_path(__FILE__) . 'thirds/admin-columns/acp-column-sp_redirect.php';
            require_once plugin_dir_path(__FILE__) . 'thirds/admin-columns/acp-column-sp_redirect_url.php';
            require_once plugin_dir_path(__FILE__) . 'thirds/admin-columns/acp-column-sp_canonical.php';
            require_once plugin_dir_path(__FILE__) . 'thirds/admin-columns/acp-column-sp_gsc_positions.php';
            require_once plugin_dir_path(__FILE__) . 'thirds/admin-columns/acp-column-sp_gsc_clicks.php';
            require_once plugin_dir_path(__FILE__) . 'thirds/admin-columns/acp-column-sp_gsc_impressions.php';
            require_once plugin_dir_path(__FILE__) . 'thirds/admin-columns/acp-column-sp_gsc_ctr.php';

            $list_screen->register_column_type(new ACP_Column_sp_title());
            $list_screen->register_column_type(new ACP_Column_sp_desc());
            $list_screen->register_column_type(new ACP_Column_sp_noindex());
            $list_screen->register_column_type(new ACP_Column_sp_nofollow());
            $list_screen->register_column_type(new ACP_Column_sp_target_kw());
            $list_screen->register_column_type(new ACP_Column_sp_redirect());
            $list_screen->register_column_type(new ACP_Column_sp_redirect_url());
            $list_screen->register_column_type(new ACP_Column_sp_canonical());
            $list_screen->register_column_type(new ACP_Column_sp_gsc_positions());
            $list_screen->register_column_type(new ACP_Column_sp_gsc_clicks());
            $list_screen->register_column_type(new ACP_Column_sp_gsc_impressions());
            $list_screen->register_column_type(new ACP_Column_sp_gsc_ctr());
        }
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//LB Widget order
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_pro_lb_widget() {
    check_ajax_referer('seopress_pro_lb_widget_nonce');
    if (current_user_can('edit_theme_options') && is_admin()) {
        if (isset($_POST['order']) && $_POST['order'] && isset($_POST['id']) && $_POST['id']) {
            $widget_option = get_option('widget_seopress_pro_lb_widget');

            $widget_option[(int)$_POST['id']]['order'] = $_POST['order'];

            update_option('widget_seopress_pro_lb_widget', $widget_option);
        }
    }

    wp_send_json_success();
}
add_action('wp_ajax_seopress_pro_lb_widget', 'seopress_pro_lb_widget');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Clear Google Page Speed cache
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_clear_page_speed_cache() {
    check_ajax_referer('seopress_clear_page_speed_cache_nonce');

    global $wpdb;

    $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_seopress_results_page_speed' ");
    $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_seopress_results_page_speed' ");
    $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_seopress_results_page_speed_desktop' ");
    $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_seopress_results_page_speed_desktop' ");

    exit();
}
add_action('wp_ajax_seopress_clear_page_speed_cache', 'seopress_clear_page_speed_cache');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Reset License
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_request_reset_license() {
    check_ajax_referer('seopress_request_reset_license_nonce');

    if (current_user_can(seopress_capability('manage_options', 'license')) && is_admin()) {
        delete_option('seopress_pro_license_status');
        delete_option('seopress_pro_license_key');
        delete_option('seopress_pro_license_key_error');
        delete_option('seopress_pro_license_automatic_attempt');
        delete_option('seopress_pro_license_home_url');

        $data = ['url' => admin_url('admin.php?page=seopress-license')];
        wp_send_json_success($data);
    }
}
add_action('wp_ajax_seopress_request_reset_license', 'seopress_request_reset_license');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Lock Google Analytics view
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_google_analytics_lock() {
    check_ajax_referer('seopress_google_analytics_lock_nonce');

    update_option('seopress_google_analytics_lock_option_name', '1', 'yes');

    wp_send_json_success();
}
add_action('wp_ajax_seopress_google_analytics_lock', 'seopress_google_analytics_lock');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Save htaccess file
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_save_htaccess() {
    check_ajax_referer('seopress_save_htaccess_nonce');

    if (!current_user_can(seopress_capability('manage_options', 'htaccess')) && !is_admin()) {
        exit;
    }
    $filename = get_home_path() . '/.htaccess';

    if (!file_exists(get_home_path() . '/.htaccess')) {
        $msg = __('Impossible to open file: ', 'wp-seopress-pro') . $filename;
        $class = 'is-error';
    }
    $old_htaccess = file_get_contents($filename);

    if (isset($_POST['htaccess_content'])) {
        $current_htaccess = stripslashes($_POST['htaccess_content']);
    }

    if (is_writable($filename)) {
        if ( ! $handle = fopen($filename, 'w')) {
            $msg = __('Impossible to open file: ', 'wp-seopress-pro') . $filename;
            $class = 'is-error';
        }

        if (false === fwrite($handle, $current_htaccess)) {
            $msg = __('Impossible to write in file: ', 'wp-seopress-pro') . $filename;
            $class = 'is-error';
        }

        fclose($handle);

        $args = [
            'blocking' => true,
            'redirection' => 0,
        ];

        $test  = wp_remote_retrieve_response_code( wp_remote_get( get_home_url(), $args ) );

        if (is_wp_error($test) || 200 !== $test) {
            $handle = fopen($filename, 'w');
            fwrite($handle, $old_htaccess);
            fclose($handle);

            $msg = __('.htaccess not updated due to a syntax error!', 'wp-seopress-pro');
            $class = 'is-error';
        } else {
            $msg = __('.htaccess successfully updated!', 'wp-seopress-pro');
            $class = 'is-success';
        }

    } else {
        $msg = __('Your .htaccess is not writable.', 'wp-seopress-pro');
        $class = 'is-error';
    }

    $data = [
        'msg' => $msg,
        'class' => $class
    ];

    wp_send_json_success($data);
}
add_action('wp_ajax_seopress_save_htaccess', 'seopress_save_htaccess');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Inspect URL with Google Search Console API
///////////////////////////////////////////////////////////////////////////////////////////////////
add_action('wp_ajax_seopress_inspect_url', 'seopress_inspect_url');
function seopress_inspect_url() {
    check_ajax_referer('seopress_inspect_url_nonce');

    if ( ! current_user_can('edit_posts') && ! is_admin()) {
        return;
    }


    $data = [];

    //Get post id
    if (isset($_POST['post_id'])) {
        $id = $_POST['post_id'];
    }

    if (empty($id)) {
        return;
    }

    $data = seopress_pro_get_service('InspectUrlGoogle')->handle($id);

    wp_send_json_success($data);
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Regenerate Video XML Sitemap
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_pro_video_xml_sitemap_regenerate()
{
    check_ajax_referer('seopress_video_regenerate_nonce', '_ajax_nonce', true);

    if (current_user_can(seopress_capability('manage_options', 'migration')) && is_admin()) {
        if (isset($_POST['offset']) && isset($_POST['offset'])) {
            $offset = absint($_POST['offset']);
        }

        $cpt = ['any'];
        if (!empty(seopress_get_service('SitemapOption')->getPostTypesList())) {
            unset($cpt[0]);
            foreach (seopress_get_service('SitemapOption')->getPostTypesList() as $cpt_key => $cpt_value) {
                foreach ($cpt_value as $_cpt_key => $_cpt_value) {
                    if ('1' == $_cpt_value) {
                        $cpt[] = $cpt_key;
                    }
                }
            }

            $cpt = array_map(function($item) {
                return "'" . esc_sql($item) . "'";
            }, $cpt);

            $cpt_string = implode(",", $cpt);
        }

        global $wpdb;
        $total_count_posts = (int) $wpdb->get_var("SELECT count(*) FROM {$wpdb->posts} WHERE post_status IN ('pending', 'draft', 'publish', 'future') AND post_type IN ( $cpt_string ) ");

        $total_count_posts = apply_filters('seopress_video_regeneration_total_count_posts', $total_count_posts);

        $increment = 1;

        $increment = apply_filters('seopress_video_regeneration_increment', $increment);

        global $post;

        if ($offset > $total_count_posts) {
            wp_reset_postdata();
            $count_items = $total_count_posts;
            $offset = 'done';
        } else {
            $args = [
                'posts_per_page' => $increment,
                'post_type'      => $cpt,
                'post_status'    => ['pending', 'draft', 'publish', 'future'],
                'offset'         => $offset,
            ];

            $args = apply_filters('seopress_video_regeneration_query', $args, $increment, $cpt, $offset);

            $video_query = get_posts($args);

            if ($video_query) {
                foreach ($video_query as $post) {
                    seopress_pro_video_xml_sitemap($post->ID, $post);
                }
            }
            $offset += $increment;
        }
        $data           = [];

        $data['total'] = $total_count_posts;

        if ($offset >= $total_count_posts) {
            $data['count'] = $total_count_posts;
        } else {
            $data['count'] = $offset;
        }

        $data['offset'] = $offset;

        //Clear cache
        delete_transient( '_seopress_sitemap_ids_video' );

        wp_send_json_success($data);
        exit();
    }
}
add_action('wp_ajax_seopress_pro_video_xml_sitemap_regenerate', 'seopress_pro_video_xml_sitemap_regenerate');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Open AI - Generate SEO metadata
///////////////////////////////////////////////////////////////////////////////////////////////////
add_action('wp_ajax_seopress_ai_generate_seo_meta', 'seopress_ai_generate_seo_meta');
function seopress_ai_generate_seo_meta() {
    check_ajax_referer('seopress_ai_generate_seo_meta_nonce');

    if ( ! current_user_can('edit_posts') && ! is_admin()) {
        return;
    }

    $data = [];

    //Get post id
    if (isset($_POST['post_id'])) {
        $post_id = (int)$_POST['post_id'];
    }

    if (empty($post_id)) {
        return;
    }

    if (isset($_POST['lang'])) {
        $language = esc_html($_POST['lang']);
    }

    $meta = '';
    if (isset($_POST['meta'])) {
        $meta = esc_html($_POST['meta']);
    }

    if( 'alt_text' == $meta ){
        $data = seopress_pro_get_service('Completions')->generateImgAltText($post_id, '', $language);
    } else {
        $data = seopress_pro_get_service('Completions')->generateTitlesDesc($post_id, $meta, $language);
    }

    if( $data['message'] !== 'Success'){
        wp_send_json_error( $data );
    } else {
        wp_send_json_success( $data ) ;
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Open AI - Check license key
///////////////////////////////////////////////////////////////////////////////////////////////////
add_action('wp_ajax_seopress_ai_check_license_key', 'seopress_ai_check_license_key');
function seopress_ai_check_license_key() {
    check_ajax_referer('seopress_ai_check_license_key_nonce');

    if ( ! current_user_can(seopress_capability('manage_options', 'ai')) && ! is_admin()) {
        return;
    }

    //Save OpenAI API key
    $options = get_option('seopress_pro_option_name');
    if (isset($_GET['seopress_ai_openai_api_key'])) {
        $options['seopress_ai_openai_api_key'] = sanitize_text_field($_GET['seopress_ai_openai_api_key']);
        update_option('seopress_pro_option_name', $options);
    }

    $data = [];

    $data = seopress_pro_get_service('Usage')->checkLicenseKeyExists();

    if ($data['code'] === 'success') {
        $data = seopress_pro_get_service('Usage')->checkLicenseKeyExpiration();
    }

    wp_send_json_success($data);
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Site Audit - Load dynamically analysis
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_site_audit_load_analysis() {
    // Check nonce
    check_ajax_referer('seopress_request_bot_nonce');

    // Check if user is in admin
    if ( ! is_admin() ) {
        return;
    }

    // Get security settings
    $options = seopress_get_service('AdvancedOption')->getOption();

    $audit_permissions = isset($options['seopress_advanced_security_metaboxe_seopress-bot-batch']) ? $options['seopress_advanced_security_metaboxe_seopress-bot-batch'] : null;

    $allowed = false;
    global $wp_roles;
    $user = wp_get_current_user();

    if (isset($user->roles) && is_array($user->roles) && !empty($user->roles)) {
        $seopress_user_role = current($user->roles);

        if ( !empty($audit_permissions) ) {
            if (array_key_exists($seopress_user_role, $audit_permissions)) {
                $allowed = true;
            }
        }
    }

    // Check if user has the capability to manage options
    if ( current_user_can(seopress_capability('manage_options', 'site-audit')) ) {
        $allowed = true;
    }

    // Check if user is allowed to access the analysis
    if ( ! $allowed ) {
        return;
    }

    //Check if GSC toggle is ON
    if (seopress_get_service('ToggleOption')->getToggleBot() !== '1') {
        return;
    }

    if (!isset($_POST['type'])) {
        wp_send_json_error('Type not provided.');
    }

    $type = sanitize_text_field($_POST['type']);

    ob_start();
    seopress_pro_get_service('SiteAudit')->renderAnalysisResults($type);
    $content = ob_get_clean();

    echo $content;
    wp_die();
}
add_action('wp_ajax_seopress_site_audit_load_analysis', 'seopress_site_audit_load_analysis');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Site Audit - Run actions
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_site_audit_run_actions() {
    // Check nonce
    check_ajax_referer('seopress_request_bot_nonce');

    // Check if user is in admin
    if ( ! is_admin() ) {
        return;
    }

    // Get security settings
    $options = seopress_get_service('AdvancedOption')->getOption();

    $audit_permissions = isset($options['seopress_advanced_security_metaboxe_seopress-bot-batch']) ? $options['seopress_advanced_security_metaboxe_seopress-bot-batch'] : null;

    $allowed = false;
    global $wp_roles;
    $user = wp_get_current_user();

    if (isset($user->roles) && is_array($user->roles) && !empty($user->roles)) {
        $seopress_user_role = current($user->roles);

        if ( !empty($audit_permissions) ) {
            if (array_key_exists($seopress_user_role, $audit_permissions)) {
                $allowed = true;
            }
        }
    }

    // Check if user has the capability to manage options
    if ( current_user_can(seopress_capability('manage_options', 'site-audit')) ) {
        $allowed = true;
    }

    // Check if user is allowed to access the analysis
    if ( ! $allowed ) {
        return;
    }

    // Validate and sanitize input
    if (!isset($_POST['issue_post_id'])) {
        wp_send_json_error('Post ID not provided.');
    }

    $issue_post_id = absint($_POST['issue_post_id']);
    if (!$issue_post_id) {
        wp_send_json_error('Invalid Post ID.');
    }

    // Retrieve data
    $data = seopress_pro_get_service('SEOIssuesDatabase')->getData($issue_post_id, ['img_alt']);

    if (empty($data) || empty($data[0])) {
        wp_send_json_error('No data found.');
    }
    
    $issue_desc = maybe_unserialize($data[0]['issue_desc']);
    if (is_array($issue_desc) && !empty($issue_desc)) {
        $results = [];
        foreach ($issue_desc as $issue) {
            $attachment_id = seopress_get_service('SearchAttachment')->searchByUrl($issue);
            
            if (!empty($attachment_id)) {
                foreach ($attachment_id as $id) {
                    $result = seopress_pro_get_service('Completions')->generateImgAltText($id, 'alt_text');
                    if (!is_wp_error($result)) {
                        $results[] = $result;
                    }
                }
            }
        }
        wp_send_json_success($results);
    } else {
        wp_send_json_error('No valid issues found.');
    }
}
add_action('wp_ajax_seopress_site_audit_run_actions', 'seopress_site_audit_run_actions');

