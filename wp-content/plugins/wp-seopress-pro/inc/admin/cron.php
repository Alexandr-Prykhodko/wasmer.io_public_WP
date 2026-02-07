<?php
defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Request Google PageSpeed Insights
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_request_page_speed_fn($cron = false)
{
    $options = get_option('seopress_pro_option_name');

    //Save URLs field
    if (isset($_POST['seopress_ps_url'])) {
        $options['seopress_ps_url'] = sanitize_textarea_field($_POST['seopress_ps_url']);
        update_option('seopress_pro_option_name', $options);
    } elseif (isset($options['seopress_ps_url'])) {
        $seopress_get_site_url = $options['seopress_ps_url'];
    } else {
        $seopress_get_site_url = get_home_url();
    }

    $options = get_option('seopress_pro_option_name');

    //Save API key
    if (isset($_POST['seopress_ps_api_key'])) {
        $options['seopress_ps_api_key'] = sanitize_text_field($_POST['seopress_ps_api_key']);
        update_option('seopress_pro_option_name', $options);
    }

    $options = get_option('seopress_pro_option_name');

    $seopress_google_api_key = ! empty($options['seopress_ps_api_key']) ? $options['seopress_ps_api_key'] : 'AIzaSyBqvSx2QrqbEqZovzKX8znGpTosw7KClHQ';
    $seopress_get_site_url = ! empty($options['seopress_ps_url']) ? $options['seopress_ps_url'] : get_home_url();

    delete_transient('seopress_results_page_speed');
    delete_transient('seopress_results_page_speed_desktop');

    $args = ['timeout' => 120, 'blocking' => true];

    if (function_exists('seopress_normalized_locale')) {
        $language = seopress_normalized_locale(get_locale());
    } else {
        $language = get_locale();
    }

    //Mobile
    if (false === ($seopress_results_page_speed_cache = get_transient('seopress_results_page_speed'))) {
        $seopress_results_page_speed = wp_remote_retrieve_body(wp_remote_get('https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=' . $seopress_get_site_url . '&key=' . $seopress_google_api_key . '&screenshot=true&strategy=mobile&category=performance&category=seo&category=best-practices&locale=' . $language, $args));
        $seopress_results_page_speed_cache = $seopress_results_page_speed;
        set_transient('seopress_results_page_speed', $seopress_results_page_speed_cache, 1 * DAY_IN_SECONDS);
    }

    //Desktop
    if (false === ($seopress_results_page_speed_desktop_cache = get_transient('seopress_results_page_speed_desktop'))) {
        $seopress_results_page_speed_desktop = wp_remote_retrieve_body(wp_remote_get('https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=' . $seopress_get_site_url . '&key=' . $seopress_google_api_key . '&screenshot=true&strategy=desktop&category=performance&locale=' . $language, $args));
        $seopress_results_page_speed_desktop_cache = $seopress_results_page_speed_desktop;
        set_transient('seopress_results_page_speed_desktop', $seopress_results_page_speed_desktop_cache, 1 * DAY_IN_SECONDS);
    }
    $data = ['url' => add_query_arg('ps', 'done', remove_query_arg(['data_permalink', 'ps'], admin_url('admin.php?page=seopress-pro-page&ps=done#tab=tab_seopress_page_speed')))];

    if ($cron === false) {
        wp_send_json_success($data);
    }
    exit();
}
/**
 * Request Page Speed Insights by CRON.
 *
 * @since 5.3
 * @param boolean Is is a CRON request?
 *
 * @author Benjamin
 */
function seopress_request_page_speed_insights_cron()
{
    seopress_request_page_speed_fn(true);
}
add_action('seopress_page_speed_insights_cron', 'seopress_request_page_speed_insights_cron');

function seopress_request_page_speed()
{
    check_ajax_referer('seopress_request_page_speed_nonce');

    if (current_user_can(seopress_capability('manage_options', 'cron')) && is_admin()) {
        seopress_request_page_speed_fn();
    }
}
add_action('wp_ajax_seopress_request_page_speed', 'seopress_request_page_speed');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Request Google Analytics
///////////////////////////////////////////////////////////////////////////////////////////////////
use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\OrderBy;
use Google\Analytics\Data\V1beta\Metric;
use Google\ApiCore\ApiException;
use Google\Auth\OAuth2;

