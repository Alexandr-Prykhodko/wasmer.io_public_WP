<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//Google Analytics Dashboard widget
//=================================================================================================
if ('1' == seopress_get_toggle_option('google-analytics') && '1' !== seopress_pro_get_service('GoogleAnalyticsWidgetsOptionPro')->getGA4DashboardWidget()) {
    if (seopress_advanced_security_ga_widget_check() === true) {
        add_action('wp_dashboard_setup', 'seopress_ga_dashboard_widget');

        function seopress_ga_dashboard_widget() {
            $return_false = '';
            $return_false = apply_filters('seopress_ga_dashboard_widget', $return_false);

            if (has_filter('seopress_ga_dashboard_widget') && false == $return_false) {
                //do nothing
            } else {
                wp_add_dashboard_widget('seopress_ga_dashboard_widget', 'Google Analytics', 'seopress_ga_dashboard_widget_display', 'seopress_ga_dashboard_widget_handle');
            }
        }

        function seopress_ga_dashboard_widget_display() {
            if ((!empty(seopress_get_service('GoogleAnalyticsOption')->getAuth()) || !empty(seopress_get_service('GoogleAnalyticsOption')->getGA4PropertId())) && !empty(seopress_pro_get_service('GoogleAnalyticsOptionPro')->getAccessToken())) {
                $seopress_results_google_analytics_cache = get_transient('seopress_results_google_analytics');
                is_string($seopress_results_google_analytics_cache) ? $seopress_results_google_analytics_cache = json_decode($seopress_results_google_analytics_cache, true) : $seopress_results_google_analytics_cache;
                
                //GA4
                if (!empty(seopress_get_service('GoogleAnalyticsOption')->getGA4PropertId()) && !empty(seopress_pro_get_service('GoogleAnalyticsOptionPro')->getAccessToken()) && !empty($seopress_results_google_analytics_cache)) {
                    if (isset($seopress_results_google_analytics_cache['error']) && !empty($seopress_results_google_analytics_cache['error'])) {
                        global $pagenow;
                        ?>
                        <a class="<?php if ('index.php' == $pagenow) { echo 'button'; } else { echo 'seopress-btn'; }; ?>" href="<?php echo admin_url('admin.php?page=seopress-google-analytics#tab=tab_seopress_google_analytics_enable'); ?>">
                            <?php _e('Check error logs', 'wp-seopress-pro'); ?>
                        </a>
                        <span class="spinner"></span>
                        <?php 
                    } else {
                        ?>
                        <span class="spinner"></span>

                        <!-- Line Chart -->
                        <div class="wrap-chart-stat">
                            <canvas id="seopress_ga_dashboard_widget_sessions" width="400" height="250"></canvas>
                            <script>var ctxseopress = document.getElementById("seopress_ga_dashboard_widget_sessions");</script>
                        </div>
                        <?php
                        ?>
                        <div id="seopress-tabs2">
                            <div id="sp-tabs-1" class="seopress-summary-items">
                                <!-- //Sessions -->
                                <div class="seopress-summary-item">
                                    <div class="seopress-summary-item-label">
                                        <?php _e('Sessions', 'wp-seopress-pro'); ?>
                                    </div>
                                    <div id="seopress-ga-sessions" class="seopress-summary-item-data"></div>
                                </div>

                                <!-- //Users -->
                                <div class="seopress-summary-item">
                                    <div class="seopress-summary-item-label">
                                        <?php _e('Users', 'wp-seopress-pro'); ?>
                                    </div>
                                    <div id="seopress-ga-users" class="seopress-summary-item-data"></div>
                                </div>

                                <!-- //Page -->
                                <div class="seopress-summary-item">
                                    <div class="seopress-summary-item-label">
                                        <?php _e('Page Views', 'wp-seopress-pro'); ?>
                                    </div>
                                    <div id="seopress-ga-pageviews" class="seopress-summary-item-data"></div>
                                </div>

                                <!-- //Average session duration -->
                                <div class="seopress-summary-item">
                                    <div class="seopress-summary-item-label">
                                        <?php _e('Average session duration', 'wp-seopress-pro'); ?>
                                    </div>
                                    <div id="seopress-ga-avgSessionDuration" class="seopress-summary-item-data"></div>
                                </div>
                            </div>
                        </div>
                        <?php 
                    }
                }
            } else {
                global $pagenow;
                ?>
                <div class="seopress-tools-card">
                    <p>
                        <?php _e('You need to login to Google Analytics.', 'wp-seopress-pro'); ?>
                    </p>

                    <p>
                        <?php _e('Make sure you have enabled these 2 APIs from <strong>Google Cloud Console</strong>:', 'wp-seopress-pro'); ?>
                    </p>

                    <ul>
                        <li><span class="dashicons dashicons-minus"></span><strong>Google Analytics API</strong></li>
                        <li><span class="dashicons dashicons-minus"></span><strong>Google Analytics Data API</strong></li>
                    </ul>

                    <p>
                        <a class="<?php if ('index.php' == $pagenow) { echo 'button'; } else { echo 'seopress-btn'; }; ?>" href="<?php echo admin_url('admin.php?page=seopress-google-analytics#tab=tab_seopress_google_analytics_enable'); ?>">
                            <?php _e('Authenticate', 'wp-seopress-pro'); ?>
                        </a>
                    </p>

                    <span class="dashicons dashicons-chart-line seopress-bg-icon"></span>
                </div>
            <?php
            }
        }
        function seopress_ga_dashboard_widget_handle() {
            // get saved data
            if ( ! $widget_options = get_option('seopress_ga_dashboard_widget_options')) {
                $widget_options = [];
            }

            // process update
            if (isset($_POST['seopress_ga_dashboard_widget_options'])) {
                check_admin_referer('seopress_ga_dashboard_widget_options');

                $widget_options['period'] = $_POST['seopress_ga_dashboard_widget_options']['period'];
                $widget_options['type'] = $_POST['seopress_ga_dashboard_widget_options']['type'];
                // save update
                update_option('seopress_ga_dashboard_widget_options', $widget_options);
                delete_transient('seopress_results_google_analytics');
            }

            wp_nonce_field('seopress_ga_dashboard_widget_options');

            // set defaults
            if ( ! isset($widget_options['period'])) {
                $widget_options['period'] = '30daysAgo';
            }

            $select = [
                'today' => __('Today', 'wp-seopress-pro'),
                'yesterday' => __('Yesterday', 'wp-seopress-pro'),
                '7daysAgo' => __('7 days ago', 'wp-seopress-pro'),
                '30daysAgo' => __('30 days ago', 'wp-seopress-pro'),
                '90daysAgo' => __('90 days ago', 'wp-seopress-pro'),
                '180daysAgo' => __('180 days ago', 'wp-seopress-pro'),
                '360daysAgo' => __('360 days ago', 'wp-seopress-pro'),
            ]; ?>

            <p><strong><?php _e('Period', 'wp-seopress-pro'); ?></strong></p>

            <p>
                <select id="period" name="seopress_ga_dashboard_widget_options[period]">
                    <?php foreach ($select as $key => $value) { ?>
                        <option value="<?php echo $key; ?>" <?php if ($widget_options['period'] === $key) {
                            echo 'selected="selected"';
                        } elseif (empty($widget_options['period']) && $key === '30daysAgo') { echo 'selected="selected"'; } ?>>
                            <?php echo $value; ?>
                        </option>
                    <?php } ?>
                </select>
            </p>

            <?php
                if ( ! isset($widget_options['type'])) {
                    $widget_options['type'] = 'ga_sessions';
                }

                $select = [
                    'ga_sessions' => __('Sessions', 'wp-seopress-pro'),
                    'ga_users' => __('Users', 'wp-seopress-pro'),
                    'ga_pageviews' => __('Page views', 'wp-seopress-pro'),
                    'ga_pageviewsPerSession' => __('Page views per session', 'wp-seopress-pro'),
                    'ga_avgSessionDuration' => __('Average session duration', 'wp-seopress-pro'),
                    'ga_bounceRate' => __('Bounce rate', 'wp-seopress-pro'),
                    'ga_percentNewSessions' => __('New Sessions', 'wp-seopress-pro'),
                ];
                if (!empty(seopress_get_service('GoogleAnalyticsOption')->getGA4PropertId())) {
                    unset($select['ga_bounceRate']);
                    unset($select['ga_percentNewSessions']);
                    unset($select['ga_pageviewsPerSession']);
                } ?>

                <p><strong><?php _e('Stats', 'wp-seopress-pro'); ?></strong></p>

                <p>
                    <select id="type" name="seopress_ga_dashboard_widget_options[type]">
                        <?php foreach ($select as $key => $value) { ?>
                        <option value="<?php echo $key; ?>" <?php if ($widget_options['type'] === $key) {
                            echo 'selected="selected"';
                        } ?>>
                            <?php echo $value; ?>
                        </option>
                        <?php } ?>
                    </select>
                </p>
            <?php
        }
    }
}
