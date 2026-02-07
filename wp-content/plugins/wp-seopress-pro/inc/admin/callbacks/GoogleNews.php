<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//Google News
function seopress_news_enable_callback() {
    $options = get_option('seopress_pro_option_name');

    $check = isset($options['seopress_news_enable']); ?>

<label for="seopress_news_enable">
    <input id="seopress_news_enable" name="seopress_pro_option_name[seopress_news_enable]" type="checkbox" <?php if (true === $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php esc_html_e('Enable Google News Sitemap', 'wp-seopress-pro'); ?>
</label>

<?php if (isset($options['seopress_news_enable'])) {
        esc_attr($options['seopress_news_enable']);
    }
}

function seopress_news_name_callback() {
    $options = get_option('seopress_pro_option_name');
    $check   = isset($options['seopress_news_name']) ? $options['seopress_news_name'] : null;

    printf(
    '<input type="text" name="seopress_pro_option_name[seopress_news_name]" aria-label="' . esc_html__('Publication Name (must be the same as used in Google News)', 'wp-seopress-pro') . '" placeholder="' . esc_html__('Enter your Google News Publication Name', 'wp-seopress-pro') . '" value="%s"></textarea>',
    esc_html($check)
    );
}

function seopress_news_name_post_types_list_callback() {
    $options = get_option('seopress_pro_option_name');

    $check = isset($options['seopress_news_name_post_types_list']);

    $post_types = seopress_get_service('WordPressData')->getPostTypes(false, ['public' => true]);
    
    if (! empty($post_types)) {
        foreach ($post_types as $seopress_cpt_key => $seopress_cpt_value) { ?>
            <!--List all post types-->
            <div class="seopress_wrap_single_cpt">

                <?php
                    $check = isset($options['seopress_news_name_post_types_list'][$seopress_cpt_key]['include']);
                    ?>
                <label
                    for="seopress_xml_sitemap_post_types_list_include[<?php echo esc_attr($seopress_cpt_key); ?>]">
                    <input
                        id="seopress_xml_sitemap_post_types_list_include[<?php echo esc_attr($seopress_cpt_key); ?>]"
                        name="seopress_pro_option_name[seopress_news_name_post_types_list][<?php echo esc_attr($seopress_cpt_key); ?>][include]"
                        type="checkbox" <?php if (true === $check) { ?>
                    checked="yes"
                    <?php } ?>
                    value="1"/>

                    <?php echo esc_html($seopress_cpt_value->labels->name); ?>
                </label>

                <?php if (isset($options['seopress_news_name_post_types_list'][$seopress_cpt_key]['include'])) {
                        esc_attr($options['seopress_news_name_post_types_list'][$seopress_cpt_key]['include']);
                    }
                ?>
            </div>
    <?php
        }
    }
}