function seopress_request_google_analytics_fn($clear = false)
{
    if (seopress_pro_get_service('GoogleAnalyticsWidgetsOptionPro')->getGA4DashboardWidget() === '1') {
        exit();
    }

    $authOption = seopress_get_service('GoogleAnalyticsOption')->getAuth();
    if (( ! empty($authOption) || ! empty(seopress_get_service('GoogleAnalyticsOption')->getGA4PropertId())) && ! empty(seopress_pro_get_service('GoogleAnalyticsOptionPro')->getAccessToken())) {
        try {
            // get saved data
            if ( ! $widget_options = get_option('seopress_ga_dashboard_widget_options')) {
                $widget_options = [];
            }

            // check if saved data contains content
            $seopress_ga_dashboard_widget_options_period = isset($widget_options['period']) ? $widget_options['period'] : false;

            $seopress_ga_dashboard_widget_options_type = isset($widget_options['type']) ? $widget_options['type'] : 'ga_sessions';

            // custom content saved by control callback, modify output
            if ($seopress_ga_dashboard_widget_options_period) {
                $period = $seopress_ga_dashboard_widget_options_period;
            } else {
                $period = '30daysAgo';
            }

            $client_id = seopress_get_service('GoogleAnalyticsOption')->getAuthClientId();
            $client_secret = seopress_get_service('GoogleAnalyticsOption')->getAuthSecretId();

            if (empty($client_id) || empty($client_secret)) {
                return;
            }

            $ga_account = 'ga:' . $authOption;
            $redirect_uri = admin_url('admin.php?page=seopress-google-analytics');

            require_once SEOPRESS_PRO_PLUGIN_DIR_PATH . '/vendor/autoload.php';

            $oauth = new OAuth2([
                'scope' => 'https://www.googleapis.com/auth/analytics.readonly',
                'tokenCredentialUri' => 'https://oauth2.googleapis.com/token',
                'authorizationUri' => 'https://accounts.google.com/o/oauth2/auth',
                'clientId' => $client_id,
                'clientSecret' => $client_secret,
                'redirectUri' => admin_url('admin.php?page=seopress-google-analytics'),
                'plugin_name' => 'SEOPress',
            ]);

            $client = new \Google\Client();
            $client->setApplicationName('Client_Library_Examples');
            $client->setClientId($client_id);
            $client->setClientSecret($client_secret);
            $client->setRedirectUri($redirect_uri);
            $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
            $client->setApprovalPrompt('force');   // mandatory to get this fucking refreshtoken
            $client->setAccessType('offline'); // mandatory to get this fucking refreshtoken
            $client->setIncludeGrantedScopes(true); // mandatory to get this fucking refreshtoken
            $client->setPrompt('consent'); // mandatory to get this fucking refreshtoken

            $client->setAccessToken(seopress_pro_get_service('GoogleAnalyticsOptionPro')->getDebug());

            if ($client->isAccessTokenExpired()) {
                $client->refreshToken(seopress_pro_get_service('GoogleAnalyticsOptionPro')->getDebug());

                $seopress_new_access_token = $client->getAccessToken(seopress_pro_get_service('GoogleAnalyticsOptionPro')->getDebug());

                $seopress_google_analytics_options = get_option('seopress_google_analytics_option_name1');
                $seopress_google_analytics_options['access_token'] = $seopress_new_access_token['access_token'] ?? null;
                $seopress_google_analytics_options['refresh_token'] = $seopress_new_access_token['refresh_token'] ?? null;
                $seopress_google_analytics_options['debug'] = $seopress_new_access_token ?? null;
                update_option('seopress_google_analytics_option_name1', $seopress_google_analytics_options, 'yes');
            }

            $service = new Google_Service_AnalyticsReporting($client);

            $oauth->setAccessToken(seopress_pro_get_service('GoogleAnalyticsOptionPro')->getAccessToken());
            $oauth->setRefreshToken(seopress_pro_get_service('GoogleAnalyticsOptionPro')->getRefreshToken());

            // GA4 Stats
            $all = [];

            //Get GA4 property ID
            $property_id = '';
            if (seopress_get_service('GoogleAnalyticsOption')->getGA4PropertId()) {
                $property_id = seopress_get_service('GoogleAnalyticsOption')->getGA4PropertId();

                //Get GA4 data
                $ga4_data = new BetaAnalyticsDataClient(['credentials' => $oauth]);
                // sessions
                $sessions = $ga4_data->runReport(
                    [
                        'property' => 'properties/' . $property_id,
                        'dateRanges' => [
                            new DateRange([
                                'start_date' => $period,
                                'end_date' => 'today',
                            ]),
                        ],
                        'dimensions' => [new Dimension([
                            'name' => 'date',
                        ]),
                        ],
                        'metrics' => [new Metric([
                            'name' => 'sessions',
                        ]),
                        ],
                        'orderBys' => [
                            new OrderBy([
                                'dimension' => new OrderBy\DimensionOrderBy([
                                    'dimension_name' => 'date',
                                    'order_type' => OrderBy\DimensionOrderBy\OrderType::ALPHANUMERIC
                                ]),
                                'desc' => false,
                            ]),
                        ],
                    ]
                );

                $users = $ga4_data->runReport(
                    [
                        'property' => 'properties/' . $property_id,
                        'dateRanges' => [
                            new DateRange([
                                'start_date' => $period,
                                'end_date' => 'today',
                            ]),
                        ],
                        'dimensions' => [new Dimension([
                            'name' => 'date',
                        ]),
                        ],
                        'metrics' => [new Metric([
                            'name' => 'newUsers',
                        ]),
                        ],
                        'orderBys' => [
                            new OrderBy([
                                'dimension' => new OrderBy\DimensionOrderBy([
                                    'dimension_name' => 'date',
                                    'order_type' => OrderBy\DimensionOrderBy\OrderType::ALPHANUMERIC
                                ]),
                                'desc' => false,
                            ]),
                        ],
                    ]
                );

                $pageviews = $ga4_data->runReport(
                    [
                        'property' => 'properties/' . $property_id,
                        'dateRanges' => [
                            new DateRange([
                                'start_date' => $period,
                                'end_date' => 'today',
                            ]),
                        ],
                        'dimensions' => [new Dimension([
                            'name' => 'date',
                        ]),
                        ],
                        'metrics' => [new Metric([
                            'name' => 'screenPageViews',
                        ]),
                        ],
                        'orderBys' => [
                            new OrderBy([
                                'dimension' => new OrderBy\DimensionOrderBy([
                                    'dimension_name' => 'date',
                                    'order_type' => OrderBy\DimensionOrderBy\OrderType::ALPHANUMERIC
                                ]),
                                'desc' => false,
                            ]),
                        ],
                    ]
                );

                $avgSessionDuration = $ga4_data->runReport(
                    [
                        'property' => 'properties/' . $property_id,
                        'dateRanges' => [
                            new DateRange([
                                'start_date' => $period,
                                'end_date' => 'today',
                            ]),
                        ],
                        'dimensions' => [new Dimension([
                            'name' => 'date',
                        ]),
                        ],
                        'metrics' => [new Metric([
                            'name' => 'averageSessionDuration',
                        ]),
                        ],
                        'orderBys' => [
                            new OrderBy([
                                'dimension' => new OrderBy\DimensionOrderBy([
                                    'dimension_name' => 'date',
                                    'order_type' => OrderBy\DimensionOrderBy\OrderType::ALPHANUMERIC
                                ]),
                                'desc' => false,
                            ]),
                        ],
                    ]
                );

                $results = [
                    'sessions' => $sessions,
                    'users' => $users,
                    'pageviews' => $pageviews,
                    'avgSessionDuration' => $avgSessionDuration
                ];

                foreach ($results as $key => $value) {
                    foreach ($value->getRows() as $row) {
                        $all[0][$key][$row->getDimensionValues()[0]->getValue()] = $row->getMetricValues()[0]->getValue();
                    }
                }
            }

            if (true === $clear) {
                delete_transient('seopress_results_google_analytics');
            }

            if (false === ($seopress_results_google_analytics_cache = get_transient('seopress_results_google_analytics'))) {
                $seopress_results_google_analytics_cache = [];

                //////GA4/////////////
                if (seopress_get_service('GoogleAnalyticsOption')->getGA4PropertId()) {
                    $seopress_results_google_analytics_cache['sessions'] = isset($all[0]['sessions']) && is_array($all[0]['sessions']) ? array_sum($all[0]['sessions']) : 0;
                    $seopress_results_google_analytics_cache['users'] = isset($all[0]['users']) && is_array($all[0]['users']) ? array_sum($all[0]['users']) : 0;
                    $seopress_results_google_analytics_cache['pageviews'] = isset($all[0]['pageviews']) && is_array($all[0]['pageviews']) ? array_sum($all[0]['pageviews']) : 0;

                    $seopress_results_google_analytics_cache['avgSessionDuration'] = 0;
                    if (isset($all[0]['avgSessionDuration']) && is_array($all[0]['avgSessionDuration'])) {
                        $sum = array_sum(array_map('floatval', $all[0]['avgSessionDuration']));
                        $divided = count($all[0]['avgSessionDuration']);
                        if ($divided === 0) {
                            $divided = 1;
                        }

                        $seopress_results_google_analytics_cache['avgSessionDuration'] = gmdate('i:s', round($sum / $divided));
                    }


                    switch ($seopress_ga_dashboard_widget_options_type) {
                        case 'ga_sessions':
                            $ga_sessions_rows = $all[0]['sessions'] ?? [];
                            $seopress_ga_dashboard_widget_options_title = __('Sessions', 'wp-seopress-pro');
                            break;
                        case 'ga_users':
                            $ga_sessions_rows = $all[0]['users'] ?? [];
                            $seopress_ga_dashboard_widget_options_title = __('Users', 'wp-seopress-pro');
                            break;
                        case 'ga_pageviews':
                            $ga_sessions_rows = $all[0]['pageviews'] ?? [];
                            $seopress_ga_dashboard_widget_options_title = __('Page Views', 'wp-seopress-pro');
                            break;
                        case 'ga_avgSessionDuration':
                            $ga_sessions_rows = $all[0]['avgSessionDuration'] ?? [];
                            $seopress_ga_dashboard_widget_options_title = __('Session Duration', 'wp-seopress-pro');
                            break;
                        default:
                            $ga_sessions_rows = $all[0]['sessions'] ?? [];
                            $seopress_ga_dashboard_widget_options_title = __('Sessions', 'wp-seopress-pro');
                    }

                    function seopress_ga_dashboard_4_get_sessions_labels($ga_date)
                    {
                        $labels = [];
                        foreach ($ga_date as $key => $value) {
                            array_push($labels, date_i18n(get_option('date_format'), strtotime($key)));
                        }

                        return $labels;
                    }

                    function seopress_ga_dashboard_4_get_sessions_data($ga_sessions_rows)
                    {
                        $data = [];
                        foreach ($ga_sessions_rows as $key => $value) {
                            array_push($data, $value);
                        }

                        return $data;
                    }
                    $seopress_results_google_analytics_cache['sessions_graph_labels'] = seopress_ga_dashboard_4_get_sessions_labels($ga_sessions_rows);
                    $seopress_results_google_analytics_cache['sessions_graph_data'] = seopress_ga_dashboard_4_get_sessions_data($ga_sessions_rows);
                    $seopress_results_google_analytics_cache['sessions_graph_title'] = $seopress_ga_dashboard_widget_options_title;
                }

                //Transient
                set_transient('seopress_results_google_analytics', $seopress_results_google_analytics_cache, 2 * HOUR_IN_SECONDS);
            }

            //Return
            $seopress_results_google_analytics_transient = get_transient('seopress_results_google_analytics');

            wp_send_json_success($seopress_results_google_analytics_transient);
        } catch (\Exception $e) {
            $error = json_encode(['error' => json_decode($e->getMessage(), true)]);
            
            set_transient('seopress_results_google_analytics', $error, 2 * HOUR_IN_SECONDS);
            
            wp_send_json($error);
        }
    }

    exit();
}
/**
 * Request GA stats by CRON.
 *
 * @since 4.2
 *
 * @author Benjamin
 */
