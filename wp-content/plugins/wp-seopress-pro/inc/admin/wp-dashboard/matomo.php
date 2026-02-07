<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//Matomo Dashboard widget
//=================================================================================================
function seopress_google_analytics_matomo_widget_auth_token() {
	return seopress_get_service('GoogleAnalyticsOption')->getMatomoAuthToken();
}

if ('1' === seopress_get_toggle_option('google-analytics') && '1' !== seopress_pro_get_service('GoogleAnalyticsWidgetsOptionPro')->getMatomoDashboardWidget()) {
    if (seopress_advanced_security_matomo_widget_check() === true) {
        add_action('wp_dashboard_setup', 'seopress_matomo_dashboard_widget');
        function seopress_matomo_dashboard_widget() {
            $return_false = '';
            $return_false = apply_filters('seopress_matomo_dashboard_widget', $return_false);

            if (has_filter('seopress_matomo_dashboard_widget') && false == $return_false) {
                //do nothing
            } else {
                $matomoID = seopress_get_service('GoogleAnalyticsOption')->getMatomoId() ? seopress_get_service('GoogleAnalyticsOption')->getMatomoId() : null;

                if (empty($matomoID)) {
                    return;
                }

                $matomoSiteID = seopress_get_service('GoogleAnalyticsOption')->getMatomoSiteId() ? seopress_get_service('GoogleAnalyticsOption')->getMatomoSiteId() : null;

                if (empty($matomoSiteID)) {
                    return;
                }

                wp_add_dashboard_widget('seopress_matomo_dashboard_widget', 'Matomo Analytics', 'seopress_matomo_dashboard_widget_display', 'seopress_matomo_dashboard_widget_handle');
            }
        }

        function seopress_matomo_dashboard_widget_display() {
            if ('' != seopress_google_analytics_matomo_widget_auth_token()) {
                $stats = get_transient('seopress_results_matomo');

                $html  = [];

                if ($stats === false) {
                    return;
                }

                if (empty($stats['all'])) {
                    return;
                }

                $stats = $stats['all'];

                if (! empty($stats['nb_uniq_visitors'])) {
                    $html[__('Unique Visitors', 'wp-seopress-pro')] = is_numeric($stats['nb_uniq_visitors']) ? number_format_i18n($stats['nb_uniq_visitors']) : '';
                }
                if (! empty($stats['nb_visits'])) {
                    $html[__('Visits', 'wp-seopress-pro')] = is_numeric($stats['nb_visits']) ? number_format_i18n($stats['nb_visits']) : '';
                }
                if (! empty($stats['max_actions'])) {
                    $html[__('Max actions in one visit', 'wp-seopress-pro')] = is_numeric($stats['max_actions']) ? number_format_i18n($stats['max_actions']) : '';
                }
                if (! empty($stats['nb_actions_per_visit'])) {
                    $html[__('Average actions per visit', 'wp-seopress-pro')] = is_numeric($stats['nb_actions_per_visit']) ? number_format_i18n($stats['nb_actions_per_visit'], 2) : '';
                }
                if (! empty($stats['bounce_rate'])) {
                    $html[__('Bounce rate', 'wp-seopress-pro')] = is_numeric($stats['bounce_rate']) ? number_format_i18n($stats['bounce_rate']) .'%' : '';
                }
                if (! empty($stats['avg_time_on_site'])) {
                    $html[__('Avg. Visit Duration (in seconds)', 'wp-seopress-pro')] = is_numeric($stats['avg_time_on_site']) ? number_format_i18n($stats['avg_time_on_site']) : '';
                } ?>

                <span class="spinner"></span>

                <div class="wrap-chart-stat">
                    <canvas id="seopress_matomo_widget_chart" width="400" height="250"></canvas>
                    <script>var ctxseopress_matomo = document.getElementById("seopress_matomo_widget_chart");</script>
                </div>

                <div class="seopress-summary-items">
                    <?php if (! empty($html)) { ?>
                        <?php foreach ($html as $key => $value) { ?>
                            <div class="seopress-summary-item">
                        <div class="seopress-summary-item-label">
                            <?php echo $key; ?>
                        </div>
                        <div class="seopress-summary-item-data">
                            <?php echo $value; ?>
                        </div>
                        </div>
                        <?php } ?>
                    <?php } else { ?>
                        <p class="inside"><?php _e('No stats found', 'wp-seopress-pro'); ?></p>
                    <?php } ?>
                </div>
                <?php
            } else {
                global $pagenow;
                ?>
                <div class="seopress-tools-card">
                    <p><?php _e('You need to login to Matomo Analytics.', 'wp-seopress-pro'); ?></p>
                    <p><?php _e('Make sure you have entered an authentication token from Matomo settings page.', 'wp-seopress-pro'); ?></p>
                    <p>
                        <a class="<?php if ('index.php' == $pagenow) { echo 'button'; } else { echo 'seopress-btn'; }; ?>" href="<?php echo admin_url('admin.php?page=seopress-google-analytics#tab=tab_seopress_google_analytics_matomo'); ?>">
                            <?php _e('Authenticate', 'wp-seopress-pro'); ?>
                        </a>
                    </p>
                    <span class="dashicons dashicons-chart-line seopress-bg-icon"></span>
                </div>
        <?php }
        }

        function seopress_matomo_dashboard_widget_handle() {
            // get saved data
            if ( ! $widget_options = get_option('seopress_matomo_dashboard_widget_options')) {
                $widget_options = [];
            }

            // process update
            if (isset($_POST['seopress_matomo_dashboard_widget_options'])) {
                check_admin_referer('seopress_matomo_dashboard_widget_options');

                $widget_options['period'] = $_POST['seopress_matomo_dashboard_widget_options']['period'];
                $widget_options['type'] = $_POST['seopress_matomo_dashboard_widget_options']['type'];
                // save update
                update_option('seopress_matomo_dashboard_widget_options', $widget_options);
                delete_transient('seopress_results_matomo');
            }

            wp_nonce_field('seopress_matomo_dashboard_widget_options');

            // set defaults
            if ( ! isset($widget_options['period'])) {
                $widget_options['period'] = 'last30';
            }

            $select = [
                'last1' => __('Today', 'wp-seopress-pro'),
                'last2' => __('Yesterday', 'wp-seopress-pro'),
                'last7' => __('7 days ago', 'wp-seopress-pro'),
                'last30' => __('30 days ago', 'wp-seopress-pro'),
                'last90' => __('90 days ago', 'wp-seopress-pro'),
                'last365' => __('365 days ago', 'wp-seopress-pro'),
            ]; ?>

            <p><strong><?php _e('Period', 'wp-seopress-pro'); ?></strong></p>

            <p>
                <select id="period" name="seopress_matomo_dashboard_widget_options[period]">
                    <?php foreach ($select as $key => $value) { ?>
                        <option value="<?php echo $key; ?>" <?php if ($widget_options['period'] === $key) {
                            echo 'selected="selected"';
                        } elseif (empty($widget_options['period']) && $key === 'last30') { echo 'selected="selected"'; } ?>>
                            <?php echo $value; ?>
                        </option>
                    <?php } ?>
                </select>
            </p>

            <?php
                if ( ! isset($widget_options['type'])) {
                    $widget_options['type'] = 'nb_visits';
                }

                $select = [
                    'nb_visits' => __('Sessions', 'wp-seopress-pro'),
                    'nb_uniq_visitors' => __('Users', 'wp-seopress-pro'),
                    'max_actions' => __('Maximum actions in one visit', 'wp-seopress-pro'),
                    'nb_actions_per_visit' => __('Average actions per visit', 'wp-seopress-pro'),
                    'avg_time_on_site' => __('Average session duration', 'wp-seopress-pro'),
                    'bounce_rate' => __('Bounce Rate', 'wp-seopress-pro'),
                ];
                ?>
            <p><strong><?php _e('Stats', 'wp-seopress-pro'); ?></strong></p>

            <p>
                <select id="type" name="seopress_matomo_dashboard_widget_options[type]">
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
