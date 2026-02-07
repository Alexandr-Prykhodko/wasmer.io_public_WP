<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

//Dublin Core
//=================================================================================================
//DC Tags
if (seopress_pro_get_service('OptionPro')->getDublinCoreEnable() ==='1') { //Is DC enable
    if (is_singular() || is_home()) { //Is Singular (post, page, cpt)
        function seopress_dublin_core_title_hook() {
            if (function_exists('seopress_titles_the_title') && seopress_titles_the_title() !='') {
                $seopress_dublin_core_title = '<meta name="dc.title" content="'.seopress_titles_the_title().'">';

                //Hook on post Dublin Core Title - 'seopress_dublin_core_title'
                if (has_filter('seopress_dublin_core_title')) {
                    $seopress_dublin_core_title = apply_filters('seopress_dublin_core_title', $seopress_dublin_core_title);
                }
                echo $seopress_dublin_core_title."\n";
            }
        }
        add_action( 'wp_head', 'seopress_dublin_core_title_hook', 1 );
        //DC Description
        function seopress_dublin_core_description_hook() {
            if (function_exists('seopress_titles_the_description_content') && seopress_titles_the_description_content() !='') {
                $seopress_dublin_core_desc = '<meta name="dc.description" content="'.seopress_titles_the_description_content().'">';

                //Hook on post Dublin Core Description - 'seopress_dublin_core_desc'
                if (has_filter('seopress_dublin_core_desc')) {
                    $seopress_dublin_core_desc = apply_filters('seopress_dublin_core_desc', $seopress_dublin_core_desc);
                }
                echo $seopress_dublin_core_desc."\n";
            }
        }
        add_action( 'wp_head', 'seopress_dublin_core_description_hook', 1 );

        //DC Relation
        function seopress_dublin_core_relation_hook() {
            //Init
            $seopress_dublin_core_relation ='';
            $page_id = get_option( 'page_for_posts' );

            if ( is_home() && get_post_meta($page_id,'_seopress_robots_canonical',true) !='') {
                $_seopress_robots_canonical = get_post_meta($page_id,'_seopress_robots_canonical',true);
                $seopress_dublin_core_relation = '<meta name="dc.relation" content="'.htmlspecialchars(urldecode($_seopress_robots_canonical)).'">';
            } elseif (get_post_meta(get_the_ID(),'_seopress_robots_canonical',true) !='') { //IS METABOXE
                $_seopress_robots_canonical = get_post_meta(get_the_ID(),'_seopress_robots_canonical',true);
                $seopress_dublin_core_relation = '<meta name="dc.relation" content="'.htmlspecialchars(urldecode($_seopress_robots_canonical)).'">';
            } else {
                global $wp;
                $current_url = user_trailingslashit(home_url(add_query_arg(array(), $wp->request)));
                $seopress_dublin_core_relation = '<meta name="dc.relation" content="'.htmlspecialchars(urldecode($current_url)).'">';
            }
            //Hook on post Dublin Core Relation - 'seopress_dublin_core_relation'
            if (has_filter('seopress_dublin_core_relation')) {
                $seopress_dublin_core_relation = apply_filters('seopress_dublin_core_relation', $seopress_dublin_core_relation);
            }

            if (isset($seopress_dublin_core_relation) && $seopress_dublin_core_relation !='') {
                echo $seopress_dublin_core_relation."\n";
            }
        }
        add_action( 'wp_head', 'seopress_dublin_core_relation_hook', 1 );

        //DC Source
        function seopress_dublin_core_source_hook() {
            $seopress_dublin_core_source = '<meta name="dc.source" content="'.htmlspecialchars(urldecode(user_trailingslashit(get_home_url()))).'">';

            //Hook on post Dublin Core Source - 'seopress_dublin_core_source'
            if (has_filter('seopress_dublin_core_source')) {
                $seopress_dublin_core_source = apply_filters('seopress_dublin_core_source', $seopress_dublin_core_source);
            }
            echo $seopress_dublin_core_source."\n";
        }
        add_action( 'wp_head', 'seopress_dublin_core_source_hook', 1 );

        //DC Language
        function seopress_dublin_core_language_hook() {
            if (function_exists('seopress_normalized_locale')) {
                $language = seopress_normalized_locale(get_locale());
            } else {
                $language = get_locale();
            }

            $seopress_dc_language = '<meta name="dc.language" content="' . $language . '">';

            //Hook on post Dublin Core Source - 'seopress_dublin_core_language'
            if (has_filter('seopress_dublin_core_language')) {
                $seopress_dc_language = apply_filters('seopress_dublin_core_language', $seopress_dc_language);
            }

            echo $seopress_dc_language."\n";
        }
        add_action( 'wp_head', 'seopress_dublin_core_language_hook', 1 );
    }
}
