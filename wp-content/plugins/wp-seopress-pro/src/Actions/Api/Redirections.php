<?php

namespace SEOPressPro\Actions\Api;

if (! defined('ABSPATH')) {
    exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

class Redirections implements ExecuteHooks
{
    private $current_user;
    
    public function hooks()
    {
        $this->current_user = wp_get_current_user()->ID;
        add_action('rest_api_init', [$this, 'register']);
    }

    /**
     * @since 8.8.0
     *
     * @return void
     */
    public function register()
    {
        register_rest_route('seopress/v1', '/redirections', [
            'methods'             => 'GET',
            'callback'            => [$this, 'processGetAll'],
            'args'                => [
                'id' => [
                    'validate_callback' => function ($param, $request, $key) {
                        return is_numeric($param);
                    },
                ],
                'enabled' => [
                    'validate_callback' => function ($param, $request, $key) {
                        if ($param === 'yes' || $param === 'no') {
                            return $param;
                        }
                        return false;
                    },
                ],
                'type' => [
                    'validate_callback' => function ($param, $request, $key) {
                        $types = [
                            '301',
                            '302',
                            '307',
                            '404',
                            '410',
                            '451'
                        ];

                        if (is_array($param)) {
                            return array_intersect($param, $types) === $param;
                        }
                        
                        return in_array($param, $types);
                    },
                ],
            ],
            'permission_callback' => function() {
                
                $current_user = $this->current_user ? $this->current_user : wp_get_current_user()->ID;

                if ( ! user_can( $current_user, 'read_redirection' )) {
                    return false;
                }
                
                return true;
            },
        ]);
    }

    /**
     * @since 8.8.0
     *
     * @param \WP_REST_Request $request
     */
    public function processGetAll(\WP_REST_Request $request)
    {
        $id = $request->get_param('id');
        $enabled = $request->get_param('enabled');
        $type = $request->get_param('type');

        $args = [
            'post_type'      => 'seopress_404',
            'posts_per_page' => '-1'
        ];

        if (!empty($id)) {
            $args['p'] = $id;
        }

        if (!empty($enabled)) {
            if ($enabled === 'yes') {
                $args['meta_query'] = [
                    [
                        'key'     => '_seopress_redirections_enabled',
                        'value'   => 'yes',
                        'compare' => '=',
                    ],
                ];
            } elseif ($enabled === 'no') {
                $args['meta_query'] = [
                    'relation' => 'OR',
                    [
                        'key'     => '_seopress_redirections_enabled',
                        'value'   => 'no',
                        'compare' => '=',
                    ],
                    [
                        'key'     => '_seopress_redirections_enabled',
                        'compare' => 'NOT EXISTS',
                    ],
                ];
            }
        }
        if (!empty($type)) {
            if (is_array($type)) {
                $args['meta_query'][] = [
                    'key'     => '_seopress_redirections_type',
                    'value'   => $type,
                    'compare' => 'IN',
                ];
            } else {
                $args['meta_query'][] = [
                    'key'     => '_seopress_redirections_type',
                    'value'   => $type,
                    'compare' => '=',
                ];
            }
        }
        
        $seopress_redirects_query = new \WP_Query($args);
        
        $posts = $seopress_redirects_query->posts;
        $response = [];
        
        foreach ($posts as $post) {
            $response[$post->ID] = [
                'origin' => $post->post_title,
                'destination' => get_post_meta($post->ID, '_seopress_redirections_value', true),
                'enabled' => get_post_meta($post->ID, '_seopress_redirections_enabled', true),
                'type' => get_post_meta($post->ID, '_seopress_redirections_type', true),
                'param' => get_post_meta($post->ID, '_seopress_redirections_param', true),
                'enabled_regex' => get_post_meta($post->ID, '_seopress_redirections_enabled_regex', true),
                'logged_status' => get_post_meta($post->ID, '_seopress_redirections_logged_status', true),
                'ip' => get_post_meta($post->ID, '_seopress_redirections_ip', true),
                'ua' => get_post_meta($post->ID, 'seopress_redirections_ua', true),
                'full_origin' => get_post_meta($post->ID, 'seopress_redirections_referer', true),
                'date_request' => get_post_meta($post->ID, '_seopress_redirections_date_request', true),
                'count' => get_post_meta($post->ID, 'seopress_404_count', true),
                'redirect_date_request' => get_post_meta($post->ID, '_seopress_404_redirect_date_request', true)
            ];
        }

        wp_send_json_success($response);
        return;
    }
}
