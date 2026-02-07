<?php

namespace SEOPressPro\Actions\Sitemap;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPress\Core\Hooks\ExecuteHooks;

class RouterNewsSitemap implements ExecuteHooks {
    /**
     * @since 4.3.0
     *
     * @return void
     */
    public function hooks() {
        add_action('init', [$this, 'init']);
        add_action('query_vars', [$this, 'queryVars']);
    }

    /**
     * @since 4.3.0
     * @see init
     *
     * @return void
     */
    public function init() {
        if ('1' !== seopress_pro_get_service('OptionPro')->getGoogleNewsEnable() || ! function_exists('seopress_get_toggle_option') || '1' !== seopress_get_toggle_option('news')) {
            return;
        }

        //XSL Sitemap
        add_rewrite_rule('^sitemaps_xsl.xsl$', 'index.php?seopress_sitemap_xsl=1', 'top');

        //Google News
        add_rewrite_rule('news.xml?$', 'index.php?seopress_news=1', 'top');
    }

    /**
     * @since 4.3.0
     * @see query_vars
     *
     * @param array $vars
     *
     * @return array
     */
    public function queryVars($vars) {
        if ('1' !== seopress_pro_get_service('OptionPro')->getGoogleNewsEnable() || ! function_exists('seopress_get_toggle_option') || '1' !== seopress_get_toggle_option('news')) {
            return $vars;
        }

        $vars[] = 'seopress_sitemap_xsl';
        $vars[] = 'seopress_news';

        return $vars;
    }
}
