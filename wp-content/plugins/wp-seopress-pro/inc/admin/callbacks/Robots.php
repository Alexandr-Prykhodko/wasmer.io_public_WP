<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_robots_enable_callback() {
    if (is_network_admin() && is_multisite()) {
        $options = get_option('seopress_pro_mu_option_name');

        $check = isset($options['seopress_mu_robots_enable']); ?>

<label for="seopress_mu_robots_enable">
    <input id="seopress_mu_robots_enable" name="seopress_pro_mu_option_name[seopress_mu_robots_enable]" type="checkbox"
        <?php if (true === $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php esc_html_e('Enable robots.txt virtual file', 'wp-seopress-pro'); ?>
</label>

<?php if (isset($options['seopress_mu_robots_enable'])) {
            esc_attr($options['seopress_mu_robots_enable']);
        }
    } else {
        $options = get_option('seopress_pro_option_name');

        $check = isset($options['seopress_robots_enable']); ?>

<label for="seopress_robots_enable">
    <input id="seopress_robots_enable" name="seopress_pro_option_name[seopress_robots_enable]" type="checkbox" <?php if (true === $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php esc_html_e('Enable robots.txt virtual file', 'wp-seopress-pro'); ?>
</label>

<?php if (isset($options['seopress_robots_enable'])) {
            esc_attr($options['seopress_robots_enable']);
        }
    }
}

function seopress_robots_file_callback() {
    $docs     = function_exists('seopress_get_docs_links') ? seopress_get_docs_links() : '';
    $search_slug = 'search';
    if (!empty(seopress_pro_get_service('AdvancedOptionPro')->getRewriteSearch())) {
        $search_slug = seopress_pro_get_service('AdvancedOptionPro')->getRewriteSearch();
    }

    if (defined('SEOPRESS_BLOCK_ROBOTS') && SEOPRESS_BLOCK_ROBOTS == true) { ?>
<div class="seopress-notice is-error">
    <p>
        <?php esc_html_e('Access not allowed by the PHP define.', 'wp-seopress-pro'); ?>
    </p>
</div>
<?php } else {
        if (is_network_admin() && is_multisite()) {
            $options = get_option('seopress_pro_mu_option_name');
            $check   = isset($options['seopress_mu_robots_file']) ? $options['seopress_mu_robots_file'] : null;

            printf(
            '<textarea id="seopress_mu_robots_file" class="seopress_robots_file" name="seopress_pro_mu_option_name[seopress_mu_robots_file]" rows="30" aria-label="' . esc_html__('Virtual Robots.txt file', 'wp-seopress-pro') . '" placeholder="' . esc_html__('This is your robots.txt file!', 'wp-seopress-pro') . '">%s</textarea>',
            esc_html($check)
            );
        } else {
            $options = get_option('seopress_pro_option_name');
            $check   = isset($options['seopress_robots_file']) ? $options['seopress_robots_file'] : null;

            printf(
            '<textarea id="seopress_robots_file" class="seopress_robots_file" name="seopress_pro_option_name[seopress_robots_file]" rows="30" aria-label="' . esc_html__('Virtual Robots.txt file', 'wp-seopress-pro') . '" placeholder="' . esc_html__('This is your robots.txt file!', 'wp-seopress-pro') . '">%s</textarea>',
            esc_html($check)
            );
        } ?>
<div class="wrap-tags">
    <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-robots-9" data-tag="
# Block add-to-cart links (WooCommerce)
user-agent: *
disallow: /*add-to-cart=*"><span class="dashicons dashicons-plus-alt2"></span><?php esc_html_e('Block add-to-cart links (WooCommerce)', 'wp-seopress-pro'); ?></button>

    <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-robots-8" data-tag="
# Block RSS feeds
user-agent: *
disallow: /feed/
disallow: */feed
disallow: */feed$
disallow: /feed/$
disallow: /comments/feed
disallow: /?feed=
disallow: /wp-feed"><span class="dashicons dashicons-plus-alt2"></span><?php esc_html_e('Block RSS feeds', 'wp-seopress-pro'); ?></button>

    <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-robots-10" data-tag="
# Block ChatGPT bot
user-agent: CCBot
disallow: /
user-agent: GPTBot
disallow: /"><span class="dashicons dashicons-plus-alt2"></span><?php esc_html_e('Block ChatGPT bot', 'wp-seopress-pro'); ?></button>

    <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-robots-12" data-tag="
# Block Bard bot
user-agent: Google-Extended
disallow: /"><span class="dashicons dashicons-plus-alt2"></span><?php esc_html_e('Block Bard bot', 'wp-seopress-pro'); ?></button>

    <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-robots-15" data-tag="
# Block Claude bot
user-agent: ClaudeBot
disallow: /"><span class="dashicons dashicons-plus-alt2"></span><?php esc_html_e('Block Claude bot', 'wp-seopress-pro'); ?></button>

    <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-robots-11" data-tag="
# Block Petal bot
user-agent: PetalBot
disallow: /"><span class="dashicons dashicons-plus-alt2"></span><?php esc_html_e('Block Petal bot', 'wp-seopress-pro'); ?></button>

    <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-robots-1" data-tag="
# Block SemrushBot
user-agent: SemrushBot
disallow: /
user-agent: SemrushBot-SA
disallow: /"><span class="dashicons dashicons-plus-alt2"></span><?php esc_html_e('Block SemrushBot', 'wp-seopress-pro'); ?></button>

    <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-robots-2" data-tag="
# Block MajesticSEOBot
user-agent: MJ12bot
disallow: /"><span class="dashicons dashicons-plus-alt2"></span><?php esc_html_e('Block MajesticSEOBot', 'wp-seopress-pro'); ?></button>

    <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-robots-7" data-tag="
# Block AhrefsBot
user-agent: AhrefsBot
disallow: /"><span class="dashicons dashicons-plus-alt2"></span><?php esc_html_e('Block AhrefsBot', 'wp-seopress-pro'); ?></button>

    <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-robots-3" data-tag="
# Link to your sitemap
Sitemap: <?php echo esc_url(get_home_url() .'/sitemaps.xml'); ?>"><span
            class="dashicons dashicons-plus-alt2"></span><?php esc_html_e('Link to your sitemap', 'wp-seopress-pro'); ?></button>

    <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-robots-13" data-tag="
# Block Search Results
user-agent: *
disallow: /?s=
disallow: /page/*/?s=
disallow: /<?php echo esc_attr($search_slug); ?>/"><span class="dashicons dashicons-plus-alt2"></span><?php esc_html_e('Block Search Results', 'wp-seopress-pro'); ?></button>

    <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-robots-14" data-tag="
# Default WooCommerce rules
user-agent: *
Disallow: /wp-content/uploads/wc-logs/
Disallow: /wp-content/uploads/woocommerce_transient_files/
Disallow: /wp-content/uploads/woocommerce_uploads/"><span class="dashicons dashicons-plus-alt2"></span><?php esc_html_e('Default WooCommerce rules', 'wp-seopress-pro'); ?></button>

    <button type="button" class="btn btnSecondary tag-title" id="seopress-tag-robots-6" data-tag="
# Default WP rules
user-agent: *
disallow: /wp-admin/
allow: /wp-admin/admin-ajax.php"><span class="dashicons dashicons-plus-alt2"></span><?php esc_html_e('Default WP rules', 'wp-seopress-pro'); ?></button>

</div>
<?php
    }
    echo seopress_tooltip_link(esc_url($docs['robots']['file']), esc_html__('Guide to edit your robots.txt file - new window', 'wp-seopress-pro'));
}
