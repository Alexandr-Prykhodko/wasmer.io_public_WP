<?php

namespace SEOPressPro\Services\OpenAI;

defined('ABSPATH') || exit;

class Completions {
	private const OPENAI_URL_CHAT_COMPLETIONS = 'https://api.openai.com/v1/chat/completions';

	/**
	 * Get OpenAI model from the SEOPress options.
	 *
	 * @return string $model the OpenAI model name
	 */
	public function getOpenAIModel() {
		$options = get_option('seopress_pro_option_name');
		$model = isset($options['seopress_ai_openai_model']) ? $options['seopress_ai_openai_model'] : 'gpt-4o';

		return $model;
	}

	/**
	 * Generate titles and descriptions for a post.
	 *
	 * This function generates titles and descriptions based on the provided parameters.
	 *
	 * @param int    $post_id   The ID of the post for which to generate titles and descriptions.
	 * @param string $meta      title|desc (optional).
	 * @param string $language  The language for generating titles and descriptions (default is 'en_US').
	 * @param bool   $autosave  Whether this is an autosave operation, useful for bulk actions (default is false).
	 *
	 * @return array $data The answers from OpenAI with title/desc
	 */
	public function generateTitlesDesc($post_id, $meta = '', $language = 'en_US', $autosave = false) {
		//Init
		$title = '';
		$description = '';
		$message = '';
		$language = get_locale();

		$content = get_post_field('post_content', $post_id);
		$content = esc_attr(stripslashes_deep(wp_filter_nohtml_kses(wp_strip_all_tags(strip_shortcodes($content)))));

		//Compatibility with current page and theme builders
		$theme = wp_get_theme();

		//Divi
		if ('Divi' == $theme->template || 'Divi' == $theme->parent_theme) {
			$regex = '/\[(\[?)(et_pb_[^\s\]]+)(?:(\s)[^\]]+)?\]?(?:(.+?)\[\/\2\])?|\[\/(et_pb_[^\s\]]+)?\]/';
			$content = preg_replace($regex, '', $content);
		}

		//Bricks compatibility
		if (defined('BRICKS_DB_EDITOR_MODE') && ('bricks' == $theme->template || 'Bricks' == $theme->parent_theme)) {
			$page_sections = get_post_meta($post_id, BRICKS_DB_PAGE_CONTENT, true);
			$editor_mode   = get_post_meta($post_id, BRICKS_DB_EDITOR_MODE, true);

			if (is_array($page_sections) && 'wordpress' !== $editor_mode) {
				$content = \Bricks\Frontend::render_data($page_sections);
			}
		}

		//Limit post content sent to 500 words (higher value will return a 400 error)
		$content = wp_trim_words( $content, 500 );

		//If no post_content use the permalink
		if (empty($content)) {
			$content = get_permalink($post_id);
		}

		$body = [
			'model'    => $this->getOpenAIModel(),
			'temperature' => 1,
			'max_tokens' => 220,
            'response_format' => [
                'type' => 'json_object'
            ],
		];

		$body['messages'] = [];

		//Get current post language for bulk actions
		if ($meta === 'title' || $meta === 'desc') {
			//Default
			if (function_exists('seopress_normalized_locale')) {
				$language = seopress_normalized_locale(get_locale());
			} else {
				$language = get_locale();
			}

			//WPML
			if (defined('ICL_SITEPRESS_VERSION')) {
				$language = apply_filters( 'wpml_post_language_details', NULL, $post_id );
				$language = !empty($language['locale']) ? $language['locale'] : get_locale();
			}

			//Polylang
			if (function_exists('pll_get_post_language')) {
				$language = !empty(pll_get_post_language($post_id, 'locale')) ? pll_get_post_language($post_id, 'locale') : get_locale();
			}
		}

		//Convert language code to readable name
		$language = locale_get_display_name($language, 'en') ? esc_html(locale_get_display_name($language, 'en')) : $language;

		//Get target keywords
		$target_keywords = !empty(get_post_meta($post_id, '_seopress_analysis_target_kw', true)) ? get_post_meta($post_id, '_seopress_analysis_target_kw', true) : null;

		//Prompt for meta title
		$prompt_title = sprintf(__('Generate, in this language %1$s, an engaging SEO title metadata in one sentence of sixty characters maximum, with at least one of these keywords in the prompt response: "%2$s", based on this content: %3$s.', 'wp-seopress-pro'), esc_attr($language), esc_html($target_keywords), esc_html($content));

		$msg   = apply_filters( 'seopress_ai_openai_meta_title', $prompt_title, $post_id );

		if (empty($meta) || $meta === 'title') {
			$body['messages'][] = ['role' => 'user', 'content' => $msg];
		}

		//Prompt for meta description
		$prompt_desc = sprintf(__('Generate, in this language ' . $language . ', an engaging SEO meta description in less than 160 characters, with at least one of these keywords in the prompt response: "%2$s", based on this content: %3$s.', 'wp-seopress-pro'), esc_attr($language), esc_html($target_keywords), esc_html($content));

		$msg   = apply_filters( 'seopress_ai_openai_meta_desc', $prompt_desc, $post_id );

		if (empty($meta) || $meta === 'desc') {
			$body['messages'][] = ['role' => 'user', 'content' => $msg];
		}

		$body['messages'][] = ['role' => 'user', 'content' => 'Provide the answer as a JSON object with "title" as first key and "desc" for second key for parsing in this language ' . $language . '. You must respect the grammar and typing of the language.'];

		$args = [
			'body'        => wp_json_encode($body),
			'timeout'     => '30',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers' => [
				'Authorization' => 'Bearer ' . seopress_pro_get_service('Usage')->getLicenseKey(),
				'Content-Type' => 'application/json'
			],
		];

