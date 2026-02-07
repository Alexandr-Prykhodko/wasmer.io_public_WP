<?php
defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//Bulk actions
//Generate automatically SEO metadata
$postTypes = seopress_get_service('WordPressData')->getPostTypes();


foreach ($postTypes as $key => $value) {
    if (method_exists(seopress_get_service('TitleOption'), 'getSingleCptEnable') && null === seopress_get_service('TitleOption')->getSingleCptEnable($key) && '' != $key) {
        add_filter('bulk_actions-edit-' . $key, 'seopress_bulk_actions_ai_title');
        add_filter('bulk_actions-edit-' . $key, 'seopress_bulk_actions_ai_desc');
    }
}

function seopress_bulk_actions_ai_title($bulk_actions)
{
    $bulk_actions['seopress_ai_title'] = __('Generate meta title with AI', 'wp-seopress-pro');

    return $bulk_actions;
}

function seopress_bulk_actions_ai_desc($bulk_actions)
{
    $bulk_actions['seopress_ai_desc'] = __('Generate meta description with AI', 'wp-seopress-pro');

    return $bulk_actions;
}

add_action('admin_notices', 'seopress_bulk_action_ai_title_admin_notice');

function seopress_bulk_action_ai_title_admin_notice()
{
    if (! empty($_REQUEST['bulk_ai_posts'])) {
        $index_count = intval($_REQUEST['bulk_ai_posts']);


        printf('<div id="message" class="updated fade"><p>' .
                esc_html(
                /* translators: %s number of posts */
                _n(
                    '%s generation by AI.',
                    '%s generations by AI.',
                    $index_count,
                    'wp-seopress-pro'
                )
            ) . '</p></div>', absint($index_count));
    }

    if (! empty($_REQUEST['bulk_ai_posts_failed'])) {
        $index_count = intval($_REQUEST['bulk_ai_posts_failed']);

        $log = '<a href="' . esc_url(admin_url( 'admin.php?page=seopress-pro-page#tab=tab_seopress_ai' )) .'" class="button button-secondary">' . esc_html__( 'Check out logs', 'wp-seopress-pro' ) . '</a>';

        if($index_count > 0){
            printf('<div id="message" class="error fade"><p>' .
            esc_html(
                /* translators: %s number of posts */
                _n(
                    '%1$s generation failed by AI.',
                    '%1$s generations failed by AI.',
                    absint($index_count),
                    'wp-seopress-pro'
                )
            ) . ' %2$s</p></div>', absint($index_count), $log);
        }
    }


    if (! empty($_REQUEST['bulk_ai_title_posts'])) {
        $ai_title_count = intval($_REQUEST['bulk_ai_title_posts']);
        /* translators: %s number of posts */
        printf('<div id="message" class="updated fade"><p>' ._n('%s meta title generated with AI.','%s meta titles generated with AI.',$ai_title_count,'wp-seopress-pro') . '</p></div>', $ai_title_count);
    }
}

function seopress_bulk_action_ai_desc_handler($redirect_to, $doaction, $post_ids)
{
    if ('seopress_ai_desc' !== $doaction) {
        return $redirect_to;
    }
    foreach ($post_ids as $post_id) {
        seopress_pro_get_service('Completions')->generateTitlesDesc($post_id, 'desc', true);
    }
    $redirect_to = add_query_arg('bulk_ai_desc_posts', count($post_ids), $redirect_to);

    return $redirect_to;
}

add_action('admin_notices', 'seopress_bulk_action_ai_desc_admin_notice');

function seopress_bulk_action_ai_desc_admin_notice()
{
    if (! empty($_REQUEST['bulk_ai_desc_posts'])) {
        $ai_desc_count = intval($_REQUEST['bulk_ai_desc_posts']);
        /* translators: %s number of posts */
        printf('<div id="message" class="updated fade"><p>' . _n('%s meta description generated with AI.', '%s meta descriptions generated with AI.', $ai_desc_count, 'wp-seopress-pro') . '</p></div>', $ai_desc_count);
    }
}

/**
 * Bulk action to generate alt text for images
 *
 * @param array $bulk_actions
 *
 * @return array $bulk_actions
 */
add_filter('bulk_actions-upload', 'seopress_bulk_actions_ai_alt_text');
function seopress_bulk_actions_ai_alt_text($bulk_actions) {
    $bulk_actions['seopress_ai_alt_text'] = __('Generate alt text with AI', 'wp-seopress-pro');

    return $bulk_actions;
}

add_filter('handle_bulk_actions-upload', 'seopress_bulk_actions_ai_alt_text_handler', 10, 3);
function seopress_bulk_actions_ai_alt_text_handler($redirect_to, $action, $ids) {
    if ('seopress_ai_desc' !== $doaction) {
        return $redirect_to;
    }
    foreach ($post_ids as $post_id) {
        seopress_pro_get_service('Completions')->generateImgAltText($post_id, 'alt_text');
    }
    $redirect_to = add_query_arg('bulk_ai_alt_text', count($post_ids), $redirect_to);

    return $redirect_to;
}

add_action('admin_notices', 'seopress_bulk_actions_ai_alt_text_notice');
function seopress_bulk_actions_ai_alt_text_notice() {
    if (! empty($_REQUEST['bulk_ai_alt_text'])) {
        $ai_alt_text_count = intval($_REQUEST['bulk_ai_alt_text']);
        /* translators: %s number of media */
        printf('<div id="message" class="updated fade"><p>' . _n('%s alternative text generated with AI.', '%s alternative texts generated with AI.', $ai_alt_text_count, 'wp-seopress-pro') . '</p></div>', $ai_alt_text_count);
    }
}

/**
 * Bulk action to generate alt text for images with missing alt text
 *
 * @param array $bulk_actions
 *
 * @return array $bulk_actions
 */
add_filter('bulk_actions-upload', 'seopress_bulk_actions_ai_alt_text_missing');
function seopress_bulk_actions_ai_alt_text_missing($bulk_actions) {
    $bulk_actions['seopress_ai_alt_text_missing'] = __('Generate alt text with AI (only for missing alt text)', 'wp-seopress-pro');

    return $bulk_actions;
}

add_filter('handle_bulk_actions-upload', 'seopress_bulk_actions_ai_alt_text_handler', 10, 3);
function seopress_bulk_actions_ai_alt_text_missing_handler($redirect_to, $action, $ids) {
    if ('seopress_ai_alt_text_missing' !== $action) {
        return $redirect_to;
    }
    foreach ($ids as $id) {
        seopress_pro_get_service('Completions')->generateImgAltText($id, 'alt_text', 'en_US', true);
    }
    $redirect_to = add_query_arg('bulk_ai_alt_text_missing', count($ids), $redirect_to);

    return $redirect_to;
}

add_action('admin_notices', 'seopress_bulk_actions_ai_alt_text_missing_notice');
function seopress_bulk_actions_ai_alt_text_missing_notice() {
    if (! empty($_REQUEST['bulk_ai_alt_text_missing'])) {
        $ai_alt_text_count = intval($_REQUEST['bulk_ai_alt_text_missing']);
        /* translators: %s number of media */
        printf('<div id="message" class="updated fade"><p>' . _n('%s alternative text generated with AI.', '%s alternative texts generated with AI.', $ai_alt_text_count, 'wp-seopress-pro') . '</p></div>', $ai_alt_text_count);
    }
}