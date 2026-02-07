<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//RSS
//=================================================================================================
if ('' != seopress_pro_get_service('OptionPro')->getRSSDisableCommentsFeed()) {
    add_filter('feed_links_show_comments_feed', '__return_false');
}
if ('' != seopress_pro_get_service('OptionPro')->getRSSDisablePostsFeed()) {
    remove_action('wp_head', 'feed_links', 2);
}
if ('' != seopress_pro_get_service('OptionPro')->getRSSDisableExtraFeed()) {
    remove_action('wp_head', 'feed_links_extra', 3);
}
if ('' != seopress_pro_get_service('OptionPro')->getRSSDisableAllFeeds()) {
    function seopress_rss_disable_feed() {
        wp_safe_redirect( esc_url(get_home_url()), 301 );
        exit();
    }

    add_action('do_feed', 'seopress_rss_disable_feed', 1);
    add_action('do_feed_rdf', 'seopress_rss_disable_feed', 1);
    add_action('do_feed_rss', 'seopress_rss_disable_feed', 1);
    add_action('do_feed_rss2', 'seopress_rss_disable_feed', 1);
    add_action('do_feed_atom', 'seopress_rss_disable_feed', 1);
    add_action('do_feed_rss2_comments', 'seopress_rss_disable_feed', 1);
    add_action('do_feed_atom_comments', 'seopress_rss_disable_feed', 1);
}

function seopress_rss_html_display($content) {
    $content_before = null;
    $content_after = null;

    if (is_feed()) {
        global $post;
        $seopress_rss_template_variables_array = [
            '%%sitetitle%%',
            '%%tagline%%',
            '%%post_author%%',
            '%%post_permalink%%',
            '%%post_title%%',
        ];

        $seopress_rss_template_variables_array = apply_filters( 'seopress_rss_dyn_vars', $seopress_rss_template_variables_array );

        $seopress_rss_template_replace_array = [
            get_bloginfo('name'),
            get_bloginfo('description'),
            get_the_author_meta('display_name', $post->post_author),
            get_the_permalink(),
            get_the_title(),
        ];

        $seopress_rss_template_replace_array = apply_filters( 'seopress_rss_dyn_vars_value', $seopress_rss_template_replace_array );

        if ('' != seopress_pro_get_service('OptionPro')->getRSSBeforeHTML()) {
            $seopress_rss_before_html_option = str_replace($seopress_rss_template_variables_array, $seopress_rss_template_replace_array, seopress_pro_get_service('OptionPro')->getRSSBeforeHTML());
            $content_before = $seopress_rss_before_html_option;
        }
        if ('' != seopress_pro_get_service('OptionPro')->getRSSAfterHTML()) {
            $seopress_rss_after_html_option = str_replace($seopress_rss_template_variables_array, $seopress_rss_template_replace_array, seopress_pro_get_service('OptionPro')->getRSSAfterHTML());
            $content_after = $seopress_rss_after_html_option;
        }
    }

    return $content_before . $content . $content_after;
}

//RSS <description></description>
add_filter('the_excerpt_rss', 'seopress_rss_html_display');
//RSS <content:encoded></content:encoded>
add_filter('the_content_feed', 'seopress_rss_html_display');

//Add post thumbnail to RSS feeds
if (seopress_pro_get_service('OptionPro')->getRSSPostThumbnail() === '1') {
    function seopress_rss_post_thumbnail() {
        if (has_post_thumbnail(get_the_ID())){
            $thumb_id = get_post_thumbnail_id(get_the_ID());
            $size = apply_filters('seopress_rss_post_thumb_size', 'thumbnail');
            $thumb = wp_get_attachment_image_src($thumb_id, $size);

            if (is_array($thumb)) {
                echo '<media:content url="' . $thumb[0] . '" width="' . $thumb[1] . '" height="' . $thumb[2] . '" medium="image"/>';
                echo "\n";
            }
        }
    }
    add_action('rss2_item', 'seopress_rss_post_thumbnail');

    //Requires to validate the RSS feed with media:content tag
    add_filter( 'rss2_ns', 'seopress_rss_post_thumbnail_namespace' );
    function seopress_rss_post_thumbnail_namespace() {
        echo 'xmlns:media="http://search.yahoo.com/mrss/"';
    }
}
