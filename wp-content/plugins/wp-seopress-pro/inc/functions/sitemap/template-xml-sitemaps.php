<?php
defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//Index News to Index XML Sitemap
add_filter('seopress_sitemaps_xml_index_item', function($seopress_sitemaps, $home_url) {
    if (method_exists(seopress_pro_get_service('OptionPro'), 'getGoogleNewsEnable') && '1' === seopress_pro_get_service('OptionPro')->getGoogleNewsEnable()
    && function_exists('seopress_get_toggle_option') && '1' == seopress_get_toggle_option('news')) {
        //Include Custom Post Types
        $news_cpt = seopress_pro_get_service('OptionPro')->getGoogleNewsPostTypesList();
	    if (!empty($news_cpt)) {
            $seopress_xml_sitemap_news_cpt_array = [];
            foreach ($news_cpt as $cpt_key => $cpt_value) {
                foreach ($cpt_value as $_cpt_key => $_cpt_value) {
                    if ('1' == $_cpt_value) {
                        array_push($seopress_xml_sitemap_news_cpt_array, $cpt_key);
                    }
                }
            }
        }

        $args = [
            'post_type'           => $seopress_xml_sitemap_news_cpt_array,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true,
            'posts_per_page'      => 1,
            'orderby'             => 'modified',
            'meta_query'          => [
                [
                    'key'     => '_seopress_robots_index',
                    'value'   => 'yes',
                    'compare' => 'NOT EXISTS',
                ],
            ],
            'order'        => 'DESC',
            'lang'         => '',
            'has_password' => false,
        ];

        //Polylang: exclude hidden languages
        $args = seopress_pll_exclude_hidden_lang($args);

        $args = apply_filters('seopress_sitemaps_index_gnews_query', $args);

        $get_latest_post = new WP_Query($args);
        if ($get_latest_post->have_posts()) {
            $seopress_sitemaps .= "\n";
            $seopress_sitemaps .= '<sitemap>';
            $seopress_sitemaps .= "\n";
            $seopress_sitemaps .= '<loc>';
            $seopress_sitemaps .= $home_url . 'news.xml';
            $seopress_sitemaps .= '</loc>';
            $seopress_sitemaps .= "\n";
            $seopress_sitemaps .= '<lastmod>';
            $seopress_sitemaps .= date('c', strtotime($get_latest_post->posts[0]->post_modified));
            $seopress_sitemaps .= '</lastmod>';
            $seopress_sitemaps .= "\n";
            $seopress_sitemaps .= '</sitemap>';
        }
    }

    return $seopress_sitemaps;
}, 10, 2);

//Add Video to Index XML Sitemap
add_filter('seopress_sitemaps_xml_index_item', function($seopress_sitemaps, $home_url) {
    if (method_exists(seopress_pro_get_service('SitemapOptionPro'), 'getSitemapVideoEnable') && '1' === seopress_pro_get_service('SitemapOptionPro')->getSitemapVideoEnable()) {
        if (!empty(seopress_get_service('SitemapOption')->getPostTypesList())) {
            $cpt = [];
            foreach (seopress_get_service('SitemapOption')->getPostTypesList() as $cpt_key => $cpt_value) {
                foreach ($cpt_value as $_cpt_key => $_cpt_value) {
                    if ('1' == $_cpt_value) {
                        $cpt[] = $cpt_key;
                    }
                }
            }
        }

        $args = [
            'post_type'           => $cpt,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true,
            'posts_per_page'      => -1,
            'meta_query'          => [
                'relation' => 'AND',
                [
                    'relation' => 'OR',
                    [
                        'key'     => '_seopress_robots_index',
                        'value'   => '',
                        'compare' => 'NOT EXISTS',
                    ],
                    [
                        'key'     => '_seopress_robots_index',
                        'value'   => 'yes',
                        'compare' => '!=',
                    ],
                ],
                [
                    'key'     => '_seopress_video',
                    'compare' => 'EXISTS',
                ],
            ],
            'lang'         => '',
            'has_password' => false,
            'fields'       => 'ids',
        ];

        //Polylang: exclude hidden languages
        $args = seopress_pll_exclude_hidden_lang($args);

        $args = apply_filters('seopress_sitemaps_index_video_query', $args, $cpt_key);

        $ids  = get_posts($args);

        $args = [
            'post_type'           => $cpt,
            'post_status'         => 'publish',
            'posts_per_page'      => -1,
            'post__in'            => $ids,
            'meta_query'          => [
                [
                    'relation' => 'OR',
                    [
                        'key'     => '_seopress_video_disabled',
                        'value'   => '',
                        'compare' => 'NOT EXISTS',
                    ],
                    [
                        'key'     => '_seopress_video_disabled',
                        'value'   => 'yes',
                        'compare' => '!=',
                    ],
                ],
            ],
            'lang'         => '',
            'fields'       => 'ids',
        ];

        //Polylang: exclude hidden languages
        $args = seopress_pll_exclude_hidden_lang($args);

        $posts       = get_posts($args);
        $count_posts = count($posts);

        $idsVideos = get_transient('_seopress_sitemap_ids_video');
        if ( ! $idsVideos) {
            set_transient('_seopress_sitemap_ids_video', $posts, 3600);
        }

        //Max posts per paginated sitemap
        $max = 1000;
        $max = apply_filters('seopress_sitemaps_max_videos_per_sitemap', $max);

        if ($count_posts >= $max) {
            $max_loop = $count_posts / $max;
        } else {
            $max_loop = 1;
        }

        $paged ='';
        $i     = '';
        for ($i=0; $i < $max_loop; ++$i) {
            if (isset($offset) && absint($offset) && '' != $offset && 0 != $offset) {
                $offset = ((($i) * $max));
            } else {
                $offset = 0;
            }

            if ($i >= 1 && $i <= $max_loop) {
                $paged = $i + 1;
            } else {
                $paged = 1;
            }

            $seopress_sitemaps .= "\n";
            $seopress_sitemaps .= '<sitemap>';
            $seopress_sitemaps .= "\n";
            $seopress_sitemaps .= '<loc>';
            $seopress_sitemaps .= $home_url . 'video' . $paged . '.xml';
            $seopress_sitemaps .= '</loc>';
            $seopress_sitemaps .= "\n";
            $seopress_sitemaps .= '</sitemap>';
        }
    }

    return $seopress_sitemaps;
}, 10, 2);
