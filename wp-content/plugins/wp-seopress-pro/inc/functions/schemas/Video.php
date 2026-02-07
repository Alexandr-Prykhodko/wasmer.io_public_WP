<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//Videos JSON-LD
function seopress_automatic_rich_snippets_videos_option($schema_datas) {
    //if no data
    if (0 != count(array_filter($schema_datas))) {
        $videos_name 							     = $schema_datas['name'];
        $videos_description 					= $schema_datas['description'];
        $videos_img 							      = $schema_datas['img'];
        $videos_duration 						  = $schema_datas['duration'];
        $videos_url 							      = $schema_datas['url'];
        $videos_uploaded_date 							      = $schema_datas['date_posted'];

        $json = [
            '@context'     => seopress_check_ssl() . 'schema.org',
            '@type'        => 'VideoObject',
            'name'         => $videos_name,
            'description'  => $videos_description,
            'thumbnailUrl' => $videos_img,
            'contentUrl'   => $videos_url,
            'embedUrl'     => $videos_url,
        ];

        if(!empty($videos_uploaded_date)){
            $json['uploadDate'] = date('c', strtotime($videos_uploaded_date));
        }
        else if (get_the_date()) {
            $json['uploadDate'] = get_the_date('c');
        }

        if ('' != $videos_duration) {
            $time   = explode(':', $videos_duration);
            $sec 	  = isset($time[2]) ? $time[2] : 00;
            $min 	  = ($time[0] * 60.0 + $time[1] * 1.0);

            $json['duration'] = 'PT' . $min . 'M' . $sec . 'S';
        }

        if (!empty(seopress_get_service('SocialOption')->getSocialKnowledgeName())) {
            $json['publisher'] = [
                '@type' => 'Organization',
                'name'  => seopress_get_service('SocialOption')->getSocialKnowledgeName(),
                'logo'  => [
                    '@type' => 'ImageObject',
                    'url'   => seopress_pro_get_service('OptionPro')->getArticlesPublisherLogo(),
                ],
            ];
        }

        $json = array_filter($json);

        $json = apply_filters('seopress_schemas_auto_video_json', $json);

        $json = '<script type="application/ld+json">' . wp_json_encode($json) . '</script>' . "\n";

        $json = apply_filters('seopress_schemas_auto_video_html', $json);

        echo $json;
    }
}
