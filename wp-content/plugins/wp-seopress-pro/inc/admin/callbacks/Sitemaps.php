<?php
defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_pro_xml_sitemap_video_enable_callback()
{
    $docs  = seopress_get_docs_links();

    $options = get_option('seopress_xml_sitemap_option_name');

    $check = isset($options['seopress_xml_sitemap_video_enable']); ?>


<label for="seopress_xml_sitemap_video_enable">
    <input id="seopress_xml_sitemap_video_enable"
        name="seopress_xml_sitemap_option_name[seopress_xml_sitemap_video_enable]" type="checkbox" <?php if ('1' == $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>
    <?php esc_html_e('Enable Video Sitemap', 'wp-seopress-pro'); ?>
    <?php echo seopress_tooltip_link(esc_url($docs['sitemaps']['video']), esc_html__('Guide to enable XML video sitemap - new window', 'wp-seopress-pro')); ?>
</label>


<p class="description seopress-help">
    <?php
        /* translators: %s: documentation URL */
        echo wp_kses_post(sprintf(__('Your video sitemap is empty? Read our guide to learn more about <a href="%s" target="_blank">adding videos to your sitemap.</a>', 'wp-seopress-pro'), esc_url($docs['sitemaps']['video'])));
    ?>
</p>
<p class="description">
    <?php esc_html_e('YouTube videos are automatically added when you create / save a post, page or post type.', 'wp-seopress-pro'); ?>
</p>
<p class="description">
    <?php
        /* translators: %s: documentation URL */
        echo wp_kses_post(sprintf(__('<a href="%s">Regenerate automatic XML Video sitemap for YouTube?</a>', 'wp-seopress-pro'), esc_url(admin_url('admin.php?page=seopress-import-export#tab=tab_seopress_tool_video'))));
    ?>
</p>

<?php if (isset($options['seopress_xml_sitemap_video_enable'])) {
        esc_attr($options['seopress_xml_sitemap_video_enable']);
    }
}
