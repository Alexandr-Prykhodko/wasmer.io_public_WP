<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_print_section_info_news()
{
    seopress_print_pro_section('news');
    $docs = seopress_get_docs_links();

    if ('1' !== seopress_get_toggle_option('xml-sitemap')) { ?>
        <div class="seopress-notice is-error">
            <p>
                <?php esc_html_e('You need to enable XML Sitemap feature, in order to use Google News Sitemap.', 'wp-seopress-pro'); ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=seopress-xml-sitemap')); ?>">
                    <?php esc_html_e('Change this settings', 'wp-seopress-pro'); ?>
                </a>
            </p>
        </div>
    <?php
    } ?>

    <p>
        <?php echo wp_kses_post(__('We respect the rules of <strong>Google News</strong>: Only articles published during the <strong>previous two days</strong>, and, to a limit of <strong>1000 articles</strong>, are visible in the sitemap.', 'wp-seopress-pro')); ?>
    </p>

    <p>
        <?php echo /* translators: %s URL of the Google Publisher Center */ wp_kses_post(sprintf(__('The Google News XML sitemap must be sent to the <a href="%s" target="_blank">Google Publication Center</a>.', 'wp-seopress-pro'), esc_url('https://publishercenter.google.com/'))); ?>
    </p>

    <p>
        <pre><span class="dashicons dashicons-external"></span><a href="<?php echo esc_url(get_option('home') . '/news.xml'); ?>" target="_blank"><?php echo esc_url(get_option('home') . '/news.xml'); ?></a></pre>
    </p>

    <div class="seopress-notice">
        <p>
            <?php echo wp_kses_post(__('<strong>Noindex content</strong> will not be displayed in Sitemaps. Same for <strong>custom canonical URLs</strong>.', 'wp-seopress-pro')); ?>
        </p>

        <p class="seopress-help">
            <span class="dashicons dashicons-external"></span>
            <a href="<?php echo esc_url($docs['sitemaps']['error']['blank']); ?>" target="_blank">
                <?php esc_html_e('Blank sitemap?', 'wp-seopress-pro'); ?>
            </a> 
            <span class="dashicons dashicons-external"></span>
            <a href="<?php echo esc_url($docs['sitemaps']['error']['404']); ?>" target="_blank">
                <?php esc_html_e('404 error?', 'wp-seopress-pro'); ?>
            </a> 
            <span class="dashicons dashicons-external"></span>
            <a href="<?php echo esc_url($docs['sitemaps']['error']['html']); ?>" target="_blank">
                <?php esc_html_e('HTML error? Exclude XML and XSL from caching plugins!', 'wp-seopress-pro'); ?>
            </a> 
            <span class="dashicons dashicons-external"></span>
            <a href="<?php echo esc_url($docs['sitemaps']['xml']); ?>" target="_blank">
                <?php esc_html_e('Add your XML sitemaps to Google Search Console (video)', 'wp-seopress-pro'); ?>
            </a>
        </p>
    </div>

<?php
}
