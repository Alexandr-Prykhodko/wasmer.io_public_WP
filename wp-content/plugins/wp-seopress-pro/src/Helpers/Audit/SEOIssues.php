<?php

namespace SEOPressPro\Helpers\Audit;

if ( ! defined('ABSPATH')) {
    exit;
}

abstract class SEOIssues {
    public static function getData() {
        $data = [
            'all_canonical'=> [
                'title'  => __('Canonical URL', 'wp-seopress-pro'),
                'desc'   => null,
            ],
            'json_schemas'=> [
                'title'  => __('Structured data types', 'wp-seopress-pro'),
                'desc'   => null,
            ],
            'old_post'=> [
                'title'  => __('Last modified date', 'wp-seopress-pro'),
                'desc'   => __('Search engines love fresh content. Update regularly your articles without entirely rewriting your content and give them a boost in search rankings.', 'wp-seopress-pro'),
            ],
            'permalink'=> [
                'title'  => __('Keywords in permalink', 'wp-seopress-pro'),
                'desc'   => null,
            ],
            'headings'=> [
                'title'  => __('Headings', 'wp-seopress-pro'),
                'desc'   => null,
            ],
            'title'=> [
                'title'  => __('Meta title', 'wp-seopress-pro'),
                'desc'   => null,
            ],
            'description'=> [
                'title'  => __('Meta description', 'wp-seopress-pro'),
                'desc'   => null,
            ],
            'social'=> [
                'title'  => __('Social meta tags', 'wp-seopress-pro'),
                'desc'   => null,
            ],
            'robots'=> [
                'title'  => __('Meta robots', 'wp-seopress-pro'),
                'desc'   => null,
            ],
            'img_alt'=> [
                'title'  => __('Alternative texts of images', 'wp-seopress-pro'),
                'desc'   => __('No alternative text found for these images. Alt tags are important for both SEO and accessibility. Edit your images using the media library or your favorite page builder and fill in alternative text fields.', 'wp-seopress-pro'),
            ],
            'nofollow_links'=> [
                'title'  => __('NoFollow Links', 'wp-seopress-pro'),
                'desc'   => null,
            ],
            'outbound_links'=> [
                'title'  => __('Outbound Links', 'wp-seopress-pro'),
                'desc'   => null,
            ],
            'internal_links'=> [
                'title'  => __('Internal Links', 'wp-seopress-pro'),
                'desc'   => null,
            ],
        ];

        return $data;
    }
}
