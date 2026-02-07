<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

use SEOPressPro\Helpers\Schemas\Currencies;

///////////////////////////////////////////////////////////////////////////////////////////////////
//Restrict Structured Data Types metaboxes to user roles
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_advanced_security_metaboxe_sdt_role_hook_option() {
	return seopress_pro_get_service('AdvancedOptionPro')->getSecurityMetaboxRoleStructuredData();
}

/**
 * Get currencies schema.
 *
 * @return array
 */
function seopress_get_options_schema_currencies() {
	return Currencies::getOptions();
}

function seopress_get_schema_html_part($type, $data, $key_schema = 0) {
	switch ($type) {
		case 'article':
			seopress_get_schema_metaboxe_article($data, $key_schema);
			break;
		case 'local-business':
			seopress_get_schema_metaboxe_local_business($data, $key_schema);
			break;
		case 'faq':
			seopress_get_schema_metaboxe_faq($data, $key_schema);
			break;
		case 'how-to':
			seopress_get_schema_metaboxe_how_to($data, $key_schema);
			break;
		case 'course':
			seopress_get_schema_metaboxe_course($data, $key_schema);
			break;
		case 'recipe':
			seopress_get_schema_metaboxe_recipe($data, $key_schema);
			break;
		case 'jobs':
			seopress_get_schema_metaboxe_jobs($data, $key_schema);
			break;
		case 'video':
			seopress_get_schema_metaboxe_video($data, $key_schema);
			break;
		case 'event':
			seopress_get_schema_metaboxe_event($data, $key_schema);
			break;
		case 'product':
			seopress_get_schema_metaboxe_product($data, $key_schema);
			break;
		case 'software':
			seopress_get_schema_metaboxe_software($data, $key_schema);
			break;
		case 'service':
			seopress_get_schema_metaboxe_service($data, $key_schema);
			break;
		case 'review':
			seopress_get_schema_metaboxe_review($data, $key_schema);
			break;
		case 'custom':
			seopress_get_schema_metaboxe_custom($data, $key_schema);
			break;
	}
}

/**
 * @return array
 *
 */
