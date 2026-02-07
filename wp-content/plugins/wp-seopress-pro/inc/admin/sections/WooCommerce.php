<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_print_section_info_woocommerce()
{
    seopress_print_pro_section('woocommerce');
?>
    <?php
    if (!is_plugin_active('woocommerce/woocommerce.php')) { ?>

        <div class="seopress-notice is-warning">
            <p><?php echo wp_kses_post(__('You need to enable <strong>WooCommerce</strong> to apply these settings.', 'wp-seopress-pro')); ?></p>
        </div>

<?php
    }
}
