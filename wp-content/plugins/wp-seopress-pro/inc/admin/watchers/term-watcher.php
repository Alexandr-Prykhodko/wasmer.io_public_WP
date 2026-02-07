<?php

defined('ABSPATH') or die('Please don&rsquo;t call the plugin directly. Thanks :)');


add_action('delete_term_taxonomy', 'seopress_watcher_term_trash');

/**
 * Detect a term trash
 * @return void
 */
function seopress_watcher_term_trash($termId)
{

    $term = get_term($termId);
    if (!$term || is_wp_error($term)) {
        return;
    }
    $taxonomy = get_taxonomy($term->taxonomy);

    if (!$taxonomy) {
        return;
    }

    if (!$taxonomy->publicly_queryable && !$taxonomy->public) {
        return;
    }

    $url = get_term_link($term, $taxonomy);
    $url = wp_parse_url($url);
    if (is_array($url) && isset($url['path'])) {
        $url = $url['path'];
    }

    $notices = seopress_get_option_post_need_redirects();

    if ($notices) {
        foreach ($notices as $key => $value) {
            if (isset($value["new_url"]) && $value["new_url"] === $url) {
                seopress_remove_notification_for_redirect($value['id']);
            }
        }
    }
    $message =
        /* translators: %s: post permalink */
        sprintf(__('<p>We have detected that you have deleted a term (<code>%s</code>).</p>', 'wp-seopress-pro'), $url);

    $message .= '<p>' . __('We suggest you to redirect this URL to avoid any SEO issues, and keep an optimal user experience.', 'wp-seopress-pro') . '</p>';

    seopress_create_notifaction_for_redirect([
        "id" => uniqid('', true),
        "message" => $message,
        "type" => "delete",
        "before_url" => $url
    ]);
}

add_action('edit_term', 'seopress_get_old_slug_before_change', 10, 3);

function seopress_get_old_slug_before_change($term_id, $tt_id, $taxonomy)
{
    $term = get_term($term_id, $taxonomy);

    set_transient('old_slug_term_' . $term_id, get_term_link($term, $taxonomy), 60);
}



add_action('edited_term', 'seopress_watcher_term_slug_change', 10, 3);

/**
 * Detect slug change
 *
 * @return void
 */
function seopress_watcher_term_slug_change($termId, $tt_id, $taxonomy)
{

    $term = get_term($termId, $taxonomy);
    if (!$term || is_wp_error($term)) {
        return;
    }

    $taxonomy = get_taxonomy($taxonomy);

    if (!$taxonomy) {
        return;
    }

    if (!$taxonomy->publicly_queryable && !$taxonomy->public) {
        return;
    }

    $url_term_before = get_transient('old_slug_term_' . $termId);

    if (!$url_term_before) {
        return;
    }


    delete_transient('old_slug_term_' . $termId);
    $url_term_before = wp_parse_url($url_term_before);

    if (is_array($url_term_before) && isset($url_term_before['path'])) {
        $url_term_before = $url_term_before['path'];
    }

    $url = get_term_link($term, $taxonomy);
    $url = wp_parse_url($url);
    if (is_array($url) && isset($url['path'])) {
        $url = $url['path'];
    }


    $notices = seopress_get_option_post_need_redirects();

    if ($notices) {
        foreach ($notices as $key => $value) {
            if (isset($value["new_url"]) && $value["new_url"] === $url_term_before) {
                seopress_remove_notification_for_redirect($value['id']);
            }
        }
    }

    // Prevent same slug
    if ($url === $url_term_before) {
        return;
    }

    /* translators: %s: post name (slug) %s: url redirect */
    $message = sprintf(
        __('<p>We have detected that you have changed a slug (<code>%s</code>) to (<code>%s</code>).</p>', 'wp-seopress-pro'),
        $url_term_before,
        $url
    );

    $message .= '<p>' . __('We suggest you to redirect this URL.', 'wp-seopress-pro') . '</p>';

    seopress_create_notifaction_for_redirect([
        "id" => uniqid('', true),
        "message" => $message,
        "type" => "update",
        "before_url" => $url_term_before,
        "new_url" => $url
    ]);
}
