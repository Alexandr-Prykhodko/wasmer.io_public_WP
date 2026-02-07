<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_print_section_info_robots()
{
    seopress_print_pro_section('robots'); ?>

    <p>
        <a href="<?php echo esc_url(get_home_url() . '/robots.txt'); ?>" class="btn btnSecondary" target="_blank">
            <?php esc_html_e('View your robots.txt', 'wp-seopress-pro'); ?>
        </a>
        <span class="spinner"></span>
    </p>

    <div class="seopress-notice">
        <p>
            <?php /* translators: %1$s: get_home_url() */ echo wp_kses_post(sprintf(__('A <strong>robots.txt file</strong> lives at the root of your site. So, for site %1$s, the robots.txt file lives at %2$s.', 'wp-seopress-pro'), '<code>' . esc_url(get_home_url()) . '</code>', '<code>' . esc_url(get_home_url() . '/robots.txt') . '</code>')); ?>
        </p>

        <p>
            <?php echo wp_kses_post(__('robots.txt is a plain text file that follows the <strong>Robots Exclusion Standard</strong>.', 'wp-seopress-pro')); ?>
        </p>

        <p>
            <?php echo wp_kses_post(__('A robots.txt file consists of one or more rules. <strong>Each rule blocks (disallows or allows) access</strong> for a given crawler to a specified file path in that website.', 'wp-seopress-pro')); ?>
        </p>

        <p>
            <?php echo wp_kses_post(__('Our robots.txt file is <strong>virtual</strong> (like the default WordPress one). It means it‘s not physically present on your server. It‘s generated via <strong>URL rewriting</strong>.', 'wp-seopress-pro')); ?>
        </p>
    </div>

    <?php
        if (file_exists(ABSPATH . 'robots.txt') && '1') {
            ?>
            <div class="seopress-notice is-warning">
                <p>
                    <?php
                        echo wp_kses_post(__('A <strong>robots.txt</strong> file already exists at the root of your site. We invite you to remove it so we can handle it virtually.', 'wp-seopress-pro'));
                    ?>
                </p>
            </div>
        <?php
    }
}