function seopress_request_google_analytics_cron()
{
    if (function_exists('seopress_get_toggle_option') && '1' === seopress_get_toggle_option('google-analytics')) {
        seopress_request_google_analytics_fn(true);
    }
}
add_action('seopress_google_analytics_cron', 'seopress_request_google_analytics_cron');

function seopress_request_google_analytics()
{
    check_ajax_referer('seopress_request_google_analytics_nonce');
    if ((current_user_can(seopress_capability('manage_options', 'cron')) || seopress_advanced_security_ga_widget_check() === true) && is_admin()) {
        if (function_exists('seopress_get_toggle_option') && '1' === seopress_get_toggle_option('google-analytics')) {
            seopress_request_google_analytics_fn(false);
        }
    }
}
add_action('wp_ajax_seopress_request_google_analytics', 'seopress_request_google_analytics');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Request Matomo Analytics
///////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Request Matomo stats.
 *
 * @since 6.0
 *
 * @author Benjamin
 * @param mixed $clear
 */
function seopress_request_matomo_analytics_fn($clear = false)
{
    if (seopress_pro_get_service('GoogleAnalyticsWidgetsOptionPro')->getMatomoDashboardWidget() === '1') {
        exit();
    }

    //Clear cache if CRON
    if (true === $clear) {
        delete_transient('seopress_results_matomo');
    }

    if (false === ($seopress_results_matomo_cache = get_transient('seopress_results_matomo'))) {
        $seopress_results_matomo_cache = [];

        $matomoID = seopress_get_service('GoogleAnalyticsOption')->getMatomoId() ? seopress_get_service('GoogleAnalyticsOption')->getMatomoId() : null;

        if (empty($matomoID)) {
            exit();
        }

        $siteID = seopress_get_service('GoogleAnalyticsOption')->getMatomoSiteId() ? seopress_get_service('GoogleAnalyticsOption')->getMatomoSiteId() : null;

        if (empty($siteID)) {
            exit();
        }

        $authToken = seopress_get_service('GoogleAnalyticsOption')->getMatomoAuthToken() ? seopress_get_service('GoogleAnalyticsOption')->getMatomoAuthToken() : null;

        if (empty($authToken)) {
            exit();
        }

        // get saved data
        if ( ! $widget_options = get_option('seopress_matomo_dashboard_widget_options')) {
            $widget_options = [];
        }

        // check if saved data contains content
        $seopress_matomo_dashboard_widget_options_period = isset($widget_options['period']) ? $widget_options['period'] : false;

        $seopress_matomo_dashboard_widget_options_type = isset($widget_options['type']) ? $widget_options['type'] : 'nb_visits';

        // custom content saved by control callback, modify output
        if ($seopress_matomo_dashboard_widget_options_period) {
            $period = $seopress_matomo_dashboard_widget_options_period;
        } else {
            $period = 'last30';
        }

        $url = 'https://' . $matomoID;

        $body = [
            'module' => 'API',
            'method' => 'API.getProcessedReport',
            'idSite' => $siteID,
            'date' => $period,
            'period' => 'day',
            'apiModule' => 'VisitsSummary',
            'apiAction' => 'get',
            'format' => 'json',
            'token_auth' => $authToken,
            'filter_truncate' => 5,
            'language' => 'en'
        ];

        $args = [
            'blocking' => true,
            'timeout' => 10,
            'sslverify' => false,
            'body' => $body
        ];

        $response = wp_remote_post($url, $args);

        //Check for error
        if ( ! is_wp_error($response)) {
            $response = wp_remote_retrieve_body($response);
            $response = json_decode($response, true);
        }

        switch ($seopress_matomo_dashboard_widget_options_type) {
            case 'nb_uniq_visitors':
                $widget_title = __('Unique visitors', 'wp-seopress-pro');
                break;
            case 'nb_visits':
                $widget_title = __('Visits', 'wp-seopress-pro');
                break;
            case 'max_actions':
                $widget_title = __('Maximum actions in one visit', 'wp-seopress-pro');
                break;
            case 'nb_actions_per_visit':
                $widget_title = __('Average actions per visit', 'wp-seopress-pro');
                break;
            case 'bounce_rate':
                $widget_title = __('Bounce Rate', 'wp-seopress-pro');
                break;
            case 'avg_time_on_site':
                $widget_title = __('Avg. Visit Duration (in seconds)', 'wp-seopress-pro');
                break;
            default:
                $widget_title = __('Unique visitors', 'wp-seopress-pro');
        }

        function seopress_matomo_get_sessions_labels($rows)
        {
            $labels = [];

            if (is_array($rows) && isset($rows['reportMetadata'])) {
                $rows = $rows['reportMetadata'];
                foreach ($rows as $key => $value) {
                    if (isset($key)) {
                        $labels[] = date_i18n(get_option('date_format'), strtotime($key));
                    } else {
                        $labels[] = 0;
                    }
                }
            }
            return $labels;
        }

        function seopress_matomo_get_sessions_data($rows, $seopress_matomo_dashboard_widget_options_type)
        {
            $data = [];
            if (is_array($rows) && isset($rows['reportData'])) {
                $rows = array_values($rows['reportData']);
                foreach ($rows as $key => $value) {
                    if (isset($value[$seopress_matomo_dashboard_widget_options_type])) {
                        //Bounce rate: remove %
                        if ($seopress_matomo_dashboard_widget_options_type === 'bounce_rate') {
                            $value[$seopress_matomo_dashboard_widget_options_type] = rtrim($value[$seopress_matomo_dashboard_widget_options_type], '%');
                        }

                        //Average time: convert to seconds
                        if ($seopress_matomo_dashboard_widget_options_type === 'avg_time_on_site') {
                            $value[$seopress_matomo_dashboard_widget_options_type] = strtotime("1970-01-01 $value[$seopress_matomo_dashboard_widget_options_type] UTC");
                        }

                        $data[] = $value[$seopress_matomo_dashboard_widget_options_type] ? $value[$seopress_matomo_dashboard_widget_options_type] : 0;
                    } else {
                        $data[] = 0;
                    }
                }
            }
            return $data;
        }

        function seopress_timestamp_to_seconds($n)
        {
            return strtotime("1970-01-01 $n UTC");
        }

        function seopress_remove_pourcentage($n)
        {
            return rtrim($n, '%');
        }

        function seopress_matomo_get_all_data($rows)
        {
            $data = [];
            $rows = $rows['reportData'];

            if ( ! is_array($rows)) {
                return $data;
            }

            if (empty($rows)) {
                return $data;
            }

            //Unique Visitors
            $data['nb_uniq_visitors'] = array_sum(array_column($rows, 'nb_uniq_visitors'));

            //Visits
            $data['nb_visits'] = array_sum(array_column($rows, 'nb_visits'));

            //Max actions
            $max_actions = array_column($rows, 'max_actions');
            $data['max_actions'] = !empty($max_actions) ? max($max_actions) : null;

            //Actions per visit
            $data['nb_actions_per_visit'] = array_column($rows, 'nb_actions_per_visit');
            $count = count($data['nb_actions_per_visit']);
            if ($count > 1) {
                $data['nb_actions_per_visit'] = round(array_sum($data['nb_actions_per_visit']) / $count, 2);
            } else {
                $data['nb_actions_per_visit'] = $data['nb_actions_per_visit'][0];
            }

            //Bounce rate
            $data['bounce_rate'] = array_map('seopress_remove_pourcentage', array_column($rows, 'bounce_rate'));
            $count = count($data['bounce_rate']);
            if ($count > 1) {
                $data['bounce_rate'] = round(array_sum($data['bounce_rate']) / $count, 2);
            } else {
                $data['bounce_rate'] = $data['bounce_rate'][0];
            }

            //Avg. Visit Duration
            $data['avg_time_on_site'] = array_map('seopress_timestamp_to_seconds', array_column($rows, 'avg_time_on_site'));
            $count = count($data['avg_time_on_site']);
            if ($count > 1) {
                $data['avg_time_on_site'] = round(array_sum($data['avg_time_on_site']) / $count, 2);
            } else {
                $data['avg_time_on_site'] = $data['avg_time_on_site'][0];
            }

            return $data;
        }

        if ( ! is_wp_error($response)) {
            $response['sessions_graph_labels'] = seopress_matomo_get_sessions_labels($response);
            $response['sessions_graph_data'] = seopress_matomo_get_sessions_data($response, $seopress_matomo_dashboard_widget_options_type);
            $response['sessions_graph_title'] = $widget_title;
            $response['all'] = seopress_matomo_get_all_data($response);

            //Transient
            set_transient('seopress_results_matomo', $response, 2 * HOUR_IN_SECONDS);
        }
    }
    //Return
    $seopress_results_matomo_transient = get_transient('seopress_results_matomo');

    wp_send_json_success($seopress_results_matomo_transient);
    exit();
}
/**
 * Request Matomo Analytics by CRON.
 *
 * @since 6.0
 * @param boolean Is is a CRON request?
 *
 * @author Benjamin
 */
