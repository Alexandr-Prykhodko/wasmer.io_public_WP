<?php
defined('ABSPATH') or die('Please don&rsquo;t call the plugin directly. Thanks :)');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Robots.txt
///////////////////////////////////////////////////////////////////////////////////////////////////
//Options Robots.txt
if (seopress_pro_get_service('OptionPro')->getRobotsTxtEnable() === '1') {
    function seopress_filter_robots_txt($output, $public) {
        $seopress_robots = seopress_pro_get_service('OptionPro')->getRobotsTxtFile();
        $seopress_robots = apply_filters('seopress_robots_txt_file', $seopress_robots);
        return $seopress_robots;
    };
    add_filter('robots_txt', 'seopress_filter_robots_txt', 10, 2);
}
