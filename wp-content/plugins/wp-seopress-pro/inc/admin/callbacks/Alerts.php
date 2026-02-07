<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//Alert if noindex on homepage
function seopress_seo_alerts_noindex_callback()
{
    $options = get_option('seopress_pro_option_name');

    $check = isset($options['seopress_seo_alerts_noindex']); ?>

<label for="seopress_seo_alerts_noindex">
    <input id="seopress_seo_alerts_noindex" name="seopress_pro_option_name[seopress_seo_alerts_noindex]" type="checkbox"
        <?php if ('1' == $check) { ?>
    checked="yes" <?php } ?> value="1" />

    <?php esc_html_e('Receive an alert if your homepage is set to noindex', 'wp-seopress-pro'); ?>
</label>

<?php if (isset($options['seopress_seo_alerts_noindex'])) {
    esc_attr($options['seopress_seo_alerts_noindex']);
}
}

//Alert if robots.txt return an error
function seopress_seo_alerts_robots_txt_callback()
{
    $options = get_option('seopress_pro_option_name');

    $check = isset($options['seopress_seo_alerts_robots_txt']); ?>

<label for="seopress_seo_alerts_robots_txt">
    <input id="seopress_seo_alerts_robots_txt" name="seopress_pro_option_name[seopress_seo_alerts_robots_txt]"
        type="checkbox"
        <?php if ('1' == $check) { ?>
    checked="yes" <?php } ?> value="1" />

    <?php esc_html_e('Receive an alert if your robots.txt returns an error code', 'wp-seopress-pro'); ?>
</label>

<?php if (isset($options['seopress_seo_alerts_robots_txt'])) {
    esc_attr($options['seopress_seo_alerts_robots_txt']);
}
}

//Alert if XML sitemaps return an error
function seopress_seo_alerts_xml_sitemaps_callback()
{
    $options = get_option('seopress_pro_option_name');

    $check = isset($options['seopress_seo_alerts_xml_sitemaps']); ?>

<label for="seopress_seo_alerts_xml_sitemaps">
    <input id="seopress_seo_alerts_xml_sitemaps" name="seopress_pro_option_name[seopress_seo_alerts_xml_sitemaps]"
        type="checkbox"
        <?php if ('1' == $check) { ?>
    checked="yes" <?php } ?> value="1" />

    <?php esc_html_e('Receive an alert if your XML sitemap index return an error code', 'wp-seopress-pro'); ?>
</label>

<?php if (isset($options['seopress_seo_alerts_xml_sitemaps'])) {
    esc_attr($options['seopress_seo_alerts_xml_sitemaps']);
}
}

//Recipients to send the alerts
function seopress_seo_alerts_recipients_callback()
{
    $options = get_option('seopress_pro_option_name');
    $check = isset($options['seopress_seo_alerts_recipients']) ? $options['seopress_seo_alerts_recipients'] : null;

    printf(
        '<input type="text" name="seopress_pro_option_name[seopress_seo_alerts_recipients]" aria-label="' . esc_html__('Your email address', 'wp-seopress-pro') . '" placeholder="' . esc_html__('e.g. admin@example.com', 'wp-seopress-pro') . '" value="%s" />',
        esc_html($check)
    ); ?>

<p class="description">
    <?php esc_html_e('Receive SEO alerts by mail. Separate emails by commas.', 'wp-seopress-pro'); ?>
</p>

<?php
}

//Slack webhook URL to send the alerts
function seopress_seo_alerts_slack_webhook_url_callback()
{
    $options = get_option('seopress_pro_option_name');
    $check = isset($options['seopress_seo_alerts_slack_webhook_url']) ? $options['seopress_seo_alerts_slack_webhook_url'] : null;
    $docs = seopress_get_docs_links();

    printf(
        '<input type="text" name="seopress_pro_option_name[seopress_seo_alerts_slack_webhook_url]" aria-label="' . esc_html__('Your Slack webhook URL', 'wp-seopress-pro') . '" placeholder="' . esc_html__('Your Slack webhook URL', 'wp-seopress-pro') . '" value="%s" />',
        esc_html($check)
    ); ?>

<p class="description">
    <?php esc_html_e('Receive notifications, twice a day, to Slack.', 'wp-seopress-pro'); ?>
</p>

<p class="description">
    <a href="<?php echo esc_url($docs['alerts']['slack_webhook']); ?>" target="_blank" class="seopress-help">
        <?php esc_html_e('How to find my Slack Webhook URL?', 'wp-seopress-pro'); ?>
    </a><span class="seopress-help dashicons dashicons-external"></span>
</p>
<?php
}
?>