function seopress_request_matomo_analytics_cron()
{
    if (function_exists('seopress_get_toggle_option') && '1' === seopress_get_toggle_option('google-analytics')) {
        seopress_request_matomo_analytics_fn(true);
    }
}
add_action('seopress_matomo_analytics_cron', 'seopress_request_matomo_analytics_cron');

function seopress_request_matomo_analytics()
{
    check_ajax_referer('seopress_request_matomo_analytics_nonce');

    if ((current_user_can(seopress_capability('manage_options', 'cron')) || seopress_advanced_security_matomo_widget_check() === true) && is_admin()) {
        if (function_exists('seopress_get_toggle_option') && '1' === seopress_get_toggle_option('google-analytics')) {
            seopress_request_matomo_analytics_fn();
        }
    }
}
add_action('wp_ajax_seopress_request_matomo_analytics', 'seopress_request_matomo_analytics');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Send 404 weekly email notifications
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_404_send_alert()
{
    $to = seopress_pro_get_service('OptionPro')->get404RedirectEnableMailsFrom();
    $subject = /* translators: %s name of the site from General settings */ sprintf(__('404 alert - %s', 'wp-seopress-pro'), get_bloginfo('name'));
    $content = '';

    // Get the Latest 404 errors
    $args = [
        'date_query' => [
            [
                'column' => 'post_date_gmt',
                'after' => '1 week ago',
            ],
        ],
        'posts_per_page' => 10,
        'post_type' => 'seopress_404',
        'meta_key' => '_seopress_redirections_type',
        'meta_compare' => 'NOT EXISTS',
    ];

    $args = apply_filters('seopress_404_email_alerts_latest_query', $args);

    $latest_404_query = new WP_Query($args);

    if ($latest_404_query->have_posts()) {
        $errors['latest'] = [];
        while ($latest_404_query->have_posts()) {
            $latest_404_query->the_post();

            $errors['latest'][] = ['url' => get_the_title(), 'count' => get_post_meta(get_the_ID(), 'seopress_404_count', true)];
        }
        wp_reset_postdata();
    }

    if ( ! empty($errors['latest'])) {
        $content .= '<h2>' . __('Latest 404 errors since 1 week', 'wp-seopress-pro') . '</h2>';
        $content .= '<ul>';
        foreach ($errors['latest'] as $error) {
            $hits = ! empty($error['count']) ? ' - ' . $error['count'] . __(' Hits', 'wp-seopress-pro') : '';
            $content .= '<li>' . get_home_url() . '/' . $error['url'] . $hits . '</li>';
        }
        $content .= '</ul>';
    }

    // Get the top 404 errors
    $args = [
        'posts_per_page' => 10,
        'post_type' => 'seopress_404',
        'meta_key' => 'seopress_404_count',
        'meta_query' => [
            'relation' => 'AND',
            [
                'key' => 'seopress_404_count',
                'compare' => 'EXISTS',
                'type' => 'NUMERIC'
            ],
            [
                'key' => '_seopress_redirections_type',
                'compare' => 'NOT EXISTS',
            ],
        ],
        'order' => 'DESC',
        'orderby' => 'meta_value_num',
    ];

    $args = apply_filters('seopress_404_email_alerts_top_query', $args);

    $top_404_query = new WP_Query($args);

    if ($top_404_query->have_posts()) {
        $errors['top'] = [];
        while ($top_404_query->have_posts()) {
            $top_404_query->the_post();

            $errors['top'][] = ['url' => get_the_title(), 'count' => get_post_meta(get_the_ID(), 'seopress_404_count', true)];
        }
        wp_reset_postdata();
    }

    if ( ! empty($errors['top'])) {
        $content .= '<h2>' . __('Top 404 errors', 'wp-seopress-pro') . '</h2>';
        $content .= '<ul>';
        foreach ($errors['top'] as $error) {
            $hits = ! empty($error['count']) ? ' - ' . $error['count'] . __(' Hits', 'wp-seopress-pro') : '';
            $content .= '<li>' . get_home_url() . '/' . $error['url'] . $hits . '</li>';
        }
        $content .= '</ul>';
    }

    if (!empty($content)) {
        // Use the new EmailService
        $email_service = \SEOPress\Services\Email\EmailService::get_instance();
        
        // Prepare email arguments
        $email_args = [
            'header_subject' => $subject,
            'footer_text'  => sprintf(
                /* translators: %s site name */
                esc_html__('Sent from %s', 'wp-seopress-pro'),
                get_bloginfo('name')
            ),
            'text_color'   => '#505050',
            'bg_color'     => '#F9F9F9',
        ];

        // Add the view all 404 errors button
        $content .= '<p style="text-align: center;"><a class="button" href="' . get_home_url() . '/wp-admin/edit.php?post_type=seopress_404&action=-1&m=0&redirect-cat=0&redirection-type=404&redirection-enabled&filter_action=Filter&paged=1&action2=-1&post_status=404">' . __('View all 404 errors', 'wp-seopress-pro') . '</a></p>';

        // Send the email
        $email_service->send($to, $subject, $content, $email_args);
    }
}

