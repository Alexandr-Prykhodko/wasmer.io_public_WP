<?php
defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

///////////////////////////////////////////////////////////////////////////////////////////////////
//SEOPRESS Core
///////////////////////////////////////////////////////////////////////////////////////////////////
//Import / Export tool
add_action('init', 'seopress_pro_enable', 999);
function seopress_pro_enable()
{
    if (is_admin()) {
        if (is_plugin_active('wp-seopress/seopress.php') && defined('SEOPRESS_VERSION')) {
            require_once dirname(__FILE__) . '/options-import-export.php'; //Import Export
        }
    }
}

//Local Business
if ('1' == seopress_get_toggle_option('local-business')) {
    //Register Local Business widget
    add_action('widgets_init', 'seopress_pro_lb_register_widget');
    function seopress_pro_lb_register_widget() {
        require_once dirname(__FILE__) . '/options-local-business-widget.php'; //Local Business
        register_widget('Local_Business_Widget');
    }
}

//WooCommerce
if ('1' == seopress_get_toggle_option('woocommerce')) {
    add_action('init', 'seopress_pro_woocommerce_sitemap', 0);
    function seopress_pro_woocommerce_sitemap() {
        if ( ! is_admin()) {
            require_once dirname(__FILE__) . '/options-woocommerce-sitemap.php'; //WooCommerce sitemap
        } else {
            require_once dirname(__FILE__) . '/options-woocommerce-admin.php'; //WooCommerce in admin
        }
    }
    //add_action('get_header', 'seopress_pro_woocommerce', 0);
    add_action('wp_head', 'seopress_pro_woocommerce', 0);
    function seopress_pro_woocommerce() {
        if ( ! is_admin()) {
            require_once dirname(__FILE__) . '/options-woocommerce.php'; //WooCommerce
        }
    }
}

//EDD
if ('1' == seopress_get_toggle_option('edd')) {
    add_action('wp_head', 'seopress_pro_edd', 0);
    function seopress_pro_edd() {
        if ( ! is_admin()) {
            require_once dirname(__FILE__) . '/options-edd.php'; //EDD
        }
    }
}

//Dublin Core
if ('1' == seopress_get_toggle_option('dublin-core')) {
    add_action('wp_head', 'seopress_pro_dublin_core', 0);
    function seopress_pro_dublin_core() {
        if ( ! is_admin()) {
            if ((function_exists('is_wpforo_page') && is_wpforo_page()) || (class_exists('Ecwid_Store_Page') && \Ecwid_Store_Page::is_store_page())) {//disable on wpForo pages to avoid conflicts
                //do nothing
            } else {
                require_once dirname(__FILE__) . '/options-dublin-core.php'; //Dublin Core
            }
        }
    }
}

//Rich Snippets
if ('1' == seopress_get_toggle_option('rich-snippets')) {
    add_action('wp_head', 'seopress_pro_rich_snippets', 2); // Must be !=0
    function seopress_pro_rich_snippets() {
        if ( ! is_admin()) {
            require_once dirname(__FILE__) . '/options-automatic-rich-snippets.php'; //Automatic Rich Snippets
        }
    }
    add_action('init', 'seopress_load_schemas_options', 9);
    function seopress_load_schemas_options() {
        require_once dirname(dirname(__FILE__)) . '/admin/schemas/schemas.php'; //Schemas
    }
    function seopress_pro_schemas_notice() {
        global $typenow;
        if (current_user_can(seopress_capability('manage_options', 'notice')) && (isset($typenow) && 'seopress_schemas' === $typenow)) {
            if ('1' !== seopress_pro_get_service('OptionPro')->getRichSnippetEnable()) {
                ?>
<div class="seopress-notice is-error">
    <p>
        <?php _e('Please enable <strong>Structured Data Types metabox for your posts, pages and custom post types</strong> option in order to use automatic schemas. (SEO > PRO > Structured Data Types (schema.org)', 'wp-seopress-pro'); ?>
        <a href="<?php echo esc_url(admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_rich_snippets')); ?>"
            class="btn btnPrimary"><?php _e('Fix this!', 'wp-seopress-pro'); ?></a>
    </p>
</div>
<?php
            }
        }
    }
    add_action('admin_notices', 'seopress_pro_schemas_notice');
}

//Redirections
if ('1' === seopress_get_toggle_option('404')) {
    require_once dirname(__FILE__) . '/redirections/redirections.php';
}

//Breadcrumbs
if ('1' == seopress_get_toggle_option('breadcrumbs')) {
    //Breadcrumbs JSON-LD
    if ('1' === seopress_pro_get_service('OptionPro')->getBreadcrumbsJsonEnable()) {
        //WooCommerce / Storefront with Breadcrumbs
        add_action('init', 'seopress_pro_compatibility_wc');
        function seopress_pro_compatibility_wc() {
            //If WooCommerce, disable default JSON-LD Breadcrumbs to avoid conflicts
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
            if (is_plugin_active('woocommerce/woocommerce.php')) {
                add_action('woocommerce_structured_data_breadcrumblist', '__return_false', 10, 2);
                remove_action('storefront_before_content', 'woocommerce_breadcrumb', 10);
            }
        }
    }
    require_once dirname(__FILE__) . '/options-breadcrumbs.php'; //Breadcrumbs
}

//RSS
add_action('init', 'seopress_pro_rss', 0);
function seopress_pro_rss() {
    if ( ! is_admin()) {
        require_once dirname(__FILE__) . '/options-rss.php'; //RSS
    }
}

//Rewrite
if ('1' === seopress_get_toggle_option('advanced')) {
    add_action('init', 'seopress_pro_rewrite', 0);
    function seopress_pro_rewrite() {
        require_once dirname(__FILE__) . '/options-rewrite.php'; //Rewrite
    }
}

//White Label
if (method_exists(seopress_get_service('ToggleOption'), 'getToggleWhiteLabel') && '1' === seopress_get_service('ToggleOption')->getToggleWhiteLabel()) {
    if (is_admin() || is_network_admin()) {
        require_once dirname(__FILE__) . '/options-white-label.php'; //White Label
    }
}

//Robots
if (function_exists('seopress_get_toggle_option') && '1' === seopress_get_toggle_option('robots')) {
    require_once dirname(__FILE__) . '/options-robots-txt.php'; //Robots.txt
}

//Video XML sitemaps
if ('1' === seopress_get_toggle_option('xml-sitemap') && '1' === seopress_get_service('SitemapOption')->isEnabled() && '1' === seopress_pro_get_service('SitemapOptionPro')->getSitemapVideoEnable()) {
    add_action('save_post', 'seopress_pro_video_xml_sitemap', 10, 3);
    function seopress_pro_video_xml_sitemap($post_id, $post, $update = '') {
        require_once dirname(__FILE__) . '/options-video-sitemap.php';
    }
}

//AI
if ('1' == seopress_get_toggle_option('ai')) {
    add_action('init', 'seopress_pro_ai', 11);
    function seopress_pro_ai() {
        if ( is_admin()) {
            require_once dirname(__FILE__) . '/options-ai-admin.php'; //AI
        }
        require_once dirname(__FILE__) . '/options-ai.php'; //AI
    }
}

//GA4 Ecommerce
add_action('seopress_ga4_before_sending_data', 'seopress_pro_ga_ecommerce');
function seopress_pro_ga_ecommerce() {
    if ( ! is_admin()) {
        require_once dirname(__FILE__) . '/options-google-ecommerce.php'; //GA4 Ecommerce
    }
}