function seopress_get_keys_rich_snippets() {
	return [
		'_seopress_pro_rich_snippets_type' => [
			'key' => '_seopress_pro_rich_snippets_type',
			'post_key' => 'seopress_pro_rich_snippets_type',
		],
		'_seopress_pro_rich_snippets_article_type' => [
			'key' => '_seopress_pro_rich_snippets_article_type',
			'post_key' => 'seopress_pro_rich_snippets_article_type',
		],
		'_seopress_pro_rich_snippets_article_title' => [
			'key' => '_seopress_pro_rich_snippets_article_title',
			'post_key' => 'seopress_pro_rich_snippets_article_title',
		],
		'_seopress_pro_rich_snippets_article_desc' => [
			'key' => '_seopress_pro_rich_snippets_article_desc',
			'post_key' => 'seopress_pro_rich_snippets_article_desc',
		],
		'_seopress_pro_rich_snippets_article_author' => [
			'key' => '_seopress_pro_rich_snippets_article_author',
			'post_key' => 'seopress_pro_rich_snippets_article_author',
		],
		'_seopress_pro_rich_snippets_article_img' => [
			'key' => '_seopress_pro_rich_snippets_article_img',
			'post_key' => 'seopress_pro_rich_snippets_article_img',
		],
		'_seopress_pro_rich_snippets_article_img_width' => [
			'key' => '_seopress_pro_rich_snippets_article_img_width',
			'post_key' => 'seopress_pro_rich_snippets_article_img_width',
		],
		'_seopress_pro_rich_snippets_article_img_height' => [
			'key' => '_seopress_pro_rich_snippets_article_img_height',
			'post_key' => 'seopress_pro_rich_snippets_article_img_height',
		],
		'_seopress_pro_rich_snippets_article_coverage_start_date' => [
			'key' => '_seopress_pro_rich_snippets_article_coverage_start_date',
			'post_key' => 'seopress_pro_rich_snippets_article_coverage_start_date',
		],
		'_seopress_pro_rich_snippets_article_coverage_start_time' => [
			'key' => '_seopress_pro_rich_snippets_article_coverage_start_time',
			'post_key' => 'seopress_pro_rich_snippets_article_coverage_start_time',
		],
		'_seopress_pro_rich_snippets_article_coverage_end_date' => [
			'key' => '_seopress_pro_rich_snippets_article_coverage_end_date',
			'post_key' => 'seopress_pro_rich_snippets_article_coverage_end_date',
		],
		'_seopress_pro_rich_snippets_article_coverage_end_time' => [
			'key' => '_seopress_pro_rich_snippets_article_coverage_end_time',
			'post_key' => 'seopress_pro_rich_snippets_article_coverage_end_time',
		],
		'_seopress_pro_rich_snippets_article_speakable_css_selector' => [
			'key' => '_seopress_pro_rich_snippets_article_speakable_css_selector',
			'post_key' => 'seopress_pro_rich_snippets_article_speakable_css_selector',
		],
		'_seopress_pro_rich_snippets_lb_name' => [
			'key' => '_seopress_pro_rich_snippets_lb_name',
			'post_key' => 'seopress_pro_rich_snippets_lb_name',
		],
		'_seopress_pro_rich_snippets_lb_type' => [
			'key' => '_seopress_pro_rich_snippets_lb_type',
			'post_key' => 'seopress_pro_rich_snippets_lb_type',
		],
		'_seopress_pro_rich_snippets_lb_cuisine' => [
			'key' => '_seopress_pro_rich_snippets_lb_cuisine',
			'post_key' => 'seopress_pro_rich_snippets_lb_cuisine',
		],
		'_seopress_pro_rich_snippets_lb_menu' => [
			'key' => '_seopress_pro_rich_snippets_lb_menu',
			'post_key' => 'seopress_pro_rich_snippets_lb_menu',
		],
		'_seopress_pro_rich_snippets_lb_accepts_reservations' => [
			'key' => '_seopress_pro_rich_snippets_lb_accepts_reservations',
			'post_key' => 'seopress_pro_rich_snippets_lb_accepts_reservations',
		],
		'_seopress_pro_rich_snippets_lb_img' => [
			'key' => '_seopress_pro_rich_snippets_lb_img',
			'post_key' => 'seopress_pro_rich_snippets_lb_img',
		],
		'_seopress_pro_rich_snippets_lb_img_width' => [
			'key' => '_seopress_pro_rich_snippets_lb_img_width',
			'post_key' => 'seopress_pro_rich_snippets_lb_img_width',
		],
		'_seopress_pro_rich_snippets_lb_img_height' => [
			'key' => '_seopress_pro_rich_snippets_lb_img_height',
			'post_key' => 'seopress_pro_rich_snippets_lb_img_height',
		],
		'_seopress_pro_rich_snippets_lb_street_addr' => [
			'key' => '_seopress_pro_rich_snippets_lb_street_addr',
			'post_key' => 'seopress_pro_rich_snippets_lb_street_addr',
		],
		'_seopress_pro_rich_snippets_lb_city' => [
			'key' => '_seopress_pro_rich_snippets_lb_city',
			'post_key' => 'seopress_pro_rich_snippets_lb_city',
		],
		'_seopress_pro_rich_snippets_lb_state' => [
			'key' => '_seopress_pro_rich_snippets_lb_state',
			'post_key' => 'seopress_pro_rich_snippets_lb_state',
		],
		'_seopress_pro_rich_snippets_lb_pc' => [
			'key' => '_seopress_pro_rich_snippets_lb_pc',
			'post_key' => 'seopress_pro_rich_snippets_lb_pc',
		],
		'_seopress_pro_rich_snippets_lb_country' => [
			'key' => '_seopress_pro_rich_snippets_lb_country',
			'post_key' => 'seopress_pro_rich_snippets_lb_country',
		],
		'_seopress_pro_rich_snippets_lb_lat' => [
			'key' => '_seopress_pro_rich_snippets_lb_lat',
			'post_key' => 'seopress_pro_rich_snippets_lb_lat',
		],
		'_seopress_pro_rich_snippets_lb_lon' => [
			'key' => '_seopress_pro_rich_snippets_lb_lon',
			'post_key' => 'seopress_pro_rich_snippets_lb_lon',
		],
		'_seopress_pro_rich_snippets_lb_website' => [
			'key' => '_seopress_pro_rich_snippets_lb_website',
			'post_key' => 'seopress_pro_rich_snippets_lb_website',
		],
		'_seopress_pro_rich_snippets_lb_tel' => [
			'key' => '_seopress_pro_rich_snippets_lb_tel',
			'post_key' => 'seopress_pro_rich_snippets_lb_tel',
		],
		'_seopress_pro_rich_snippets_lb_price' => [
			'key' => '_seopress_pro_rich_snippets_lb_price',
			'post_key' => 'seopress_pro_rich_snippets_lb_price',
		],
		'_seopress_pro_rich_snippets_lb_opening_hours' => [
			'key' => '_seopress_pro_rich_snippets_lb_opening_hours',
			'post_key' => 'seopress_pro_rich_snippets_lb_opening_hours',
		],
		'_seopress_pro_rich_snippets_faq' => [
			'key' => '_seopress_pro_rich_snippets_faq',
			'post_key' => 'seopress_pro_rich_snippets_faq',
		],
		'_seopress_pro_rich_snippets_how_to_name' => [
			'key' => '_seopress_pro_rich_snippets_how_to_name',
			'post_key' => 'seopress_pro_rich_snippets_how_to_name',
		],
		'_seopress_pro_rich_snippets_how_to_desc' => [
			'key' => '_seopress_pro_rich_snippets_how_to_desc',
			'post_key' => 'seopress_pro_rich_snippets_how_to_desc',
		],
		'_seopress_pro_rich_snippets_how_to_img' => [
			'key' => '_seopress_pro_rich_snippets_how_to_img',
			'post_key' => 'seopress_pro_rich_snippets_how_to_img',
		],
		'_seopress_pro_rich_snippets_how_to_img_width' => [
			'key' => '_seopress_pro_rich_snippets_how_to_img_width',
			'post_key' => 'seopress_pro_rich_snippets_how_to_img_width',
		],
		'_seopress_pro_rich_snippets_how_to_img_height' => [
			'key' => '_seopress_pro_rich_snippets_how_to_img_height',
			'post_key' => 'seopress_pro_rich_snippets_how_to_img_height',
		],
		'_seopress_pro_rich_snippets_how_to_currency' => [
			'key' => '_seopress_pro_rich_snippets_how_to_currency',
			'post_key' => 'seopress_pro_rich_snippets_how_to_currency',
		],
		'_seopress_pro_rich_snippets_how_to_cost' => [
			'key' => '_seopress_pro_rich_snippets_how_to_cost',
			'post_key' => 'seopress_pro_rich_snippets_how_to_cost',
		],
		'_seopress_pro_rich_snippets_how_to_total_time' => [
			'key' => '_seopress_pro_rich_snippets_how_to_total_time',
			'post_key' => 'seopress_pro_rich_snippets_how_to_total_time',
		],
		'_seopress_pro_rich_snippets_how_to' => [
			'key' => '_seopress_pro_rich_snippets_how_to',
			'post_key' => 'seopress_pro_rich_snippets_how_to',
		],
		'_seopress_pro_rich_snippets_courses_title' => [
			'key' => '_seopress_pro_rich_snippets_courses_title',
			'post_key' => 'seopress_pro_rich_snippets_courses_title',
		],
		'_seopress_pro_rich_snippets_courses_desc' => [
			'key' => '_seopress_pro_rich_snippets_courses_desc',
			'post_key' => 'seopress_pro_rich_snippets_courses_desc',
		],
		'_seopress_pro_rich_snippets_courses_school' => [
			'key' => '_seopress_pro_rich_snippets_courses_school',
			'post_key' => 'seopress_pro_rich_snippets_courses_school',
		],
		'_seopress_pro_rich_snippets_courses_website' => [
			'key' => '_seopress_pro_rich_snippets_courses_website',
			'post_key' => 'seopress_pro_rich_snippets_courses_website',
		],
		'_seopress_pro_rich_snippets_courses_offers' => [
			'key' => '_seopress_pro_rich_snippets_courses_offers',
			'post_key' => 'seopress_pro_rich_snippets_courses_offers',
		],
		'_seopress_pro_rich_snippets_courses_instances' => [
			'key' => '_seopress_pro_rich_snippets_courses_instances',
			'post_key' => 'seopress_pro_rich_snippets_courses_instances',
		],
		'_seopress_pro_rich_snippets_recipes_name' => [
			'key' => '_seopress_pro_rich_snippets_recipes_name',
			'post_key' => 'seopress_pro_rich_snippets_recipes_name',
		],
		'_seopress_pro_rich_snippets_recipes_desc' => [
			'key' => '_seopress_pro_rich_snippets_recipes_desc',
			'post_key' => 'seopress_pro_rich_snippets_recipes_desc',
		],
		'_seopress_pro_rich_snippets_recipes_cat' => [
			'key' => '_seopress_pro_rich_snippets_recipes_cat',
			'post_key' => 'seopress_pro_rich_snippets_recipes_cat',
		],
		'_seopress_pro_rich_snippets_recipes_img' => [
			'key' => '_seopress_pro_rich_snippets_recipes_img',
			'post_key' => 'seopress_pro_rich_snippets_recipes_img',
		],
		'_seopress_pro_rich_snippets_recipes_video' => [
			'key' => '_seopress_pro_rich_snippets_recipes_video',
			'post_key' => 'seopress_pro_rich_snippets_recipes_video',
		],
		'_seopress_pro_rich_snippets_recipes_prep_time' => [
			'key' => '_seopress_pro_rich_snippets_recipes_prep_time',
			'post_key' => 'seopress_pro_rich_snippets_recipes_prep_time',
		],
		'_seopress_pro_rich_snippets_recipes_cook_time' => [
			'key' => '_seopress_pro_rich_snippets_recipes_cook_time',
			'post_key' => 'seopress_pro_rich_snippets_recipes_cook_time',
		],
		'_seopress_pro_rich_snippets_recipes_calories' => [
			'key' => '_seopress_pro_rich_snippets_recipes_calories',
			'post_key' => 'seopress_pro_rich_snippets_recipes_calories',
		],
		'_seopress_pro_rich_snippets_recipes_yield' => [
			'key' => '_seopress_pro_rich_snippets_recipes_yield',
			'post_key' => 'seopress_pro_rich_snippets_recipes_yield',
		],
		'_seopress_pro_rich_snippets_recipes_keywords' => [
			'key' => '_seopress_pro_rich_snippets_recipes_keywords',
			'post_key' => 'seopress_pro_rich_snippets_recipes_keywords',
		],
		'_seopress_pro_rich_snippets_recipes_cuisine' => [
			'key' => '_seopress_pro_rich_snippets_recipes_cuisine',
			'post_key' => 'seopress_pro_rich_snippets_recipes_cuisine',
		],
		'_seopress_pro_rich_snippets_recipes_ingredient' => [
			'key' => '_seopress_pro_rich_snippets_recipes_ingredient',
			'post_key' => 'seopress_pro_rich_snippets_recipes_ingredient',
		],
		'_seopress_pro_rich_snippets_recipes_instructions' => [
			'key' => '_seopress_pro_rich_snippets_recipes_instructions',
			'post_key' => 'seopress_pro_rich_snippets_recipes_instructions',
		],
		'_seopress_pro_rich_snippets_jobs_name' => [
			'key' => '_seopress_pro_rich_snippets_jobs_name',
			'post_key' => 'seopress_pro_rich_snippets_jobs_name',
		],
		'_seopress_pro_rich_snippets_jobs_desc' => [
			'key' => '_seopress_pro_rich_snippets_jobs_desc',
			'post_key' => 'seopress_pro_rich_snippets_jobs_desc',
		],
		'_seopress_pro_rich_snippets_jobs_date_posted' => [
			'key' => '_seopress_pro_rich_snippets_jobs_date_posted',
			'post_key' => 'seopress_pro_rich_snippets_jobs_date_posted',
		],
		'_seopress_pro_rich_snippets_jobs_valid_through' => [
			'key' => '_seopress_pro_rich_snippets_jobs_valid_through',
			'post_key' => 'seopress_pro_rich_snippets_jobs_valid_through',
		],
		'_seopress_pro_rich_snippets_jobs_employment_type' => [
			'key' => '_seopress_pro_rich_snippets_jobs_employment_type',
			'post_key' => 'seopress_pro_rich_snippets_jobs_employment_type',
		],
		'_seopress_pro_rich_snippets_jobs_identifier_name' => [
			'key' => '_seopress_pro_rich_snippets_jobs_identifier_name',
			'post_key' => 'seopress_pro_rich_snippets_jobs_identifier_name',
		],
		'_seopress_pro_rich_snippets_jobs_identifier_value' => [
			'key' => '_seopress_pro_rich_snippets_jobs_identifier_value',
			'post_key' => 'seopress_pro_rich_snippets_jobs_identifier_value',
		],
		'_seopress_pro_rich_snippets_jobs_hiring_organization' => [
			'key' => '_seopress_pro_rich_snippets_jobs_hiring_organization',
			'post_key' => 'seopress_pro_rich_snippets_jobs_hiring_organization',
		],
		'_seopress_pro_rich_snippets_jobs_hiring_same_as' => [
			'key' => '_seopress_pro_rich_snippets_jobs_hiring_same_as',
			'post_key' => 'seopress_pro_rich_snippets_jobs_hiring_same_as',
		],
		'_seopress_pro_rich_snippets_jobs_hiring_logo' => [
			'key' => '_seopress_pro_rich_snippets_jobs_hiring_logo',
			'post_key' => 'seopress_pro_rich_snippets_jobs_hiring_logo',
		],
		'_seopress_pro_rich_snippets_jobs_hiring_logo_width' => [
			'key' => '_seopress_pro_rich_snippets_jobs_hiring_logo_width',
			'post_key' => 'seopress_pro_rich_snippets_jobs_hiring_logo_width',
		],
		'_seopress_pro_rich_snippets_jobs_hiring_logo_height' => [
			'key' => '_seopress_pro_rich_snippets_jobs_hiring_logo_height',
			'post_key' => 'seopress_pro_rich_snippets_jobs_hiring_logo_height',
		],
		'_seopress_pro_rich_snippets_jobs_address_street' => [
			'key' => '_seopress_pro_rich_snippets_jobs_address_street',
			'post_key' => 'seopress_pro_rich_snippets_jobs_address_street',
		],
		'_seopress_pro_rich_snippets_jobs_address_locality' => [
			'key' => '_seopress_pro_rich_snippets_jobs_address_locality',
			'post_key' => 'seopress_pro_rich_snippets_jobs_address_locality',
		],
		'_seopress_pro_rich_snippets_jobs_address_region' => [
			'key' => '_seopress_pro_rich_snippets_jobs_address_region',
			'post_key' => 'seopress_pro_rich_snippets_jobs_address_region',
		],
		'_seopress_pro_rich_snippets_jobs_postal_code' => [
			'key' => '_seopress_pro_rich_snippets_jobs_postal_code',
			'post_key' => 'seopress_pro_rich_snippets_jobs_postal_code',
		],
		'_seopress_pro_rich_snippets_jobs_country' => [
			'key' => '_seopress_pro_rich_snippets_jobs_country',
			'post_key' => 'seopress_pro_rich_snippets_jobs_country',
		],
		'_seopress_pro_rich_snippets_jobs_remote' => [
			'key' => '_seopress_pro_rich_snippets_jobs_remote',
			'post_key' => 'seopress_pro_rich_snippets_jobs_remote',
		],
		'_seopress_pro_rich_snippets_jobs_direct_apply' => [
			'key' => '_seopress_pro_rich_snippets_jobs_direct_apply',
			'post_key' => 'seopress_pro_rich_snippets_jobs_direct_apply',
		],
		'_seopress_pro_rich_snippets_jobs_salary' => [
			'key' => '_seopress_pro_rich_snippets_jobs_salary',
			'post_key' => 'seopress_pro_rich_snippets_jobs_salary',
		],
		'_seopress_pro_rich_snippets_jobs_salary_currency' => [
			'key' => '_seopress_pro_rich_snippets_jobs_salary_currency',
			'post_key' => 'seopress_pro_rich_snippets_jobs_salary_currency',
		],
		'_seopress_pro_rich_snippets_jobs_salary_unit' => [
			'key' => '_seopress_pro_rich_snippets_jobs_salary_unit',
			'post_key' => 'seopress_pro_rich_snippets_jobs_salary_unit',
		],
		'_seopress_pro_rich_snippets_jobs_location_requirement' => [
			'key' => '_seopress_pro_rich_snippets_jobs_location_requirement',
			'post_key' => 'seopress_pro_rich_snippets_jobs_location_requirement',
		],
		'_seopress_pro_rich_snippets_videos_name' => [
			'key' => '_seopress_pro_rich_snippets_videos_name',
			'post_key' => 'seopress_pro_rich_snippets_videos_name',
		],
		'_seopress_pro_rich_snippets_videos_description' => [
			'key' => '_seopress_pro_rich_snippets_videos_description',
			'post_key' => 'seopress_pro_rich_snippets_videos_description',
		],
		'_seopress_pro_rich_snippets_videos_date_posted' => [
			'key' => '_seopress_pro_rich_snippets_videos_date_posted',
			'post_key' => 'seopress_pro_rich_snippets_videos_date_posted',
		],
		'_seopress_pro_rich_snippets_videos_img' => [
			'key' => '_seopress_pro_rich_snippets_videos_img',
			'post_key' => 'seopress_pro_rich_snippets_videos_img',
		],
		'_seopress_pro_rich_snippets_videos_img_width' => [
			'key' => '_seopress_pro_rich_snippets_videos_img_width',
			'post_key' => 'seopress_pro_rich_snippets_videos_img_width',
		],
		'_seopress_pro_rich_snippets_videos_img_height' => [
			'key' => '_seopress_pro_rich_snippets_videos_img_height',
			'post_key' => 'seopress_pro_rich_snippets_videos_img_height',
		],
		'_seopress_pro_rich_snippets_videos_duration' => [
			'key' => '_seopress_pro_rich_snippets_videos_duration',
			'post_key' => 'seopress_pro_rich_snippets_videos_duration',
		],
		'_seopress_pro_rich_snippets_videos_url' => [
			'key' => '_seopress_pro_rich_snippets_videos_url',
			'post_key' => 'seopress_pro_rich_snippets_videos_url',
		],
		'_seopress_pro_rich_snippets_events_type' => [
			'key' => '_seopress_pro_rich_snippets_events_type',
			'post_key' => 'seopress_pro_rich_snippets_events_type',
		],
		'_seopress_pro_rich_snippets_events_name' => [
			'key' => '_seopress_pro_rich_snippets_events_name',
			'post_key' => 'seopress_pro_rich_snippets_events_name',
		],
		'_seopress_pro_rich_snippets_events_desc' => [
			'key' => '_seopress_pro_rich_snippets_events_desc',
			'post_key' => 'seopress_pro_rich_snippets_events_desc',
		],
		'_seopress_pro_rich_snippets_events_img' => [
			'key' => '_seopress_pro_rich_snippets_events_img',
			'post_key' => 'seopress_pro_rich_snippets_events_img',
		],
		'_seopress_pro_rich_snippets_events_start_date' => [
			'key' => '_seopress_pro_rich_snippets_events_start_date',
			'post_key' => 'seopress_pro_rich_snippets_events_start_date',
		],
		'_seopress_pro_rich_snippets_events_start_date_timezone' => [
			'key' => '_seopress_pro_rich_snippets_events_start_date_timezone',
			'post_key' => 'seopress_pro_rich_snippets_events_start_date_timezone',
		],
		'_seopress_pro_rich_snippets_events_start_time' => [
			'key' => '_seopress_pro_rich_snippets_events_start_time',
			'post_key' => 'seopress_pro_rich_snippets_events_start_time',
		],
		'_seopress_pro_rich_snippets_events_end_date' => [
			'key' => '_seopress_pro_rich_snippets_events_end_date',
			'post_key' => 'seopress_pro_rich_snippets_events_end_date',
		],
		'_seopress_pro_rich_snippets_events_end_time' => [
			'key' => '_seopress_pro_rich_snippets_events_end_time',
			'post_key' => 'seopress_pro_rich_snippets_events_end_time',
		],
		'_seopress_pro_rich_snippets_events_previous_start_date' => [
			'key' => '_seopress_pro_rich_snippets_events_previous_start_date',
			'post_key' => 'seopress_pro_rich_snippets_events_previous_start_date',
		],
		'_seopress_pro_rich_snippets_events_previous_start_time' => [
			'key' => '_seopress_pro_rich_snippets_events_previous_start_time',
			'post_key' => 'seopress_pro_rich_snippets_events_previous_start_time',
		],
		'_seopress_pro_rich_snippets_events_location_name' => [
			'key' => '_seopress_pro_rich_snippets_events_location_name',
			'post_key' => 'seopress_pro_rich_snippets_events_location_name',
		],
		'_seopress_pro_rich_snippets_events_location_url' => [
			'key' => '_seopress_pro_rich_snippets_events_location_url',
			'post_key' => 'seopress_pro_rich_snippets_events_location_url',
		],
		'_seopress_pro_rich_snippets_events_location_address' => [
			'key' => '_seopress_pro_rich_snippets_events_location_address',
			'post_key' => 'seopress_pro_rich_snippets_events_location_address',
		],
		'_seopress_pro_rich_snippets_events_offers_name' => [
			'key' => '_seopress_pro_rich_snippets_events_offers_name',
			'post_key' => 'seopress_pro_rich_snippets_events_offers_name',
		],
		'_seopress_pro_rich_snippets_events_offers_cat' => [
			'key' => '_seopress_pro_rich_snippets_events_offers_cat',
			'post_key' => 'seopress_pro_rich_snippets_events_offers_cat',
		],
		'_seopress_pro_rich_snippets_events_offers_price' => [
			'key' => '_seopress_pro_rich_snippets_events_offers_price',
			'post_key' => 'seopress_pro_rich_snippets_events_offers_price',
		],
		'_seopress_pro_rich_snippets_events_offers_price_currency' => [
			'key' => '_seopress_pro_rich_snippets_events_offers_price_currency',
			'post_key' => 'seopress_pro_rich_snippets_events_offers_price_currency',
		],
		'_seopress_pro_rich_snippets_events_offers_availability' => [
			'key' => '_seopress_pro_rich_snippets_events_offers_availability',
			'post_key' => 'seopress_pro_rich_snippets_events_offers_availability',
		],
		'_seopress_pro_rich_snippets_events_offers_valid_from_date' => [
			'key' => '_seopress_pro_rich_snippets_events_offers_valid_from_date',
			'post_key' => 'seopress_pro_rich_snippets_events_offers_valid_from_date',
		],
		'_seopress_pro_rich_snippets_events_offers_valid_from_time' => [
			'key' => '_seopress_pro_rich_snippets_events_offers_valid_from_time',
			'post_key' => 'seopress_pro_rich_snippets_events_offers_valid_from_time',
		],
		'_seopress_pro_rich_snippets_events_offers_url' => [
			'key' => '_seopress_pro_rich_snippets_events_offers_url',
			'post_key' => 'seopress_pro_rich_snippets_events_offers_url',
		],
		'_seopress_pro_rich_snippets_events_performer' => [
			'key' => '_seopress_pro_rich_snippets_events_performer',
			'post_key' => 'seopress_pro_rich_snippets_events_performer',
		],
		'_seopress_pro_rich_snippets_events_organizer_name' => [
			'key' => '_seopress_pro_rich_snippets_events_organizer_name',
			'post_key' => 'seopress_pro_rich_snippets_events_organizer_name',
		],
		'_seopress_pro_rich_snippets_events_organizer_url' => [
			'key' => '_seopress_pro_rich_snippets_events_organizer_url',
			'post_key' => 'seopress_pro_rich_snippets_events_organizer_url',
		],
		'_seopress_pro_rich_snippets_events_status' => [
			'key' => '_seopress_pro_rich_snippets_events_status',
			'post_key' => 'seopress_pro_rich_snippets_events_status',
		],
		'_seopress_pro_rich_snippets_events_attendance_mode' => [
			'key' => '_seopress_pro_rich_snippets_events_attendance_mode',
			'post_key' => 'seopress_pro_rich_snippets_events_attendance_mode',
		],
		'_seopress_pro_rich_snippets_product_name' => [
			'key' => '_seopress_pro_rich_snippets_product_name',
			'post_key' => 'seopress_pro_rich_snippets_product_name',
		],
		'_seopress_pro_rich_snippets_product_description' => [
			'key' => '_seopress_pro_rich_snippets_product_description',
			'post_key' => 'seopress_pro_rich_snippets_product_description',
		],
		'_seopress_pro_rich_snippets_product_img' => [
			'key' => '_seopress_pro_rich_snippets_product_img',
			'post_key' => 'seopress_pro_rich_snippets_product_img',
		],
		'_seopress_pro_rich_snippets_product_price' => [
			'key' => '_seopress_pro_rich_snippets_product_price',
			'post_key' => 'seopress_pro_rich_snippets_product_price',
		],
		'_seopress_pro_rich_snippets_product_price_valid_date' => [
			'key' => '_seopress_pro_rich_snippets_product_price_valid_date',
			'post_key' => 'seopress_pro_rich_snippets_product_price_valid_date',
		],
		'_seopress_pro_rich_snippets_product_sku' => [
			'key' => '_seopress_pro_rich_snippets_product_sku',
			'post_key' => 'seopress_pro_rich_snippets_product_sku',
		],
		'_seopress_pro_rich_snippets_product_brand' => [
			'key' => '_seopress_pro_rich_snippets_product_brand',
			'post_key' => 'seopress_pro_rich_snippets_product_brand',
		],
		'_seopress_pro_rich_snippets_product_global_ids' => [
			'key' => '_seopress_pro_rich_snippets_product_global_ids',
			'post_key' => 'seopress_pro_rich_snippets_product_global_ids',
		],
		'_seopress_pro_rich_snippets_product_global_ids_value' => [
			'key' => '_seopress_pro_rich_snippets_product_global_ids_value',
			'post_key' => 'seopress_pro_rich_snippets_product_global_ids_value',
		],
		'_seopress_pro_rich_snippets_product_price_currency' => [
			'key' => '_seopress_pro_rich_snippets_product_price_currency',
			'post_key' => 'seopress_pro_rich_snippets_product_price_currency',
		],
		'_seopress_pro_rich_snippets_product_condition' => [
			'key' => '_seopress_pro_rich_snippets_product_condition',
			'post_key' => 'seopress_pro_rich_snippets_product_condition',
		],
		'_seopress_pro_rich_snippets_product_availability' => [
			'key' => '_seopress_pro_rich_snippets_product_availability',
			'post_key' => 'seopress_pro_rich_snippets_product_availability',
		],
		'_seopress_pro_rich_snippets_product_positive_notes' => [
			'key' => '_seopress_pro_rich_snippets_product_positive_notes',
			'post_key' => 'seopress_pro_rich_snippets_product_positive_notes',
		],
		'_seopress_pro_rich_snippets_product_negative_notes' => [
			'key' => '_seopress_pro_rich_snippets_product_negative_notes',
			'post_key' => 'seopress_pro_rich_snippets_product_negative_notes',
		],
		'_seopress_pro_rich_snippets_product_energy_consumption' => [
			'key' => '_seopress_pro_rich_snippets_product_energy_consumption',
			'post_key' => 'seopress_pro_rich_snippets_product_energy_consumption',
		],
		'_seopress_pro_rich_snippets_softwareapp_name' => [
			'key' => '_seopress_pro_rich_snippets_softwareapp_name',
			'post_key' => 'seopress_pro_rich_snippets_softwareapp_name',
		],
		'_seopress_pro_rich_snippets_softwareapp_os' => [
			'key' => '_seopress_pro_rich_snippets_softwareapp_os',
			'post_key' => 'seopress_pro_rich_snippets_softwareapp_os',
		],
		'_seopress_pro_rich_snippets_softwareapp_cat' => [
			'key' => '_seopress_pro_rich_snippets_softwareapp_cat',
			'post_key' => 'seopress_pro_rich_snippets_softwareapp_cat',
		],
		'_seopress_pro_rich_snippets_softwareapp_price' => [
			'key' => '_seopress_pro_rich_snippets_softwareapp_price',
			'post_key' => 'seopress_pro_rich_snippets_softwareapp_price',
		],
		'_seopress_pro_rich_snippets_softwareapp_currency' => [
			'key' => '_seopress_pro_rich_snippets_softwareapp_currency',
			'post_key' => 'seopress_pro_rich_snippets_softwareapp_currency',
		],
		'_seopress_pro_rich_snippets_softwareapp_rating' => [
			'key' => '_seopress_pro_rich_snippets_softwareapp_rating',
			'post_key' => 'seopress_pro_rich_snippets_softwareapp_rating',
		],
		'_seopress_pro_rich_snippets_softwareapp_max_rating' => [
			'key' => '_seopress_pro_rich_snippets_softwareapp_max_rating',
			'post_key' => 'seopress_pro_rich_snippets_softwareapp_max_rating',
		],
		'_seopress_pro_rich_snippets_service_name' => [
			'key' => '_seopress_pro_rich_snippets_service_name',
			'post_key' => 'seopress_pro_rich_snippets_service_name',
		],
		'_seopress_pro_rich_snippets_service_type' => [
			'key' => '_seopress_pro_rich_snippets_service_type',
			'post_key' => 'seopress_pro_rich_snippets_service_type',
		],
		'_seopress_pro_rich_snippets_service_description' => [
			'key' => '_seopress_pro_rich_snippets_service_description',
			'post_key' => 'seopress_pro_rich_snippets_service_description',
		],
		'_seopress_pro_rich_snippets_service_img' => [
			'key' => '_seopress_pro_rich_snippets_service_img',
			'post_key' => 'seopress_pro_rich_snippets_service_img',
		],
		'_seopress_pro_rich_snippets_service_area' => [
			'key' => '_seopress_pro_rich_snippets_service_area',
			'post_key' => 'seopress_pro_rich_snippets_service_area',
		],
		'_seopress_pro_rich_snippets_service_provider_name' => [
			'key' => '_seopress_pro_rich_snippets_service_provider_name',
			'post_key' => 'seopress_pro_rich_snippets_service_provider_name',
		],
		'_seopress_pro_rich_snippets_service_lb_img' => [
			'key' => '_seopress_pro_rich_snippets_service_lb_img',
			'post_key' => 'seopress_pro_rich_snippets_service_lb_img',
		],
		'_seopress_pro_rich_snippets_service_provider_mobility' => [
			'key' => '_seopress_pro_rich_snippets_service_provider_mobility',
			'post_key' => 'seopress_pro_rich_snippets_service_provider_mobility',
		],
		'_seopress_pro_rich_snippets_service_slogan' => [
			'key' => '_seopress_pro_rich_snippets_service_slogan',
			'post_key' => 'seopress_pro_rich_snippets_service_slogan',
		],
		'_seopress_pro_rich_snippets_service_street_addr' => [
			'key' => '_seopress_pro_rich_snippets_service_street_addr',
			'post_key' => 'seopress_pro_rich_snippets_service_street_addr',
		],
		'_seopress_pro_rich_snippets_service_city' => [
			'key' => '_seopress_pro_rich_snippets_service_city',
			'post_key' => 'seopress_pro_rich_snippets_service_city',
		],
		'_seopress_pro_rich_snippets_service_state' => [
			'key' => '_seopress_pro_rich_snippets_service_state',
			'post_key' => 'seopress_pro_rich_snippets_service_state',
		],
		'_seopress_pro_rich_snippets_service_pc' => [
			'key' => '_seopress_pro_rich_snippets_service_pc',
			'post_key' => 'seopress_pro_rich_snippets_service_pc',
		],
		'_seopress_pro_rich_snippets_service_country' => [
			'key' => '_seopress_pro_rich_snippets_service_country',
			'post_key' => 'seopress_pro_rich_snippets_service_country',
		],
		'_seopress_pro_rich_snippets_service_lat' => [
			'key' => '_seopress_pro_rich_snippets_service_lat',
			'post_key' => 'seopress_pro_rich_snippets_service_lat',
		],
		'_seopress_pro_rich_snippets_service_lon' => [
			'key' => '_seopress_pro_rich_snippets_service_lon',
			'post_key' => 'seopress_pro_rich_snippets_service_lon',
		],
		'_seopress_pro_rich_snippets_service_tel' => [
			'key' => '_seopress_pro_rich_snippets_service_tel',
			'post_key' => 'seopress_pro_rich_snippets_service_tel',
		],
		'_seopress_pro_rich_snippets_service_price' => [
			'key' => '_seopress_pro_rich_snippets_service_price',
			'post_key' => 'seopress_pro_rich_snippets_service_price',
		],
		'_seopress_pro_rich_snippets_review_item' => [
			'key' => '_seopress_pro_rich_snippets_review_item',
			'post_key' => 'seopress_pro_rich_snippets_review_item',
		],
		'_seopress_pro_rich_snippets_review_item_type' => [
			'key' => '_seopress_pro_rich_snippets_review_item_type',
			'post_key' => 'seopress_pro_rich_snippets_review_item_type',
		],
		'_seopress_pro_rich_snippets_review_img' => [
			'key' => '_seopress_pro_rich_snippets_review_img',
			'post_key' => 'seopress_pro_rich_snippets_review_img',
		],
		'_seopress_pro_rich_snippets_review_rating' => [
			'key' => '_seopress_pro_rich_snippets_review_rating',
			'post_key' => 'seopress_pro_rich_snippets_review_rating',
		],
		'_seopress_pro_rich_snippets_review_max_rating' => [
			'key' => '_seopress_pro_rich_snippets_review_max_rating',
			'post_key' => 'seopress_pro_rich_snippets_review_max_rating',
		],
		'_seopress_pro_rich_snippets_review_body' => [
			'key' => '_seopress_pro_rich_snippets_review_body',
			'post_key' => 'seopress_pro_rich_snippets_review_body',
		],
		'_seopress_pro_rich_snippets_custom' => [
			'key' => '_seopress_pro_rich_snippets_custom',
			'post_key' => 'seopress_pro_rich_snippets_custom',
		],
	];
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Display Rich Snippets metabox in Custom Post Type
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_pro_admin_std_metaboxe_display() {
	add_action('add_meta_boxes', 'seopress_pro_init_metabox', 20);
	function seopress_pro_init_metabox() {
		if (seopress_get_service('AdvancedOption')->getAppearanceMetaboxePosition() !== null) {
			$metaboxe_position = seopress_get_service('AdvancedOption')->getAppearanceMetaboxePosition();
		} else {
			$metaboxe_position = 'default';
		}

		$seopress_get_post_types = seopress_get_service('WordPressData')->getPostTypes();
		$seopress_get_post_types = apply_filters('seopress_pro_metaboxe_sdt', $seopress_get_post_types);

		if ( ! empty($seopress_get_post_types)) {
			foreach ($seopress_get_post_types as $key => $value) {
				add_meta_box('seopress_pro_cpt', esc_html__('Structured Data Types', 'wp-seopress-pro'), 'seopress_pro_cpt', $key, 'normal', $metaboxe_position);
			}
		}
	}

	function seopress_pro_cpt($post) {
		$options_schemas_available = [
			[
				'file' => dirname(__DIR__) . '/schemas/manual/Article.php',
				'value' => 'articles',
				'label' => esc_html__('Article (WebPage)', 'wp-seopress-pro'),
				'key_html_part' => 'article',
			],
			[
				'file' => dirname(__DIR__) . '/schemas/manual/LocalBusiness.php',
				'value' => 'localbusiness',
				'label' => esc_html__('Local Business', 'wp-seopress-pro'),
				'key_html_part' => 'local-business',
			],
			[
				'file' => dirname(__DIR__) . '/schemas/manual/Faq.php',
				'value' => 'faq',
				'label' => esc_html__('FAQ', 'wp-seopress-pro'),
				'key_html_part' => 'faq',
			],
			[
				'file' => dirname(__DIR__) . '/schemas/manual/HowTo.php',
				'value' => 'howto',
				'label' => esc_html__('How-to', 'wp-seopress-pro'),
				'key_html_part' => 'how-to',
			],
			[
				'file' => dirname(__DIR__) . '/schemas/manual/Course.php',
				'value' => 'courses',
				'label' => esc_html__('Course', 'wp-seopress-pro'),
				'key_html_part' => 'course',
			],
			[
				'file' => dirname(__DIR__) . '/schemas/manual/Recipe.php',
				'value' => 'recipes',
				'label' => esc_html__('Recipe', 'wp-seopress-pro'),
				'key_html_part' => 'recipe',
			],
			[
				'file' => dirname(__DIR__) . '/schemas/manual/Job.php',
				'value' => 'jobs',
				'label' => esc_html__('Job', 'wp-seopress-pro'),
				'key_html_part' => 'jobs',
			],
			[
				'file' => dirname(__DIR__) . '/schemas/manual/Video.php',
				'value' => 'videos',
				'label' => esc_html__('Video', 'wp-seopress-pro'),
				'key_html_part' => 'video',
			],
			[
				'file' => dirname(__DIR__) . '/schemas/manual/Event.php',
				'value' => 'events',
				'label' => esc_html__('Event', 'wp-seopress-pro'),
				'key_html_part' => 'event',
			],
			[
				'file' => dirname(__DIR__) . '/schemas/manual/Product.php',
				'value' => 'products',
				'label' => esc_html__('Product', 'wp-seopress-pro'),
				'key_html_part' => 'product',
			],
			[
				'file' => dirname(__DIR__) . '/schemas/manual/SoftwareApp.php',
				'value' => 'softwareapp',
				'label' => esc_html__('Software Application', 'wp-seopress-pro'),
				'key_html_part' => 'software',
			],
			[
				'file' => dirname(__DIR__) . '/schemas/manual/Service.php',
				'value' => 'services',
				'label' => esc_html__('Service', 'wp-seopress-pro'),
				'key_html_part' => 'service',
			],
			[
				'file' => dirname(__DIR__) . '/schemas/manual/Review.php',
				'value' => 'review',
				'label' => esc_html__('Review', 'wp-seopress-pro'),
				'key_html_part' => 'review',
			],
			[
				'file' => dirname(__DIR__) . '/schemas/manual/Custom.php',
				'value' => 'custom',
				'label' => esc_html__('Custom', 'wp-seopress-pro'),
				'key_html_part' => 'custom',
			],
		];

		foreach ($options_schemas_available as $item) {
			include_once $item['file'];
		}

		$prefix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		wp_nonce_field(plugin_basename(__FILE__), 'seopress_pro_cpt_nonce');
		wp_enqueue_script('jquery-ui-accordion');
		wp_enqueue_script('seopress-pro-media-uploader', SEOPRESS_PRO_ASSETS_DIR . '/js/seopress-pro-media-uploader' . $prefix . '.js', ['jquery'], SEOPRESS_PRO_VERSION, false);
		wp_enqueue_script('seopress-pro-rich-snippets', SEOPRESS_PRO_ASSETS_DIR . '/js/seopress-pro-rich-snippets' . $prefix . '.js', ['jquery', 'jquery-ui-tabs'], SEOPRESS_PRO_VERSION, false);
		wp_enqueue_media();
		wp_enqueue_script('jquery-ui-datepicker');

		$seopress_pro_rich_snippets_data = get_post_meta($post->ID, '_seopress_pro_schemas_manual', true);

		$tab1 = '<li><a href="#seopress-schemas-tabs-2">' . esc_html__('Automatic', 'wp-seopress-pro') . '</a></li>';
		$tab2 = '';

		if ( ! seopress_get_service('EnqueueModuleMetabox')->canEnqueue()) {
			$tab2 = '<li><a href="#seopress-schemas-tabs-1">' . esc_html__('Manual', 'wp-seopress-pro') . '</a></li>';
		}
		$tabs = $tab1 . $tab2;
		if (function_exists('seopress_advanced_appearance_schema_default_tab_option') && seopress_advanced_appearance_schema_default_tab_option()) {
			if ('manual' == seopress_advanced_appearance_schema_default_tab_option()) {
				$tabs = $tab2 . $tab1;
			}
		}
		$docs = function_exists('seopress_get_docs_links') ? seopress_get_docs_links() : '';

		//Classic Editor compatibility
		if (function_exists('get_current_screen') && method_exists(get_current_screen(), 'is_block_editor') && true === get_current_screen()->is_block_editor()) {
			$btn_classes_tertiary = 'components-button is-tertiary';
		} else {
			$btn_classes_tertiary = 'submitdelete deletion';
		} ?>
<div id="seopress-schemas-tabs">
	<ul class="wrap-schemas-list">
		<?php if ( ! seopress_get_service('EnqueueModuleMetabox')->canEnqueue()) { ?>
		<li><a href="#seopress-schemas-tabs-1"><?php esc_html_e('Manual', 'wp-seopress-pro'); ?></a>
		</li>
		<?php } ?>
		<li><a id="sp-automatic-tab" href="#seopress-schemas-tabs-2"><?php esc_html_e('Automatic', 'wp-seopress-pro'); ?><span></span></a>
		</li>
	</ul>
	<input type="hidden" name="can_enqueue_seopress_metabox"
		value="<?php echo seopress_get_service('EnqueueModuleMetabox')->canEnqueue() ? '1' : '0'; ?>">

	<template id="js-select-template-schema">
		<div class="box-schema-item" data-key="[X]">
			<div class="wrap-rich-snippets-type">
				<div>
                    <button type="button" class="js-handle-snippet-type" aria-expanded="true">
                        <span class="toggle-indicator" aria-hidden="true"></span>
                    </button>
					<select id="seopress_pro_rich_snippets_type" class="js-select_seopress_pro_rich_snippets_type"
						name="seopress_pro_rich_snippets_data[X][seopress_pro_rich_snippets_type]">
						<option value="none"><?php esc_html_e('None', 'wp-seopress-pro'); ?>
						</option>
						<?php foreach ($options_schemas_available as $item) { ?>
						<option
							value="<?php echo $item['value']; ?>">
							<?php echo $item['label']; ?>
						</option>
						<?php } ?>
					</select>
				</div>
				<a href="#"
					class="js-delete-schema-manual <?php echo $btn_classes_tertiary; ?> is-destructive"
					data-key="[X]">
					<?php esc_html_e('Delete schema', 'wp-seopress-pro'); ?>
				</a>
			</div>
		</div>
	</template>
	<?php foreach ($options_schemas_available as $item) { ?>
	<template
		id="schema-template-<?php echo $item['value']; ?>">
		<?php seopress_get_schema_html_part($item['key_html_part'], [], 'X'); ?>
	</template>
	<?php } ?>
	<template id="schema-template-empty">
		<div class="box-schema-item" data-key="[X]">
			<div class="wrap-rich-snippets-type">
				<div>
                    <button type="button" class="js-handle-snippet-type" aria-expanded="true">
                        <span class="toggle-indicator" aria-hidden="true"></span>
                    </button>
					<select id="seopress_pro_rich_snippets_type" class="js-select_seopress_pro_rich_snippets_type"
						name="seopress_pro_rich_snippets_data[X][seopress_pro_rich_snippets_type]">
						<option value="none"><?php esc_html_e('None', 'wp-seopress-pro'); ?>
						</option>
						<?php foreach ($options_schemas_available as $item) { ?>
						<option
							value="<?php echo $item['value']; ?>">
							<?php echo $item['label']; ?>
						</option>
						<?php } ?>
					</select>
				</div>
				<a href="#"
					class="js-delete-schema-manual <?php echo $btn_classes_tertiary; ?> is-destructive"
					data-key="[X]">
					<?php esc_html_e('Delete schema', 'wp-seopress-pro'); ?>
				</a>
			</div>
		</div>
	</template>
	<?php if ( ! seopress_get_service('EnqueueModuleMetabox')->canEnqueue()) {?>
	<div id="seopress-schemas-tabs-1">
		<div class="box-left">

			<p class="description-alt desc-fb">
				<svg width="24" height="24" viewBox="0 0 24 24" role="img" aria-hidden="true" focusable="false">
					<path
						d="M12 15.8c-3.7 0-6.8-3-6.8-6.8s3-6.8 6.8-6.8c3.7 0 6.8 3 6.8 6.8s-3.1 6.8-6.8 6.8zm0-12C9.1 3.8 6.8 6.1 6.8 9s2.4 5.2 5.2 5.2c2.9 0 5.2-2.4 5.2-5.2S14.9 3.8 12 3.8zM8 17.5h8V19H8zM10 20.5h4V22h-4z">
					</path>
				</svg>
				<?php esc_html_e('It is recommended to enter as many properties as possible to maximize the chances of getting a rich snippet in Google search results.', 'wp-seopress-pro'); ?>
			</p>

			<div class="schemas-bar-new">
				<p>
					<a href="#" id="js-add-schema-manual"
						class="<?php echo seopress_btn_secondary_classes(); ?>">
						<?php esc_html_e('Add a schema', 'wp-seopress-pro'); ?>
					</a>
				</p>
				<p>
					<a href="#" class="js-expand-all components-button is-link"><?php esc_html_e('Expand', 'wp-seopress-pro'); ?></a>&nbsp;/&nbsp;<a
						href="#" class="js-close-all components-button is-link"><?php esc_html_e('Close', 'wp-seopress-pro'); ?></a>
				</p>
			</div>

			<div id="js-box-list-schemas">
				<?php
					if (!empty($seopress_pro_rich_snippets_data)) {
						foreach ($seopress_pro_rich_snippets_data as $key => $data) {
							if (is_array($data)) {
							$seopress_pro_rich_snippets_type = $data['_seopress_pro_rich_snippets_type'];
							} else {
								break;
							}
						?>
						<div class="box-schema-item"
							data-key="<?php echo $key; ?>">
							<div class="wrap-rich-snippets-type">
								<div>
                                    <button type="button" class="js-handle-snippet-type" aria-expanded="true">
                                        <span class="toggle-indicator" aria-hidden="true"></span>
                                    </button>
									<select id="seopress_pro_rich_snippets_type"
										class="js-select_seopress_pro_rich_snippets_type"
										name="seopress_pro_rich_snippets_data[<?php echo $key; ?>][seopress_pro_rich_snippets_type]">
										<option <?php echo selected('none', $seopress_pro_rich_snippets_type); ?>
											value="none"><?php esc_html_e('None', 'wp-seopress-pro'); ?>
										</option>
										<?php foreach ($options_schemas_available as $item) { ?>
										<option <?php echo selected($item['value'], $seopress_pro_rich_snippets_type); ?>
											value="<?php echo $item['value']; ?>"><?php echo $item['label']; ?>
										</option>
										<?php } ?>
									</select>
								</div>

								<a href="#"
									class="js-delete-schema-manual <?php echo $btn_classes_tertiary; ?> is-destructive"
									data-key="<?php echo $key; ?>">
									<?php esc_html_e('Delete schema', 'wp-seopress-pro'); ?>
								</a>
							</div>
							<?php
								foreach ($options_schemas_available as $item) {
									if ($item['value'] === $seopress_pro_rich_snippets_type) {
										seopress_get_schema_html_part($item['key_html_part'], $data, $key);
									}
								}
							?>
						</div>
						<?php
						}
					}
				?>
			</div>
			<p>
				<a href="https://search.google.com/test/rich-results?url=<?php echo get_permalink(); ?>"
					target="_blank"
					class="<?php echo seopress_btn_secondary_classes(); ?>">
					<?php esc_html_e('Validate my schema', 'wp-seopress-pro'); ?>
				</a>
			</p>
		</div>
	</div>

	<?php } ?>
	<div id="seopress-schemas-tabs-2">
		<?php include_once dirname(__FILE__) . '/admin-metaboxes-schemas.php'; ?>
	</div>
</div>
<?php
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////
	//Save datas
	///////////////////////////////////////////////////////////////////////////////////////////////////
	add_action('save_post', 'seopress_pro_save_metabox', 10, 2);
	function seopress_pro_save_metabox($post_id, $post) {
		//Nonce
		if ( ! isset($_POST['seopress_pro_cpt_nonce']) || ! wp_verify_nonce($_POST['seopress_pro_cpt_nonce'], plugin_basename(__FILE__))) {
			return $post_id;
		}

		//Post type object
		$post_type = get_post_type_object($post->post_type);

		//Check permission
		if ( ! current_user_can($post_type->cap->edit_post, $post_id)) {
			return $post_id;
		}

		if ('attachment' !== get_post_type($post_id) && 'seopress_schemas' !== get_post_type($post_id)) {
			//Automatic
			if (isset($_POST['seopress_pro_schemas'])) {
				update_post_meta($post_id, '_seopress_pro_schemas', $_POST['seopress_pro_schemas']);
			}

			//Disable all automatic schemas
			if (isset($_POST['seopress_pro_rich_snippets_disable_all'])) {
				update_post_meta($post_id, '_seopress_pro_rich_snippets_disable_all', '1');
			} else {
				delete_post_meta($post_id, '_seopress_pro_rich_snippets_disable_all', '');
			}

			//Disable automatic schemas individually
			if (isset($_POST['seopress_pro_rich_snippets_disable'])) {
				update_post_meta($post_id, '_seopress_pro_rich_snippets_disable', $_POST['seopress_pro_rich_snippets_disable']);
			} else {
				delete_post_meta($post_id, '_seopress_pro_rich_snippets_disable', '');
			}

			// SEOPress >= 3.9
			if (! seopress_get_service('EnqueueModuleMetabox')->canEnqueue()) {
				$_seopress_pro_rich_snippets_videos_duration = null;
				if (isset($_POST['seopress_pro_rich_snippets_videos_duration'])) {
					$duration = $_POST['seopress_pro_rich_snippets_videos_duration'];
					$findme = ':';
					$pos = strpos($duration, $findme);
					if (false === $pos) {
						$_POST['seopress_pro_rich_snippets_videos_duration'] = '00:' . $_POST['seopress_pro_rich_snippets_videos_duration'];
					}
					$_seopress_pro_rich_snippets_videos_duration = esc_html($_POST['seopress_pro_rich_snippets_videos_duration']);
				}

				if ( ! isset($_POST['seopress_pro_rich_snippets_data'])) {
					delete_post_meta($post_id, '_seopress_pro_schemas_manual');

					return;
				}

				$data_schemas = $_POST['seopress_pro_rich_snippets_data'];
				$keys_rich_snippets = seopress_get_keys_rich_snippets();
				$data_pro_rich_snippets = [];

				foreach ($data_schemas as $number_item => $value) {
					foreach ($keys_rich_snippets as $key => $item) {
						if (isset($value[$item['post_key']])) {
							$data_pro_rich_snippets[$number_item][$item['key']] = $value[$item['post_key']];
						}
					}
				}

				update_post_meta($post_id, '_seopress_pro_schemas_manual', array_values($data_pro_rich_snippets));
			}
		}
	}
}

if ('1' == seopress_get_toggle_option('rich-snippets') && '1' === seopress_pro_get_service('OptionPro')->getRichSnippetEnable()) {
	if (is_user_logged_in()) {
		if (is_super_admin()) {
			echo seopress_pro_admin_std_metaboxe_display();
		} else {
			global $wp_roles;

			//Get current user role
			if (isset(wp_get_current_user()->roles[0])) {
				$seopress_user_role = wp_get_current_user()->roles[0];
				//If current user role matchs values from Security settings then apply
				if (function_exists('seopress_advanced_security_metaboxe_sdt_role_hook_option') && '' != seopress_advanced_security_metaboxe_sdt_role_hook_option()) {
					if (array_key_exists($seopress_user_role, seopress_advanced_security_metaboxe_sdt_role_hook_option())) {
						//do nothing
					} else {
						echo seopress_pro_admin_std_metaboxe_display();
					}
				} else {
					echo seopress_pro_admin_std_metaboxe_display();
				}
			}
		}
	}
}