/**
 * Send 404 email alerts by CRON.
 *
 * @since 6.3
 *
 * @author Benjamin
 */
function seopress_404_send_alert_cron()
{
    if ((function_exists('seopress_get_toggle_option') && '1' === seopress_get_toggle_option('404')) && '1' === seopress_pro_get_service('OptionPro')->get404RedirectEnableMails() && '' !== seopress_pro_get_service('OptionPro')->get404RedirectEnableMailsFrom()) {
        seopress_404_send_alert();
    }
}
add_action('seopress_404_email_alerts_cron', 'seopress_404_send_alert_cron');

///////////////////////////////////////////////////////////////////////////////////////////////////
// 404 Cleaning CRON
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_404_cron_cleaning_action($force = false)
{
    if ('1' === seopress_pro_get_service('OptionPro')->get404Cleaning() || true === $force) {
        $args = [
            'date_query' => [
                [
                    'column' => 'post_date_gmt',
                    'before' => '1 month ago',
                ],
            ],
            'posts_per_page' => -1,
            'post_type' => 'seopress_404',
            'meta_key' => '_seopress_redirections_type',
            'meta_compare' => 'NOT EXISTS',
        ];

        $args = apply_filters('seopress_404_cleaning_query', $args);

        // The Query
        $old_404_query = new WP_Query($args);

        // The Loop
        if ($old_404_query->have_posts()) {
            while ($old_404_query->have_posts()) {
                $old_404_query->the_post();
                wp_delete_post(get_the_ID(), true);
            }
            /* Restore original Post Data */
            wp_reset_postdata();
        }
    }
}
add_action('seopress_404_cron_cleaning', 'seopress_404_cron_cleaning_action', 10, 1);

