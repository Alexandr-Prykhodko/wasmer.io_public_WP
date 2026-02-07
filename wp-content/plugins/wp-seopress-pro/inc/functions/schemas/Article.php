<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_automatic_rich_snippets_articles_option($schema_datas) {

    //if no data
    if (0 != count(array_filter($schema_datas))) {
        $article_type = $schema_datas['type'];
        $article_title = $schema_datas['title'];
        $article_desc = $schema_datas['desc'];
        $article_author = $schema_datas['author'];
        $article_img = $schema_datas['img'];
        $article_coverage_start_date = $schema_datas['coverage_start_date'];
        $article_coverage_start_time = $schema_datas['coverage_start_time'];
        $article_coverage_end_date = $schema_datas['coverage_end_date'];
        $article_coverage_end_time = $schema_datas['coverage_end_time'];
        $article_speakable = $schema_datas['speakable'];

        $json = [
            '@context' => seopress_check_ssl() . 'schema.org/',
            '@type' => $article_type,
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
        ];

        $article_canonical = '' ;
        if (get_post_meta(get_the_ID(), '_seopress_robots_canonical', true)) {
            $article_canonical = get_post_meta(get_the_ID(), '_seopress_robots_canonical', true);
        } else {
            global $wp;
            if (isset($wp->request)) {
                $article_canonical = user_trailingslashit(home_url(add_query_arg([], $wp->request)));
            }
        }

        if (!empty($article_canonical)) {
            $json['mainEntityOfPage'] = [
                '@type' => 'WebPage',
                '@id' => $article_canonical,
            ];
        }

        $json['headline'] = $article_title;

        $author = get_the_author();
        if ('' != $article_author) {
            $author = $article_author;
        }

        $authorUrl = get_the_author_meta('user_url');

        if ( ! empty($author) && empty($authorUrl)) {
            $authorUrl = get_author_posts_url(get_the_author_meta('ID'));
        } elseif (is_author() && is_int(get_queried_object_id()) && empty($authorUrl)) {
            $authorUrl = get_author_posts_url(get_queried_object_id());
        }


        $json['author'] = [
            '@type' => 'Person',
            'name' => $author,
        ];

        if ( ! empty($authorUrl)) {
            $json['author']['url'] = $authorUrl;
        }

        if ('' != $article_img) {
            $json['image'] = [
                '@type' => 'ImageObject',
                'url' => $article_img,
            ];
        }

        if (!empty(seopress_get_service('SocialOption')->getSocialKnowledgeName())) {
            $json['publisher'] = [
                '@type' => 'Organization',
                'name' => seopress_get_service('SocialOption')->getSocialKnowledgeName(),
            ];
            if (!empty(seopress_pro_get_service('OptionPro')->getArticlesPublisherLogo())) {
                $json['publisher']['logo'] = [
                    '@type' => 'ImageObject',
                    'url' => seopress_pro_get_service('OptionPro')->getArticlesPublisherLogo(),
                    'width' => seopress_pro_get_service('OptionPro')->getArticlesPublisherLogoWidth(),
                    'height' => seopress_pro_get_service('OptionPro')->getArticlesPublisherLogoHeight(),
                ];

                if (empty(seopress_pro_get_service('OptionPro')->getArticlesPublisherLogoWidth()) && empty(seopress_pro_get_service('OptionPro')->getArticlesPublisherLogoHeight())) {
                    unset($json['publisher']['logo']['width']);
                    unset($json['publisher']['logo']['height']);
                }
            }

            $facebook = seopress_get_service('SocialOption')->getSocialAccountsFacebook();
            $twitter = seopress_get_service('SocialOption')->getSocialAccountsTwitter();
            $pinterest = seopress_get_service('SocialOption')->getSocialAccountsPinterest();
            $instagram = seopress_get_service('SocialOption')->getSocialAccountsInstagram();
            $youtube = seopress_get_service('SocialOption')->getSocialAccountsYoutube();
            $linkedin = seopress_get_service('SocialOption')->getSocialAccountsLinkedin();

            $extra = '';

            if (version_compare(SEOPRESS_VERSION, '6.5', '>=')) {
                $extra = seopress_get_service('SocialOption')->getSocialAccountsExtra();
            }

            $accounts = [];

            if ('' != $facebook) {
                array_push($accounts, $facebook);
            }
            if ('' != $twitter) {
                $twitter = 'https://twitter.com/' . $twitter;
                array_push($accounts, $twitter);
            }
            if ('' != $pinterest) {
                array_push($accounts, $pinterest);
            }
            if ('' != $instagram) {
                array_push($accounts, $instagram);
            }
            if ('' != $youtube) {
                array_push($accounts, $youtube);
            }
            if ('' != $linkedin) {
                array_push($accounts, $linkedin);
            }

            if ('' != $extra) {
                $extra = preg_split('/\r\n|\r|\n/', $extra);
                $accounts = array_merge($accounts, $extra);
            }

            if ( ! empty($accounts)) {
                $json['publisher']['sameAs'] = $accounts;
            }
        }

        if ($article_coverage_start_date && $article_coverage_start_time && 'LiveBlogPosting' == $article_type) {
            $json['coverageStartTime'] = $article_coverage_start_date . 'T' . $article_coverage_start_time;
        }

        if ($article_coverage_end_date && $article_coverage_end_time && 'LiveBlogPosting' == $article_type) {
            $json['coverageEndTime'] = $article_coverage_end_date . 'T' . $article_coverage_end_time;
        }

        if ('ReviewNewsArticle' == $article_type) {
            $json['itemReviewed'] = [
                '@type' => 'Thing',
                'name' => get_the_title(),
            ];
        }

        $desc = wp_trim_words(esc_html(get_the_excerpt()), 30);
        if ('' != $article_desc) {
            $desc = $article_desc;
        }

        $json['description'] = $desc;

        if ('' != $article_speakable) {
            $json['speakable'] = [
                '@type' => 'SpeakableSpecification',
                'cssSelector' => $article_speakable,
            ];
        }

        $json = array_filter($json);

        $json = apply_filters('seopress_schemas_auto_article_json', $json);

        $json = '<script type="application/ld+json">' . wp_json_encode($json) . '</script>' . "\n";

        $json = apply_filters('seopress_schemas_auto_article_html', $json);

        echo $json;
    }
}
