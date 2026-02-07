<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//Courses JSON-LD
function seopress_automatic_rich_snippets_courses_option($schema_datas) {
    //if no data
    if (0 != count(array_filter($schema_datas))) {
        $courses_title   = $schema_datas['title'];
        $courses_desc 	 = $schema_datas['desc'];
        $courses_school  = $schema_datas['school'];
        $courses_website = $schema_datas['website'];
        $courses_offers  = $schema_datas['offers'];
        $courses_instances = $schema_datas['instances'];

        $json = [
            '@context'    => seopress_check_ssl() . 'schema.org',
            '@type'       => 'Course',
            'name'        => $courses_title,
            'description' => $courses_desc,
            'offers'      => $courses_offers,
            'hasCourseInstance' => $courses_instances,
        ];

        if ('' != $courses_school) {
            $json['provider'] = [
                '@type'  => 'Organization',
                'name'   => $courses_school,
                'url'    => $courses_website,
            ];
        }

        $json = array_filter($json);

        $json = apply_filters('seopress_schemas_auto_course_json', $json);

        $json = '<script type="application/ld+json">' . wp_json_encode($json) . '</script>' . "\n";

        $json = apply_filters('seopress_schemas_auto_course_html', $json);

        echo $json;
    }
}