///////////////////////////////////////////////////////////////////////////////////////////////////
//Daily Get Insights from Google Search Console
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_get_insights_gsc_cron()
{
    //Check if GSC toggle is ON
    if (seopress_get_service('ToggleOption')->getToggleInspectUrl() !== '1') {
        return;
    }

    //Get Google API Key
    $options = get_option('seopress_instant_indexing_option_name');
    $google_api_key = isset($options['seopress_instant_indexing_google_api_key']) ? $options['seopress_instant_indexing_google_api_key'] : '';

    if (empty($google_api_key)) {
        return;
    }

    try {
        $service = seopress_pro_get_service('SearchConsole');

        $response = $service->handle();
        if ($response['status'] === 'error') {
            return;
        }

        foreach ($response['data'] as $row) {
            $result = $service->saveDataFromRowResult($row);
        }
    } catch (\Exception $e) {
        // No need to do anything here
    }
}
add_action('seopress_insights_gsc_cron', 'seopress_get_insights_gsc_cron');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Twice Daily send emails / Slack SEO alerts
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_send_alerts_cron()
{
    //Check if SEO Alerts toggle is ON
    if (seopress_get_service('ToggleOption')->getToggleAlerts() !== '1') {
        return;
    }

    //Check if email/slack webhook are set
    if (empty(seopress_pro_get_service('OptionPro')->getSEOAlertsRecipients()) && empty(seopress_pro_get_service('OptionPro')->getSEOAlertsSlackWebhookUrl())) {
        return;
    }

    //Init
    $alerts = [];
    $alerts['noindex'] = false;
    $alerts['robots'] = false;
    $alerts['xml_sitemaps'] = false;


    //Check noindex on homepage
    if (seopress_pro_get_service('OptionPro')->getSEOAlertsNoIndex() === '1') {
        $alerts['noindex'] = false;

        $args = [
            'blocking' => true,
            'timeout' => 10,
            'redirection' => 1,
        ];

        $args = apply_filters('seopress_seo_alerts_homepage_args', $args);

        $response = wp_remote_get(get_home_url(), $args);

        if ( ! is_wp_error($response)) {
            $body = wp_remote_retrieve_body($response);

            // Load HTML into DOMDocument
            $dom = new DOMDocument();
            libxml_use_internal_errors(true); // Suppress errors for malformed HTML
            if ($dom->loadHTML('<?xml encoding="utf-8" ?>' . $body)) {
                $xpath = new DOMXPath($dom);

                // Find meta robots tag
                $metaRobots = $xpath->query('//meta[@name="robots"]');

                if ($metaRobots->length > 0) {
                    $content = $metaRobots->item(0)->getAttribute('content');
                    if (strpos($content, 'noindex') !== false) {
                        // "noindex" found
                        $alerts['noindex'] = true;
                    }
                }
            }
            libxml_clear_errors(); // Clear libxml errors
        }
    }

    //Check robots.txt file
    if (seopress_pro_get_service('OptionPro')->getSEOAlertsRobotsTxt() === '1') {
        $alerts['robots'] = false;

        $args = [
            'blocking' => true,
            'timeout' => 10,
            'redirection' => 1,
        ];

        $args = apply_filters('seopress_seo_alerts_robots_args', $args);

        $test = wp_remote_retrieve_response_code(wp_remote_get(get_home_url() . '/robots.txt', $args));

        if (is_wp_error($test) || 200 !== $test) {
            $alerts['robots'] = true;
        }
    }

    //Check XML sitemaps file
    if (seopress_pro_get_service('OptionPro')->getSEOAlertsXMLSitemaps() === '1') {
        $alerts['xml_sitemaps'] = false;

        $args = [
            'blocking' => true,
            'timeout' => 10,
            'redirection' => 1,
        ];

        $args = apply_filters('seopress_seo_alerts_sitemap_args', $args);

        $test = wp_remote_retrieve_response_code(wp_remote_get(get_home_url() . '/sitemaps.xml', $args));

        if (is_wp_error($test) || 200 !== $test) {
            $alerts['xml_sitemaps'] = true;
        }
    }

    //Email alerts
    if ( ! empty(seopress_pro_get_service('OptionPro')->getSEOAlertsRecipients())) {
        if ($alerts['noindex'] === true || $alerts['robots'] === true || $alerts['xml_sitemaps'] === true) {
            $to = seopress_pro_get_service('OptionPro')->getSEOAlertsRecipients();
            $subject = /* translators: %s name of the site from General settings */ sprintf(__('SEO Alerts - %s', 'wp-seopress-pro'), get_bloginfo('name'));
            $content = '';

            if ( ! empty($alerts)) {
                $content .= '<ul>';

                if ($alerts['noindex'] === true) {
                    $content .= '<li>' . sprintf(
                        /* translators: %1$s homepage URL, %2$s homepage URL */ wp_kses_post(__('⚠️ Your <strong>homepage</strong> has a <code>noindex</code> meta robots. Please check it at <a href="%1$s" target="_blank">%2$s</a>', 'wp-seopress-pro')),
                        esc_url(get_home_url()),
                        esc_url(get_home_url())
                    ) . '</li>';
                }
                if ($alerts['robots'] === true) {
                    $content .= '<li>' . sprintf(
                        /* translators: %1$s robots.txt URL, %2$s robots.txt URL */ wp_kses_post(__('⚠️ Your <code>robots.txt</code> file returns an error. Please check it at <a href="%1$s" target="_blank">%2$s</a>', 'wp-seopress-pro')),
                        esc_url(get_home_url() . '/robots.txt'),
                        esc_url(get_home_url() . '/robots.txt')
                    ) . '</li>';
                }
                if ($alerts['xml_sitemaps'] === true) {
                    $content .= '<li>' . sprintf(
                        /* translators: %1$s XML sitemap URL, %2$s XML sitemap URL */ wp_kses_post(__('⚠️ Your <strong>XML sitemap</strong> returns an error. Please check your index sitemap at <a href="%1$s" target="_blank">%2$s</a>', 'wp-seopress-pro')),
                        esc_url(get_home_url() . '/sitemaps.xml'),
                        esc_url(get_home_url() . '/sitemaps.xml')
                    ) . '</li>';
                }

                $content .= '</ul>';
            }

            // Add notification management link
            $content .= '<p>' . sprintf(
                /* translators: %s manage notifications URL */
                wp_kses_post(__('You are receiving this email because SEO alerts are enabled on your WordPress site. <a href="%s">Manage notifications</a>', 'wp-seopress-pro')),
                esc_url(admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_alerts'))
            ) . '</p>';

            // Use the new EmailService
            $email_service = \SEOPress\Services\Email\EmailService::get_instance();
            
            // Prepare email arguments
            $email_args = [
                'header_subject' => $subject,
                'footer_text'  => sprintf(
                    /* translators: %s site name */
                    esc_html__('Sent from %s', 'wp-seopress-pro'),
                    get_bloginfo('name')
                ),
                'text_color'   => '#505050',
                'bg_color'     => '#F9F9F9',
            ];

            if ( ! empty($content)) {
                $email_service->send($to, $subject, $content, $email_args);
            }
        }
    }

    //Slack alerts
    if ( ! empty(seopress_pro_get_service('OptionPro')->getSEOAlertsSlackWebhookUrl())) {
        if ($alerts['noindex'] === true || $alerts['robots'] === true || $alerts['xml_sitemaps'] === true) {
            $title = '🔔 ' . __('SEO Alerts', 'wp-seopress-pro');

            $body = [
                'blocks' => [
                    [
                        'type' => 'header',
                        'text' => [
                            'type' => 'plain_text',
                            'text' => $title,
                            'emoji' => true
                        ]
                    ]
                ]
            ];
            if ($alerts['noindex'] === true) {
                $body['blocks'][] =
                [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => '⚠️ Your *homepage* has a `noindex` meta robots. Please check it at ' . get_home_url()
                    ]
                ];
            }
            if ($alerts['robots'] === true) {
                $body['blocks'][] =
                [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => '⚠️ Your `robots.txt` file returns an error. Please check it at ' . get_home_url() . '/robots.txt'
                    ]
                ];
            }
            if ($alerts['xml_sitemaps'] === true) {
                $body['blocks'][] =
                [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => '⚠️ Your *XML sitemap* returns an error. Please check your index sitemap at ' . get_home_url() . '/sitemaps.xml'
                    ]
                ];
            }

            $args = [
                'method' => 'POST',
                'headers' => [
                    'Content-type' => 'application/json'
                ],
                'user-agent' => 'WordPress/' . get_bloginfo('version'),
                'timeout' => 15,
                'sslverify' => false,
                'body' => wp_json_encode($body)
            ];

            wp_remote_post(seopress_pro_get_service('OptionPro')->getSEOAlertsSlackWebhookUrl(), $args);
        }
    }
}
add_action('seopress_alerts_cron', 'seopress_send_alerts_cron');

