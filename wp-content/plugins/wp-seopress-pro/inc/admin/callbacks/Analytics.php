<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//Google Analytics
function seopress_google_analytics_auth_callback() {
    $options = get_option('seopress_google_analytics_option_name');

    $selected = isset($options['seopress_google_analytics_auth']) ? $options['seopress_google_analytics_auth'] : null;

    $client_id = '';
    if ('' !== seopress_get_service('GoogleAnalyticsOption')->getAuthClientId()) {
        $client_id = seopress_get_service('GoogleAnalyticsOption')->getAuthClientId();
    }

    $client_secret = '';
    if ('' !== seopress_get_service('GoogleAnalyticsOption')->getAuthSecretId()) {
        $client_secret = seopress_get_service('GoogleAnalyticsOption')->getAuthSecretId();
    }

    $redirect_uri = admin_url('admin.php?page=seopress-google-analytics');

    if (!empty($client_id) && !empty($client_secret)) {
        require_once SEOPRESS_PRO_PLUGIN_DIR_PATH . '/vendor/autoload.php';
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
    } else { ?>
<p>
    <?php esc_html_e('To sign in with Google Analytics, you have to set a Client and Secret ID in the fields below:', 'wp-seopress-pro'); ?>
</p>
<?php }

    //Logout
    if (!empty($client_id) && !empty($client_secret)) {
        if (isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'ga-logout')) {
            $seopress_google_analytics_options = get_option('seopress_google_analytics_option_name1');
            $seopress_google_analytics_options['refresh_token'] = null;
            $seopress_google_analytics_options['access_token'] = null;
            $seopress_google_analytics_options['code'] = '';
            $seopress_google_analytics_options['debug'] = '';
            update_option('seopress_google_analytics_option_name1', $seopress_google_analytics_options, 'yes');
            update_option('seopress_google_analytics_lock_option_name', '', 'yes');
        }
    }

    if (!empty($client_id) && !empty($client_secret)) {
        // No nonce token, GG will check if the code is correct, if not, nothing happen.
        if (isset($_GET['code']) && null === seopress_pro_get_service('GoogleAnalyticsOptionPro')->getAccessToken()) {
            $client->authenticate($_GET['code']);
            $_SESSION['token'] = $client->getAccessToken();

            $seopress_google_analytics_options = get_option('seopress_google_analytics_option_name1');
            $seopress_google_analytics_options['access_token'] = $_SESSION['token']['access_token'];
            $seopress_google_analytics_options['refresh_token'] = $_SESSION['token']['refresh_token'];
            $seopress_google_analytics_options['debug'] = $_SESSION['token'];
            $seopress_google_analytics_options['code'] = $_GET['code'];
            update_option('seopress_google_analytics_option_name1', $seopress_google_analytics_options, 'yes');
        }

        //Login button
        if ( ! $client->getAccessToken() && null === seopress_pro_get_service('GoogleAnalyticsOptionPro')->getAccessToken()) {
            $authUrl = $client->createAuthUrl(); ?>

            <p>
                <a class="login btn btnSecondary"
                    href="<?php echo esc_url($authUrl); ?> ">
                    <?php esc_html_e('Connect with Google Analytics', 'wp-seopress-pro'); ?>
                </a>
            </p>
            <?php
        }

        //Logout button
        if (null !== seopress_pro_get_service('GoogleAnalyticsOptionPro')->getAccessToken()) {
            $client->setAccessToken(seopress_pro_get_service('GoogleAnalyticsOptionPro')->getDebug());

            if ($client->isAccessTokenExpired()) {
                $client->refreshToken(seopress_pro_get_service('GoogleAnalyticsOptionPro')->getDebug());

                $seopress_new_access_token = $client->getAccessToken(seopress_pro_get_service('GoogleAnalyticsOptionPro')->getDebug());

                $seopress_google_analytics_options = get_option('seopress_google_analytics_option_name1');
                $seopress_google_analytics_options['access_token'] = $seopress_new_access_token['access_token'] ?? null;
                $seopress_google_analytics_options['refresh_token'] = $seopress_new_access_token['refresh_token'] ?? null;
                $seopress_google_analytics_options['debug'] = $seopress_new_access_token;
                update_option('seopress_google_analytics_option_name1', $seopress_google_analytics_options, 'yes');
            } ?>

            <p>
                <a class="logout btn btnSecondary" href="<?php echo esc_url(wp_nonce_url($redirect_uri . '&logout=1', 'ga-logout')); ?>"><?php esc_html_e('Log out from Google', 'wp-seopress-pro'); ?></a>
            </p>

        <?php
        }
    }
    if (isset($options['seopress_google_analytics_auth'])) {
        esc_attr($options['seopress_google_analytics_auth']);
    }
}

function seopress_google_analytics_auth_client_id_callback() {
    $options = get_option('seopress_google_analytics_option_name');
    $docs = function_exists('seopress_get_docs_links') ? seopress_get_docs_links() : '';

    $selected = isset($options['seopress_google_analytics_auth_client_id']) ? $options['seopress_google_analytics_auth_client_id'] : null; ?>

    <input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_auth_client_id]"
        placeholder="<?php esc_html_e('Enter your client ID', 'wp-seopress-pro'); ?>"
        aria-label="<?php esc_html_e('Google Console Client ID', 'wp-seopress-pro'); ?>"
        value="<?php echo esc_attr($selected); ?>" />

    <?php if (isset($options['seopress_google_analytics_auth_client_id'])) {
        esc_html($options['seopress_google_analytics_auth_client_id']);
    }

    echo seopress_tooltip_link(esc_url($docs['analytics']['connect']), esc_html__('Guide to connect your WordPress site with Google Analytics - new window', 'wp-seopress-pro'));
}

