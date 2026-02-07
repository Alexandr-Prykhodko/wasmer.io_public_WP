<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_print_section_info_breadcrumbs()
{
    seopress_print_pro_section('breadcrumbs'); ?>

    <div class="seopress-notice">
        <h3><?php esc_html_e('How to install the Breadcrumbs?', 'wp-seopress-pro'); ?></h3>

        <div id="seopress-breadcrumbs-notice">
            <div>
                <span class="dashicons dashicons-block-default"></span>
                <h4><?php esc_html_e('Block Editor / FSE', 'wp-seopress-pro'); ?></h4>
                <p><?php echo wp_kses_post(__('Add the Breadcrumbs block using the <strong>Block Editor</strong> or the <strong>Full Site Editing</strong> feature.', 'wp-seopress-pro')); ?></p>
            </div>

            <div>
                <span class="dashicons dashicons-shortcode"></span>
                <h4><?php esc_html_e('Shortcode', 'wp-seopress-pro'); ?></h4>
                <p><?php esc_html_e('You can also use this shortcode in your content (post, page, post type...):', 'wp-seopress-pro'); ?></p>

                <pre>[seopress_breadcrumbs]</pre>
            </div>

            <div>
                <span class="dashicons dashicons-editor-code"></span>
                <h4><?php esc_html_e('PHP template', 'wp-seopress-pro'); ?></h4>
                <p><?php esc_html_e('Copy and paste this function into your theme (e.g. header.php) to enable your breadcrumbs:', 'wp-seopress-pro'); ?></p>

                <pre>&lt;?php if(function_exists('seopress_display_breadcrumbs')) { seopress_display_breadcrumbs(); } ?&gt;</pre>

                <p><?php echo wp_kses_post(__('This function accepts 1 parameter: <strong>true / false</strong> for <strong>echo / return</strong>. Default: <strong>true</strong>.', 'wp-seopress-pro')); ?></p>

            </div>
        </div>
        <p>
            <a class="seopress-help" href="https://www.youtube.com/watch?v=YP6QG9qO0ps" target="_blank">
                <?php esc_html_e('Watch this video guide to easily integrate your breadcrumbs with your WordPress theme', 'wp-seopress-pro'); ?>
            </a>
            <span class="seopress-help dashicons dashicons-external"></span>
        </p>
    </div>

    <?php
    //Elementor
    if (did_action('elementor/loaded')) {
    ?>

        <div class="seopress-notice">
            <h3><?php esc_html_e('Elementor user?', 'wp-seopress-pro'); ?></h3>
            <p><?php echo wp_kses_post(__('We also provide a widget for <strong>Elementor users</strong> (Elementor Builder > Elements tab > Site section > Breadcrumbs widget).', 'wp-seopress-pro')); ?>

            <a class="seopress-help" href="https://www.youtube.com/watch?v=ID4xm1UVikc" target="_blank">
                <?php esc_html_e('Click to watch the video tutorial','wp-seopress-pro'); ?>
            </a>
            <span class="seopress-help dashicons dashicons-external"></span>
        </p>
        </div>

    <?php
    } ?>


<?php
}

function seopress_print_section_info_breadcrumbs_enable()
{
?>
    <hr>

    <h3 id="seopress-breadcrumbs-enable"><?php esc_html_e('Enable', 'wp-seopress-pro'); ?></h3>
<?php
}

function seopress_print_section_info_breadcrumbs_customize()
{
?>
    <hr>

    <h3 id="seopress-breadcrumbs-customize"><?php esc_html_e('Customize', 'wp-seopress-pro'); ?></h3>
<?php
}

function seopress_print_section_info_breadcrumbs_i18n()
{
    $docs = seopress_get_docs_links();
?>
    <hr>

    <h3 id="seopress-breadcrumbs-translations"><?php esc_html_e('Translations', 'wp-seopress-pro'); ?></h3>

    <p class="description">
        <a href="<?php echo esc_url($docs['breadcrumbs']['i18n']); ?>" class="seopress-help" target="_blank">
            <?php esc_html_e('Learn how to translate these options with multilingual plugins', 'wp-seopress-pro'); ?>
        </a>
        <span class="seopress-help dashicons dashicons-external"></span>
    </p>
<?php
}

function seopress_print_section_info_breadcrumbs_misc()
{
?>
    <hr>

    <h3 id="seopress-breadcrumbs-misc"><?php esc_html_e('Misc', 'wp-seopress-pro'); ?></h3>
<?php
}