///////////////////////////////////////////////////////////////////////////////////////////////////
// Site Audit - Run Task
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_site_audit_run_task_fn($offset = 0, $new = false) {
    // Check if GSC toggle is ON
    if (seopress_get_service('ToggleOption')->getToggleBot() !== '1') {
        return;
    }

    // Get post types
    $postTypes = seopress_get_service('WordPressData')->getPostTypes();
    if (empty($postTypes)) {
        return;
    }

    // Get post types for the query
    $cpt = array_keys($postTypes);

    // Get post types from the Audit settings if available
    $cpt = seopress_pro_get_service('OptionBot')->getBotScanSettingsAuditCPT() ? array_keys(seopress_pro_get_service('OptionBot')->getBotScanSettingsAuditCPT()) : $cpt;

    // Get current offset and batch size
    $batch_size = 50;

    // CPT status
    $cpt_status = ['publish'];

    // Update last scan date
	if ($new === true) {
		delete_option('seopress_pro_site_audit_offset');
		delete_option('seopress_pro_site_audit_count_posts');
		delete_option('seopress_pro_site_audit_post_count');
    	update_option('seopress_pro_site_audit_last_scan', current_time('timestamp', true),false);
	}

    // If no offset was passed, fetch the saved one
    if ((int)$offset === 0) {
        $offset = get_option('seopress_pro_site_audit_offset', 0);
    }

    // Query the posts
    $args = [
        'posts_per_page' => $batch_size,
        'offset'         => (int)$offset,
        'post_type'      => $cpt,
        'post_status'    => $cpt_status,
        'fields'         => 'ids'
    ];

    // Apply filters if needed
    $args = apply_filters('seopress_site_audit_query', $args, $batch_size, $offset, $cpt, $cpt_status);

    // Fetch posts
    $postslist = get_posts($args);

    // Check if there are posts to process
    if (!empty($postslist)) {
        // Update total post count once (if needed)
        if ($offset === 0) {
            global $wpdb;

            $cpt = array_map(function($item) {
                return "'" . esc_sql($item) . "'";
            }, $cpt);

            $cpt_string = implode(",", $cpt);

            $cpt_status = array_map(function($item) {
                return "'" . esc_sql($item) . "'";
            }, $cpt_status);

            $cpt_status_string = implode(",", $cpt_status);

            $sql = "SELECT count(*) FROM {$wpdb->posts} WHERE post_status IN ($cpt_status_string) AND post_type IN ($cpt_string)";
            $total_count_posts = (int)$wpdb->get_var($sql);

            update_option('seopress_pro_site_audit_count_posts', $total_count_posts, false);
        }

        $post_count = get_option('seopress_pro_site_audit_post_count') ? (int)get_option('seopress_pro_site_audit_post_count') : 1;

        foreach ($postslist as $post_id) {
            $is_running = get_option('seopress_pro_site_audit_running', 0);

            if ($is_running === "0") {
                // Task was canceled, break the loop
                break;
            }

            // Save current offset
            update_option('seopress_pro_site_audit_offset', $offset, false);

            // Ignore issue marked as ignore
            $is_ignore = seopress_pro_get_service('SEOIssuesDatabase')->getData($post_id, ['issue_ignore']);
            if (!empty($is_ignore)) {
                continue;
            }

            // Skip if noindex is enabled for this post
            if (get_post_meta($post_id, '_seopress_robots_index', true) === 'yes' && seopress_pro_get_service('OptionBot')->getBotScanSettingsAuditNoindex() !== '1') {
                continue;
            }

            // Skip if redirections are enabled for this post
            if ('yes' == get_post_meta($post_id, '_seopress_redirections_enabled', true)) {
                continue;
            }

            // Process post audit logic
            $domResult = seopress_get_service('RequestPreview')->getDomById($post_id, null);

            if (!$domResult['success']) {
                continue;
            }

            $str = $domResult['body'];

            $data = seopress_get_service('DomFilterContent')->getData($str, $post_id);
            $data = seopress_get_service('DomAnalysis')->getDataAnalyze($data, ["id" => $post_id]);

            // Save analysis data
            $post = get_post($post_id);
            $score = seopress_get_service('DomAnalysis')->getScore($post);
            $data['score'] = $score;
            $keywords = seopress_get_service('DomAnalysis')->getKeywords(['id' => $post_id]);
            seopress_get_service('ContentAnalysisDatabase')->saveData($post_id, $data, $keywords);
            seopress_get_service('GetContentAnalysis')->getAnalyzes($post);

            // Re-enable QM if disabled
            remove_filter('user_has_cap', 'seopress_disable_qm', 10, 3);

            // Increment current post count
            update_option('seopress_pro_site_audit_post_count', $post_count++, false);
        }

        // Update offset for the next batch
        $new_offset = $offset + $batch_size;
        update_option('seopress_pro_site_audit_offset', $new_offset, false);

        if ((int)get_option('seopress_pro_site_audit_running', 1) === 1) {
            // Schedule the next batch with a slight delay (e.g., 5 seconds)
            wp_schedule_single_event(time() + 5, 'seopress_site_audit_run_task_cron', [$new_offset]);
        } else {
            wp_send_json_success('Site audit task canceled.');
        }
    } else {
        // No more posts to process, mark the task as finished
        update_option('seopress_pro_site_audit_running', 0, false);
        $start_time = get_option('seopress_pro_site_audit_last_scan');
        update_option('seopress_pro_site_audit_scan_duration', current_time('timestamp', true) - $start_time, false);

        //Send an email notification
        do_action('seopress_site_audit_after_processs');

        wp_send_json_success('Site audit task completed.');
    }
}