function seopress_google_analytics_auth_secret_id_callback() {
    $options = get_option('seopress_google_analytics_option_name');

    $selected = isset($options['seopress_google_analytics_auth_secret_id']) ? $options['seopress_google_analytics_auth_secret_id'] : null; ?>

    <input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_auth_secret_id]"
        placeholder="<?php esc_html_e('Enter your secret ID', 'wp-seopress-pro'); ?>"
        aria-label="<?php esc_html_e('Google Console Secret ID', 'wp-seopress-pro'); ?>"
        value="<?php echo esc_attr($selected); ?>" />

    <?php if (isset($options['seopress_google_analytics_auth_secret_id'])) {
        esc_html($options['seopress_google_analytics_auth_secret_id']);
    }
}

function seopress_google_analytics_ga4_property_id_callback() {
    $options = get_option('seopress_google_analytics_option_name');
    $docs = function_exists('seopress_get_docs_links') ? seopress_get_docs_links() : '';

    $selected = isset($options['seopress_google_analytics_ga4_property_id']) ? $options['seopress_google_analytics_ga4_property_id'] : null;

    if ('1' === seopress_get_toggle_option('google-analytics')) {
        if (!empty(seopress_get_service('GoogleAnalyticsOption')->getGA4PropertId()) && !empty(seopress_get_service('GoogleAnalyticsOption')->getGA4())) {
            if (seopress_get_service('GoogleAnalyticsOption')->getGA4PropertId() === seopress_get_service('GoogleAnalyticsOption')->getGA4()) {
            ?>
                <div class="seopress-notice is-warning">
                    <p>
                        <?php
                            echo wp_kses_post(sprintf(/* translators: %s documentation URL */ __('To get your Google Analytics stats in dashboard, your <strong>GA4 property ID must NOT be equals to your GA4 measurement ID</strong>. <span class="seopress-help dashicons dashicons-external"></span><a class="seopress-help" href="%s" target="_blank">Find my property ID</a>', 'wp-seopress-pro'), esc_url($docs['analytics']['ga4_property'])));
                        ?>
                    </p>
                </div>
            <?php }
            }
        }
    ?>

    <input type="text" name="seopress_google_analytics_option_name[seopress_google_analytics_ga4_property_id]"
        placeholder="<?php esc_html_e('Enter your Google Analytics v4 property ID', 'wp-seopress-pro'); ?>"
        aria-label="<?php esc_html_e('GA4 property ID', 'wp-seopress-pro'); ?>"
        value="<?php echo esc_attr($selected); ?>" />

    <?php if (isset($options['seopress_google_analytics_ga4_property_id'])) {
        esc_html($options['seopress_google_analytics_ga4_property_id']);
    }

    echo seopress_tooltip_link(esc_url($docs['analytics']['ga4_property']), esc_html__('Find your GA4 property ID - new window', 'wp-seopress-pro')); ?>

    <p class="description">
        <?php
            /* translators: %s documentation URL */
            printf(__('<span class="seopress-help dashicons dashicons-external"></span><a class="seopress-help" href="%s" target="_blank">Find my property ID</a>', 'wp-seopress-pro'), esc_url($docs['analytics']['ga4_property']));
        ?>
    </p>

<?php
}

function seopress_google_analytics_dashboard_widget_callback() {
    $options = get_option('seopress_google_analytics_option_name');

    $check = isset($options['seopress_google_analytics_dashboard_widget']); ?>

    <label for="seopress_google_analytics_dashboard_widget">
        <input id="seopress_google_analytics_dashboard_widget"
            name="seopress_google_analytics_option_name[seopress_google_analytics_dashboard_widget]" type="checkbox" <?php if ('1' == $check) { ?>
        checked="yes"
        <?php } ?>
        value="1"/>

        <?php esc_html_e('Remove Google Analytics stats widget from WordPress dashboard', 'wp-seopress-pro'); ?>
    </label>

    <?php if (isset($options['seopress_google_analytics_dashboard_widget'])) {
        esc_attr($options['seopress_google_analytics_dashboard_widget']);
    }
}

function seopress_print_section_info_google_analytics_logs() {
    ?>
    <hr>
    <h3 id="seopress-google-analytics-logs">
        <?php esc_html_e('Google Analytics Logs', 'wp-seopress-pro'); ?>
    </h3>

    <p><?php esc_html_e('Below is the latest error message obtained from the Google Analytics API:', 'wp-seopress-pro'); ?></p>

    <?php
    //Logs
    $logs = get_transient('seopress_results_google_analytics') ? get_transient('seopress_results_google_analytics') : '';
    is_string($logs) ? $logs = json_decode($logs, true) : $logs;
    
    echo '<pre style="width: 100%">';
    if (is_array($logs) && !empty($logs['error'])) {
        foreach ($logs['error'] as $key => $value) {
            if (is_string($value)) {
                echo esc_html($key) . ' => ' . esc_html($value) . '<br>';
            }
        }
    ?>
<?php
    } else {
        esc_html_e('Currently no errors logged.', 'wp-seopress-pro');
    }
    echo '</pre>';
}