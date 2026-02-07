<?php
defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

/**
 * Generate alt text when sending an image to WP
 *
 * @param string $post_ID
 *
 * @return void
 */
add_action('add_attachment', 'seopress_ai_alt_text_upload', 20);
function seopress_ai_alt_text_upload($post_ID) {
    if (seopress_pro_get_service('OptionPro')->getAIOpenaiAltText() !== '1') {
        return;
    }

    if (!isset($post_ID)) {
        return;
    }

    if (false === wp_attachment_is_image($post_ID)) {
        return;
    }

    // Check if the upload is from a Gravity Forms request
    if (isset($_POST['gform_submit']) || isset($_POST['gform_unique_id'])) {
        return;
    }

    $language = function_exists('seopress_get_current_lang') ? seopress_get_current_lang() : get_locale();

    $alt_text = seopress_pro_get_service('Completions')->generateImgAltText($post_ID, 'alt_text', $language);

    update_post_meta($post_ID, '_wp_attachment_image_alt', apply_filters('seopress_update_alt', sanitize_text_field($alt_text), $post_ID));
}


/**
 * Add AI button to media modal
 *
 * @param array $form_fields
 * @param object $post
 *
 * @return void
 */
function seopress_ai_alt_text_media_modal( $form_fields, $post ) {
    $text_field = get_post_meta($post->ID, 'text_field', true);
    $language   = function_exists('seopress_get_current_lang') ? seopress_get_current_lang() : get_locale();

    $form_fields['seopress_ai_generate_alt_text'] = array(
        'label' => '<img src="' . SEOPRESS_ASSETS_DIR . '/img/ai.svg" alt="" width="22" height="22" style="max-width: inherit;margin-right:10px">',
        'input' => 'html',
        'html'  => sprintf(
            '<div id="seopress-ai-generate-alt-text">
                <button
                    class="button button-small seopress-ai-generate-alt-text"
                    style="white-space:normal"
                    onclick="SEOPRESSMedia.generateAltText(%d, \'%s\')"
                >
                    %s
                </button>
                <div class="spinner"></div>
                <div class="seopress-error" style="display:none;"></div>
            </div>',
            (int) $post->ID,
            (string) $language,
            esc_html__('Generate alt text with AI', 'wp-seopress-pro'),
        ),
        'helps' => sprintf(
            'Configure <a href="%s">%s</a>',
            esc_url(admin_url('admin.php?page=seopress-pro-page')),
            esc_html__('AI settings or check logs.', 'wp-seopress-pro')
        ),
    );
    return $form_fields;
}
add_filter('attachment_fields_to_edit', 'seopress_ai_alt_text_media_modal', 10, 2);


