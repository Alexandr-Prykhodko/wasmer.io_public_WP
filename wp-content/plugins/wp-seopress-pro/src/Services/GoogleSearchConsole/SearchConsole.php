<?php

namespace SEOPressPro\Services\GoogleSearchConsole;

defined('ABSPATH') || exit;

class SearchConsole
{
    protected $client = null;

    protected function getDateRange($options = [])
    {
        $date_range = seopress_pro_get_service('OptionPro')->getGSCDateRange() ? seopress_pro_get_service('OptionPro')->getGSCDateRange() : '- 3 months';

        $date_range = apply_filters('seopress_search_console_date_range', $date_range);

        $startDate = isset($options['startDate']) ? $options['startDate'] : (new \DateTime('today'))->modify($date_range)->format('Y-m-d');

        $startDate = apply_filters('seopress_search_console_start_date', $startDate);

        $endDate = isset($options['endDate']) ? $options['endDate'] : (new \DateTime('today'))->format('Y-m-d');

        $endDate = apply_filters('seopress_search_console_end_date', $endDate);

        return  [
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];
    }

    protected function getUrl()
    {
        $url = seopress_pro_get_service('OptionPro')->getGSCDomainProperty() ? true : site_url();

        if ($url === true) {
            $url = wp_parse_url(site_url());
            if (is_array($url) && isset($url['host'])) {
                $url = 'sc-domain:' . $url['host'];
            }
        }

        $url = apply_filters('seopress_search_console_base_url', $url);

        if (SEOPRESS_VERSION === '{VERSION}') {
            $url = 'https://protuts.net'; // Use for testing in dev mode
        }

        return $url;
    }

    public function handle($options = [])
    {
        //Cleaning old data
        delete_post_meta_by_key('_seopress_search_console_analysis_clicks');
        delete_post_meta_by_key('_seopress_search_console_analysis_ctr');
        delete_post_meta_by_key('_seopress_search_console_analysis_impressions');
        delete_post_meta_by_key('_seopress_search_console_analysis_position');

        $client = seopress_pro_get_service('GoogleClient')->getClient();

        $range = $this->getDateRange($options);

        $dimensions = isset($options['dimensions']) ? $options['dimensions'] : ['PAGE'];

        $url = $this->getUrl();

        $queryRequest = new \Google_Service_SearchConsole_SearchAnalyticsQueryRequest();
        $queryRequest->setStartDate($range['startDate']);
        $queryRequest->setEndDate($range['endDate']);
        $queryRequest->setDimensions($dimensions);
        try {
            $response = $client->searchanalytics->query($url, $queryRequest);

            if ( ! property_exists($response, 'rows')) {
                return [];
            }

            return [
                'data' => $response->rows,
                'status' => 'success'
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'status' => 'error'
            ];
        }
    }

    public function getKeywords($options = [])
    {
        $rows = get_transient('seopress_search_console_keywords');
        if ($rows) {
            return [
                'data' => $rows,
                'status' => 'success'
            ];
        }

        $client = seopress_pro_get_service('GoogleClient')->getClient();

        $url = $this->getUrl();

        $queryRequest = new \Google_Service_SearchConsole_SearchAnalyticsQueryRequest();

        $range = $this->getDateRange($options);

        $queryRequest->setStartDate($range['startDate']);
        $queryRequest->setEndDate($range['endDate']);
        $queryRequest->setDimensions(['QUERY']);
        $queryRequest->setRowLimit(isset($options['rowLimit']) ? $options['rowLimit'] : 5);

        try {
            $response = $client->searchanalytics->query($url, $queryRequest);
            $rows = $response->getRows();

            $data = [];

            foreach ($rows as $key => $value) {
                $data[] = [
                    'impressions' => $value->getImpressions(),
                    'clicks' => $value->getClicks(),
                    'ctr' => $value->getCtr(),
                    'position' => $value->getPosition(),
                    'keyword' => $value->getKeys()[0],
                ];
            }


            set_transient('seopress_search_console_keywords', $data, 60 * 60 * 24); // 1 day

            return [
                'data' => $data,
                'status' => 'success'
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'status' => 'error'
            ];
        }

        // Iterate over each row and print the query (keyword) and clicks.
        foreach ($rows as $row) {
            echo 'Keyword: ', $row->getKeys()[0], ', Clicks: ', $row->getClicks(), "\n";
        }
    }

    public function saveDataFromRowResult($row)
    {
        if (is_array($row)) {
            $keys = isset($row['keys']) ? $row['keys'] : [];
        } else {
            $keys = property_exists($row, 'keys') ? $row->keys : [];
        }

        $url = null;

        if ( ! isset($keys[0])) {
            return;
        }

        $url = $keys[0];

        if ( ! is_array($row)) {
            $clicks = property_exists($row, 'clicks') ? $row->clicks : 0;
            $ctr = property_exists($row, 'ctr') ? $row->ctr : 0;
            $impressions = property_exists($row, 'impressions') ? $row->impressions : 0;
            $position = property_exists($row, 'position') ? $row->position : 0;
        } else {
            $clicks = isset($row['clicks']) ? $row['clicks'] : 0;
            $ctr = isset($row['ctr']) ? $row['ctr'] : 0;
            $impressions = isset($row['impressions']) ? $row['impressions'] : 0;
            $position = isset($row['position']) ? $row['position'] : 0;
        }

        if (SEOPRESS_VERSION === '{VERSION}') {
            $url = str_replace('https://protuts.net', site_url(), $url); // Use for testing in dev mode
        }


        $parseUrl = wp_parse_url($url);
        if ( ! isset($parseUrl['scheme']) || ! isset($parseUrl['host']) || ! isset($parseUrl['path'])) {
            $cleanUrl = $url;
        } else {
            $cleanUrl = $parseUrl['scheme'] . '://' . $parseUrl['host'] . $parseUrl['path'];
        }

        $postId = url_to_postid($cleanUrl);

        if ( ! $postId) {
            return;
        }

        update_post_meta($postId, '_seopress_search_console_analysis_clicks', sanitize_text_field($clicks));
        update_post_meta($postId, '_seopress_search_console_analysis_ctr', sanitize_text_field($ctr));
        update_post_meta($postId, '_seopress_search_console_analysis_impressions', sanitize_text_field($impressions));
        update_post_meta($postId, '_seopress_search_console_analysis_position', sanitize_text_field($position));

        return [
            'post_id' => $postId,
            'data' => [
                'clicks' => $clicks,
                'ctr' => $ctr,
                'impressions' => $impressions,
                'position' => $position,
            ],
        ];
    }
}
