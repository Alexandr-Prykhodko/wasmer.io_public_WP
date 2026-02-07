<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_rich_snippets_enable_callback() {
    $options = get_option('seopress_pro_option_name');

    $check = isset($options['seopress_rich_snippets_enable']); ?>

<label for="seopress_rich_snippets_enable">
    <input id="seopress_rich_snippets_enable" name="seopress_pro_option_name[seopress_rich_snippets_enable]"
        type="checkbox" <?php if (true === $check) { ?>
    checked="yes"
    <?php } ?>
    value="1"/>

    <?php esc_html_e('Enable Structured Data Types metabox for your posts, pages and custom post types', 'wp-seopress-pro'); ?>
</label>

<?php if (isset($options['seopress_rich_snippets_enable'])) {
        esc_attr($options['seopress_rich_snippets_enable']);
    }
}

function seopress_rich_snippets_publisher_logo_callback() {
    $options = get_option('seopress_pro_option_name');

    $options_set = isset($options['seopress_rich_snippets_publisher_logo']) ? $options['seopress_rich_snippets_publisher_logo'] : null;

    $options_set2 = isset($options['seopress_rich_snippets_publisher_logo_width']) ? $options['seopress_rich_snippets_publisher_logo_width'] : null;
    $options_set3 = isset($options['seopress_rich_snippets_publisher_logo_height']) ? $options['seopress_rich_snippets_publisher_logo_height'] : null;

    $check = isset($options['seopress_rich_snippets_publisher_logo']); ?>

<input id="seopress_rich_snippets_publisher_logo_meta" autocomplete="off" type="text"
    value="<?php echo esc_attr($options_set); ?>"
    name="seopress_pro_option_name[seopress_rich_snippets_publisher_logo]"
    aria-label="<?php esc_html_e('Upload your publisher logo', 'wp-seopress-pro'); ?>"
    placeholder="<?php esc_html_e('Select your logo', 'wp-seopress-pro'); ?>" />

<input id="seopress_rich_snippets_publisher_logo_width" type="hidden"
    value="<?php echo esc_attr($options_set2); ?>"
    name="seopress_pro_option_name[seopress_rich_snippets_publisher_logo_width]" />
<input id="seopress_rich_snippets_publisher_logo_height" type="hidden"
    value="<?php echo esc_attr($options_set3); ?>"
    name="seopress_pro_option_name[seopress_rich_snippets_publisher_logo_height]" />

<input id="seopress_rich_snippets_publisher_logo_upload" class="btn btnSecondary" type="button"
    value="<?php esc_html_e('Upload an Image', 'wp-seopress-pro'); ?>" />

<input id="seopress_rich_snippets_publisher_logo_remove" class="btn btnLink is-deletable" type="button" value="<?php esc_html_e('Remove', 'wp-seopress-pro'); ?>" />

<p class="description">
    <?php esc_html_e('A logo that is representative of the organization. Files must be BMP, GIF, JPEG, PNG, WebP or SVG. The image must be 112x112px, at minimum.', 'wp-seopress-pro'); ?>
</p>

<div id="seopress_rich_snippets_publisher_logo_placeholder_upload" class="seopress-img-placeholder" data_caption="<?php esc_html_e('Click to select an image', 'wp-seopress-pro'); ?>">
    <img id="seopress_rich_snippets_publisher_logo_placeholder_src" src="<?php echo esc_attr($options_set); ?>" />
</div>

<?php
    if (isset($options['seopress_rich_snippets_publisher_logo'])) {
        esc_attr($options['seopress_rich_snippets_publisher_logo']);
    }
}