		$args = apply_filters('seopress_ai_openai_request_args', $args);

		$url = self::OPENAI_URL_CHAT_COMPLETIONS;

		$response = wp_remote_post( $url, $args );

		// make sure the response came back okay
		if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
			if (is_wp_error($response)) {
				$message = $response->get_error_message();
			} else {
				$message = __('An error occurred, please try again. Response code: ', 'wp-seopress-pro') . wp_remote_retrieve_response_code($response);
			}
            //Logs
            set_transient('seopress_pro_ai_logs', wp_remote_retrieve_body($response), 30 * DAY_IN_SECONDS);
		} else {
			$data = json_decode(wp_remote_retrieve_body($response));

			$message = 'Success';

			if (empty($meta) || $meta === 'title') {
				$result = json_decode($data->choices[0]->message->content, true);
				$result = $result['title'];

				$title = esc_attr(trim(stripslashes_deep(wp_filter_nohtml_kses(wp_strip_all_tags(strip_shortcodes($result)))), '"'));

				if ($autosave === true) {
					update_post_meta( $post_id, '_seopress_titles_title', sanitize_text_field(html_entity_decode($title)));
				}
			}

			if (empty($meta)) {
				$result = json_decode($data->choices[0]->message->content, true);
				$result = $result['desc'];
			} elseif ($meta === 'desc') {
				$result = json_decode($data->choices[0]->message->content, true);
				$result = $result['desc'];
			}

			if (empty($meta) || $meta === 'desc') {
				$description = esc_attr(trim(stripslashes_deep(wp_filter_nohtml_kses(wp_strip_all_tags(strip_shortcodes($result)))), '"'));

				if ($autosave === true) {
					update_post_meta( $post_id, '_seopress_titles_desc', sanitize_textarea_field(html_entity_decode($description)));
				}
            }
		}

		$data = [
			'message' => $message,
			'title' => html_entity_decode($title),
			'desc' => html_entity_decode($description)
		];

		return $data;
	}

	/**
	 * Generate alt text for an image.
	 *
	 * This function generates the alternative text for an image file.
	 *
	 * @param int    $post_id   The ID of the post for which to generate titles and descriptions.
	 * @param string $action    The action to run (optional).
	 * @param string $language  The language for generating titles and descriptions (default is 'en_US').
	 * @param bool   $update_empty_alt_text  Whether to update empty alt text (default is true).
	 *
	 * @return array $data The answers from OpenAI with title/desc
	 */
	public function generateImgAltText($post_id, $action = '', $language = 'en_US', $update_empty_alt_text = true) {
		// Update empty alt text only
		$current_alt_text = get_post_meta($post_id, '_wp_attachment_image_alt', true);
		if (!empty($current_alt_text) && !$update_empty_alt_text) {
			return [
				'message' => 'Alt text already exists, no need to generate it.',
				'alt_text' => $current_alt_text,
			];
		}

		if ($action === 'alt_text') {
			//Default
			if (function_exists('seopress_normalized_locale')) {
				$language = seopress_normalized_locale(get_locale());
			} else {
				$language = get_locale();
			}

			//WPML
			if (defined('ICL_SITEPRESS_VERSION')) {
				$language = apply_filters( 'wpml_post_language_details', NULL, $post_id );
				$language = !empty($language['locale']) ? $language['locale'] : get_locale();
			}

			//Polylang
			if (function_exists('pll_get_post_language')) {
				$language = !empty(pll_get_post_language($post_id, 'locale')) ? pll_get_post_language($post_id, 'locale') : get_locale();
			}
		}

		//Convert language code to readable name
		$language = locale_get_display_name($language, 'en') ? esc_html(locale_get_display_name($language, 'en')) : $language;

		$image_src = wp_get_attachment_image_src($post_id, 'full');

		//Prompt for alt text
		$prompt_alt_text = sprintf(esc_html__('Write in less than 10 words an alternative text to improve accessibility and SEO, in this language %s.', 'wp-seopress-pro'), esc_attr($language));

		$prompt_alt_text  = apply_filters( 'seopress_ai_openai_alt_text', $prompt_alt_text, $post_id );

        $prompt_alt_text .= esc_html__('Return the answer in JSON. The key containing the alternative text must be called alt_text.', 'wp-seopress-pro');

		$body = [
			'model'    => $this->getOpenAIModel(),
            'response_format' => [
                'type' => 'json_object'
            ],
			'temperature' => 1,
			'messages' => [
				[
					'role' => 'user',
					'content' => [
						[
							'type' => 'text',
							'text' => $prompt_alt_text
						],
						[
							'type' => 'image_url',
							'image_url' => [
								'url' => esc_html($image_src[0])
							]
						]
					]
				]
			],
			'max_tokens' => 300,
		];


		$args = [
			'body'        => wp_json_encode($body),
			'timeout'     => '30',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers' => [
				'Authorization' => 'Bearer ' . seopress_pro_get_service('Usage')->getLicenseKey(),
				'Content-Type' => 'application/json'
			],
		];

		$args = apply_filters('seopress_ai_openai_request_args_alt', $args);

		$url = self::OPENAI_URL_CHAT_COMPLETIONS;

		$response = wp_remote_post( $url, $args );

		$alt_text = '';

		// make sure the response came back okay
		if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
			if (is_wp_error($response)) {
				$message = $response->get_error_message();
			} else {
				$message = __('An error occurred, please try again. Response code: ', 'wp-seopress-pro') . wp_remote_retrieve_response_code($response);
			}
            //Logs
            set_transient('seopress_pro_ai_logs', wp_remote_retrieve_body($response), 30 * DAY_IN_SECONDS);
		} else {
			$data = json_decode(wp_remote_retrieve_body($response), true);

			$message = 'Success';

			$result = $data['choices'][0]['message']['content'];
            $result = json_decode($result, true);
            $result = isset($result['alt_text']) ? $result['alt_text'] : '';

			$alt_text = esc_attr(trim(stripslashes_deep(wp_filter_nohtml_kses(wp_strip_all_tags(strip_shortcodes($result)))), '"'));
		}

		$data = [
			'message' => $message,
			'alt_text' => html_entity_decode($alt_text),
		];

		if(!empty($alt_text)){
			update_post_meta($post_id, '_wp_attachment_image_alt', apply_filters('seopress_update_alt', sanitize_text_field(html_entity_decode($alt_text)), $post_id));
		}

		return $action === 'alt_text' ? $data['alt_text'] : $data;
	}
}
