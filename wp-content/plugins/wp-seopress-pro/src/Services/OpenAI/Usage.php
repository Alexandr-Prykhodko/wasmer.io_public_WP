<?php

namespace SEOPressPro\Services\OpenAI;

defined('ABSPATH') || exit;

class Usage {
    private const OPENAI_URL_USAGE = 'https://api.openai.com/v1/usage';
    private const OPENAI_URL_CHAT_COMPLETIONS = 'https://api.openai.com/v1/chat/completions';

    public function getLicenseKey() {
        $options = get_option('seopress_pro_option_name');

        $api_key = '';

        if (defined( 'SEOPRESS_OPENAI_KEY' ) && ! empty( SEOPRESS_OPENAI_KEY ) && is_string( SEOPRESS_OPENAI_KEY )) {
            $api_key = SEOPRESS_OPENAI_KEY;
        } else {
            $api_key = isset($options['seopress_ai_openai_api_key']) ? $options['seopress_ai_openai_api_key'] : '';
        }

        return $api_key;
    }

    public function checkLicenseKeyExists() {
        $api_key = $this->getLicenseKey();

        if ($api_key ==='xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' || empty($api_key)) {
            $data = [
                'code' => 'error',
                'message' => sprintf(__('Your OpenAI API key has not been entered correctly. Please paste it again from OpenAI website.', 'wp-seopress-pro'), esc_html($httpCode))
            ];

            return $data;
        }

        $params = array(
            'date' => date('Y-m-d'),
        );

        $url = self::OPENAI_URL_USAGE;

        $url = add_query_arg($params, $url);

        $response = wp_remote_get($url, array(
            'headers' => [
                'Authorization' => 'Bearer ' . $api_key,
            ],
        ));

        if (!is_wp_error($response)) {
            $httpCode = wp_remote_retrieve_response_code($response);

            //Logs
            set_transient('seopress_pro_ai_logs', wp_remote_retrieve_body($response), 30 * DAY_IN_SECONDS);

            $data = [];
            if ($httpCode === 200) {
                $data = [
                    'code' => 'success',
                    'message' => __('Your OpenAI API key is valid.', 'wp-seopress-pro')
                ];
            } else {
                $data = [
                    'code' => 'error',
                    'message' => sprintf(__('Your OpenAI API key is invalid or has expired. Error: %s', 'wp-seopress-pro'), esc_html($httpCode))
                ];
            }
            return $data;
        }
    }

    public function checkLicenseKeyExpiration() {
        $api_key = $this->getLicenseKey();

        $options = get_option('seopress_pro_option_name');
        $model = isset($options['seopress_ai_openai_model']) ? $options['seopress_ai_openai_model'] : 'gpt-4o';

        $url = self::OPENAI_URL_CHAT_COMPLETIONS;

        $body = [
            'model'    => $model,
            'temperature' => 1,
            'max_tokens' => 220,
        ];

        $body['messages'][] = ['role' => 'user', 'content' => 'Prompt test to check if my OpenAI key works has not expired'];

        $args = [
            'body'        => wp_json_encode($body),
            'timeout'     => '30',
            'redirection' => '5',
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers' => [
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json'
            ],
        ];

        $response = wp_remote_post($url, $args);

        if (!is_wp_error($response)) {
            $httpCode = wp_remote_retrieve_response_code($response);

            //Logs
            set_transient('seopress_pro_ai_logs', wp_remote_retrieve_body($response), 30 * DAY_IN_SECONDS);

            $data = [];
            if ($httpCode === 200) {
                $data = [
                    'code' => 'success',
                    'message' => __('Your OpenAI API key is valid.', 'wp-seopress-pro')
                ];
            } else {
                $data = [
                    'code' => 'error',
                    'message' => sprintf(__('Your OpenAI API key is invalid or has expired. Error: %1$s. Go to your <a href="%2$s" target="_blank">OpenAI Usage page</a> to check this.', 'wp-seopress-pro'), esc_html($httpCode), esc_url('https://platform.openai.com/account/usage'))
                ];
            }
            return $data;
        }
    }
}
