<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_print_section_info_edd()
{
    seopress_print_pro_section('edd');

    if (!is_plugin_active('easy-digital-downloads/easy-digital-downloads.php')) { ?>

        <div class="seopress-notice is-warning">
            <p>
                <?php echo wp_kses_post(__('You need to enable <strong>Easy Digital Downloads</strong> to apply these settings.', 'wp-seopress-pro')); ?>
            </p>
        </div>

<?php
    }
}
