<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//Google Search Console API
function seopress_pro_inspect_url_api_callback() {
    $docs = function_exists('seopress_get_docs_links') ? seopress_get_docs_links() : '';
    $options = get_option('seopress_instant_indexing_option_name');
    $check   = isset($options['seopress_instant_indexing_google_api_key']) ? esc_attr($options['seopress_instant_indexing_google_api_key']) : null;

    printf(
'<textarea id="seopress_instant_indexing_google_api_key" name="seopress_instant_indexing_option_name[seopress_instant_indexing_google_api_key]" rows="12" placeholder="' . esc_html__('Paste your full Google JSON key file here', 'wp-seopress-pro') . '" aria-label="' . esc_html__('Paste your Google JSON key file here', 'wp-seopress-pro') . '">%s</textarea>',
esc_html($check)); ?>

<p class="seopress-help description"><?php echo wp_kses_post(sprintf(/* translators: %1$s documentation URL, %2$s documentation URL */ __('To use the <span class="dashicons dashicons-external"></span><a href="%1$s" target="_blank">Google Search Console API</a> and generate your JSON key file, please <span class="dashicons dashicons-external"></span><a href="%2$s" target="_blank">follow our guide.</a>', 'wp-seopress-pro'), esc_url($docs['search_console_api']['api']), esc_url($docs['inspect_url']['google']))); ?></p>

<?php
}

//Google Search Console Domain Property
function seopress_gsc_domain_property_callback() {
    $options = get_option('seopress_pro_option_name');

    $check = isset($options['seopress_gsc_domain_property']); ?>

<label for="seopress_gsc_domain_property">
    <input id="seopress_gsc_domain_property" name="seopress_pro_option_name[seopress_gsc_domain_property]" type="checkbox"
        <?php if (true === $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php esc_html_e('Yes, I‘m using a domain property to add my site in Google Search Console', 'wp-seopress-pro'); ?>
</label>

<?php if (isset($options['seopress_gsc_domain_property'])) {
        esc_attr($options['seopress_gsc_domain_property']);
    }
}

//Google Search Console Date Range
function seopress_gsc_date_range_callback() {
    $options = get_option('seopress_pro_option_name');

    $selected = isset($options['seopress_gsc_date_range']) ? $options['seopress_gsc_date_range'] : '- 3 months';
    ?>

<select id="seopress_gsc_date_range" name="seopress_pro_option_name[seopress_gsc_date_range]">
    <?php
        $dates = [
            '- 7 days'        => __('Last 7 days','wp-seopress-pro'),
            '- 28 days'       => __('Last 28 days','wp-seopress-pro'),
            '- 3 months'      => __('Last 3 months (default)','wp-seopress-pro'),
            '- 6 months'      => __('Last 6 months','wp-seopress-pro'),
            '- 12 months'     => __('Last 12 months','wp-seopress-pro'),
            '- 16 months'     => __('Last 16 months','wp-seopress-pro'),
        ];
        if ( ! empty($dates)) {
            foreach ($dates as $key => $date) { ?>
    <option <?php if (esc_attr($key) == $selected) { ?>
        selected="selected"
        <?php } ?>
        value="<?php echo esc_attr($key); ?>"><?php echo esc_html($date); ?>
    </option>
    <?php }
        }
    ?>
</select>

<div class="seopress-notice">
    <p>
        <?php
            /* translators: %s documentation URL */
            echo wp_kses_post(sprintf(__('To see Google Search Console data from your post types list, please <a href="%s">enable the GSC columns from <strong>Advanced settings</strong></a>','wp-seopress-pro'), esc_url(admin_url('admin.php?page=seopress-advanced#tab=tab_seopress_advanced_appearance'))));
        ?>
    </p>

    <p>
        <?php echo wp_kses_post(__('A schedule task will be executed <strong>daily</strong> to get your data from <strong>Search Console</strong>. Use the button below to manually init the data.', 'wp-seopress-pro')); ?>
    </p>

    <p>
        <?php esc_html_e('Note that the metrics obtained may be slightly different due to incomplete date ranges in Search Console.', 'wp-seopress-pro'); ?>
    </p>

    <p>
        <?php echo wp_kses_post(__('Be sure to <strong>save changes</strong> before requesting data from Search Console to reflect your new settings.', 'wp-seopress-pro')); ?>
    </p>
</div>

<p>
    <div id="seopress_launch_bot_search_console" class="btn btnPrimary">
        <?php esc_html_e('Get Insights from Google Search Console', 'wp-seopress-pro'); ?>
    </div>
    <span class="spinner"></span>
</p>
<div class="log"></div>

<?php if (isset($options['seopress_gsc_date_range'])) {
        esc_attr($options['seopress_gsc_date_range']);
    }
}
