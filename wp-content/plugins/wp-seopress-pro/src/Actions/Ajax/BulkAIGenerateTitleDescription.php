<?php

namespace SEOPressPro\Actions\Ajax;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPress\Core\Hooks\ExecuteHooks;

class BulkAIGenerateTitleDescription implements ExecuteHooks
{
    /**
     *
     * @return void
     */
    public function hooks()
    {
        add_action('wp_ajax_seopress_bulk_action_ai_title', [$this, 'handleTitle']);
        add_action('wp_ajax_seopress_bulk_action_ai_desc', [$this, 'handleDescription']);
        add_action('wp_ajax_seopress_bulk_action_ai_alt_text', [$this, 'handleAltText']);
        add_action('wp_ajax_seopress_bulk_action_ai_alt_text_missing', [$this, 'handleAltTextMissing']);
    }

    /**
     * @return void
     */
    public function handleTitle()
    {
        check_ajax_referer('bulk-posts');

        if(!is_admin()){
            wp_send_json_error("not_authorized");
        }

        if(!current_user_can('edit_posts')){
            wp_send_json_error("not_authorized");
        }

        if(!isset($_POST['post_id'])){
            wp_send_json_error("missing_parameters");
        }

        $post_id = $_POST['post_id'];
        $lang = isset($_POST['lang']) ? $_POST['lang'] : 'en_US';

        $data = seopress_pro_get_service('Completions')->generateTitlesDesc($post_id, 'title', $lang, true);

        wp_send_json_success($data);
    }

    /**
     * @return void
     */
    public function handleDescription()
    {
        check_ajax_referer('bulk-posts');

        if(!is_admin()){
            wp_send_json_error("not_authorized");
        }

        if(!current_user_can('edit_posts')){
            wp_send_json_error("not_authorized");
        }

        if(!isset($_POST['post_id'])){
            wp_send_json_error("missing_parameters");
        }

        $post_id = $_POST['post_id'];
        $lang = isset($_POST['lang']) ? $_POST['lang'] : 'en_US';

        $data = seopress_pro_get_service('Completions')->generateTitlesDesc($post_id, 'desc', $lang, true);

        wp_send_json_success($data);
    }

    /**
     * @return void
     */
    public function handleAltText()
    {
        check_ajax_referer('bulk-media');

        if(!is_admin()){
            wp_send_json_error("not_authorized");
        }

        if(!current_user_can('edit_posts')){
            wp_send_json_error("not_authorized");
        }

        if(!isset($_POST['post_id'])){
            wp_send_json_error("missing_parameters");
        }

        $post_id = $_POST['post_id'];
        $lang = isset($_POST['lang']) ? $_POST['lang'] : 'en_US';

        $data = seopress_pro_get_service('Completions')->generateImgAltText($post_id, 'alt_text', $lang);

        wp_send_json_success($data);
    }

    /**
     * @return void
     */
    public function handleAltTextMissing()
    {
        check_ajax_referer('bulk-media');

        if(!is_admin()){
            wp_send_json_error("not_authorized");
        }

        if(!current_user_can('edit_posts')){
            wp_send_json_error("not_authorized");
        }

        if(!isset($_POST['post_id'])){
            wp_send_json_error("missing_parameters");
        }

        $post_id = $_POST['post_id'];
        $lang = isset($_POST['lang']) ? $_POST['lang'] : 'en_US';

        $data = seopress_pro_get_service('Completions')->generateImgAltText($post_id, 'alt_text', $lang, true);

        wp_send_json_success($data);
    }
}