// Trigger via AJAX
add_action('wp_ajax_seopress_site_audit_run_task', 'seopress_site_audit_run_task');
function seopress_site_audit_run_task() {
    check_ajax_referer('seopress_request_bot_nonce');

    if (current_user_can(seopress_capability('manage_options', 'site-audit')) && is_admin()) {
        update_option('seopress_pro_site_audit_running', 1, false);

        seopress_site_audit_run_task_fn(0, true);
    }
}

// Hook the cron event
add_action('seopress_site_audit_run_task_cron', 'seopress_site_audit_run_task_fn', 10, 1);

///////////////////////////////////////////////////////////////////////////////////////////////////
// Site Audit - Email notification
///////////////////////////////////////////////////////////////////////////////////////////////////
add_action('seopress_site_audit_after_processs', 'seopress_site_audit_send_email');
function seopress_site_audit_send_email() {
    $to = seopress_pro_get_service('OptionBot')->getBotScanSettingsEmail() ? seopress_pro_get_service('OptionBot')->getBotScanSettingsEmail() : null;
    $subject = /* translators: %s name of the site from General settings */ sprintf(__('Site audit completed - %s', 'wp-seopress-pro'), get_bloginfo('name'));
    $scan_duration = get_option('seopress_pro_site_audit_scan_duration') ? get_option('seopress_pro_site_audit_scan_duration') : null;

    if ($scan_duration) {
        $scan_duration_minutes = floor($scan_duration / 60);
        $scan_duration_seconds = $scan_duration % 60;
    }

    $content = '<p>' . esc_html__('We have finished auditing your site\'s SEO.', 'wp-seopress-pro') . '</p>';
    $content .= '<p>' . sprintf(
        wp_kses_post(/* translators: %1$s duration in minutes, %2$s duration in seconds */ __('<strong>Scan duration:</strong> %1$s minutes %2$s seconds', 'wp-seopress-pro')),
        esc_html($scan_duration_minutes),
        esc_html($scan_duration_seconds)
    ) . '</p>';
    $content .= '<p>' . esc_html__('Find all the details of the analysis by clicking on the following link:', 'wp-seopress-pro') . '</p>';
    $content .= '<p><a href="' . esc_url(admin_url('admin.php?page=seopress-bot-batch#tab=tab_seopress_audit')) . '" title="' . esc_html__('Read the audit', 'wp-seopress-pro') . '">' . esc_html__('Read the site audit', 'wp-seopress-pro') . '</a></p>';

    // Use the new EmailService
    $email_service = \SEOPress\Services\Email\EmailService::get_instance();
    
    // Prepare email arguments
    $email_args = [
        'header_subject' => $subject,
        'footer_text'  => sprintf(
            /* translators: %s site name */
            esc_html__('Sent from %s', 'wp-seopress-pro'),
            get_bloginfo('name')
        ),
        'text_color'   => '#505050',
        'bg_color'     => '#F9F9F9',
    ];

    if (!empty($content) && $to !== null) {
        $email_service->send($to, $subject, $content, $email_args);
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////
// Site Audit - Cancel Task
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_site_audit_cancel_task() {
    check_ajax_referer('seopress_request_bot_nonce');

    if (current_user_can(seopress_capability('manage_options', 'site-audit')) && is_admin()) {
        update_option('seopress_pro_site_audit_running', 0, false);

        // Clear all scheduled hooks for the task
        $crons = _get_cron_array();
        foreach ($crons as $timestamp => $cron) {
            if (isset($cron['seopress_site_audit_run_task_cron'])) {
                foreach ($cron['seopress_site_audit_run_task_cron'] as $hook => $details) {
                    wp_unschedule_event($timestamp, 'seopress_site_audit_run_task_cron', $details['args']);
                }
            }
        }

        wp_send_json_success('Site audit task canceled.');
    }
}
add_action('wp_ajax_seopress_site_audit_cancel_task', 'seopress_site_audit_cancel_task');

///////////////////////////////////////////////////////////////////////////////////////////////////
// Site Audit - Task Progress
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_site_audit_get_task_progress() {
    check_ajax_referer('seopress_request_bot_nonce');

    if (current_user_can(seopress_capability('manage_options', 'site-audit')) && is_admin()) {
        $progress = get_option('seopress_pro_site_audit_post_count', 0);
        $running = get_option('seopress_pro_site_audit_running', 0);

        wp_send_json_success(array(
            'progress' => (int)$progress,
            'running'  => (int)$running
        ));
    }
}
add_action('wp_ajax_seopress_site_audit_get_task_progress', 'seopress_site_audit_get_task_progress');

///////////////////////////////////////////////////////////////////////////////////////////////////
// Site Audit - Ignore Issue
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_site_audit_ignore_issue() {
    check_ajax_referer('seopress_request_bot_nonce');
    
    if (current_user_can(seopress_capability('manage_options', 'site-audit')) && is_admin()) {
        if (isset($_POST['issue_post_id']) && isset($_POST['issue_type'])) {
            $data = seopress_pro_get_service('SEOIssuesDatabase')->getData(absint($_POST['issue_post_id']), [esc_attr($_POST['issue_type'])]);
            if (!empty($data) && !empty($data[0])) {
                $data[0]['issue_ignore'] = 1;
                seopress_pro_get_service('SEOIssuesRepository')->updateSEOIssue(absint($_POST['issue_post_id']), $data[0]);
                wp_send_json_success('Issue ignored.');
            }
        }
    }
}
add_action('wp_ajax_seopress_site_audit_ignore_issue', 'seopress_site_audit_ignore_issue');