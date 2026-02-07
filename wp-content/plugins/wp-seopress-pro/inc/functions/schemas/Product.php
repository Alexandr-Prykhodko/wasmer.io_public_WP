<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//Products JSON-LD
function seopress_automatic_rich_snippets_products_option($schema_datas) {

    //if no data
    if (0 != count(array_filter($schema_datas))) {
        //Init
        global $post;
        global $product;

        $products_name = $schema_datas['name'];
        if ('' == $products_name) {
            $products_name = the_title_attribute('echo=0');
        }

        $products_description = $schema_datas['description'];
        if ('' == $products_description) {
            $products_description = wp_trim_words(esc_html(get_the_excerpt()), 30);
        }

        $products_img = $schema_datas['img'];
        if ('' == $products_img && '' != get_the_post_thumbnail_url(get_the_ID(), 'large')) {
            $products_img = get_the_post_thumbnail_url(get_the_ID(), 'large');
        }

        $products_price = $schema_datas['price'];

        if (isset($product) && '' == $products_price && method_exists($product, 'get_price') && '' != $product->get_price()) {
            $products_price = $product->get_price();
        }

        $products_price_valid_date = $schema_datas['price_valid_date'];

        if (isset($product) && '' == $products_price_valid_date && method_exists($product, 'get_date_on_sale_to') && '' != $product->get_date_on_sale_to()) {
            $products_price_valid_date = $product->get_date_on_sale_to();
            $products_price_valid_date = $products_price_valid_date->date('m-d-Y');
        } else {
            $products_price_valid_date = gmdate( 'Y-12-31', time() + YEAR_IN_SECONDS );
        }

        $products_sku = $schema_datas['sku'];
        if (isset($product) && '' == $products_sku && method_exists($product, 'get_sku') && '' != $product->get_sku()) {
            $products_sku = $product->get_sku();
        }

        $products_global_ids = $schema_datas['global_ids'];

        if (isset($product) && method_exists($product, 'get_id')) {
            if('' != get_post_meta($product->get_id(), 'sp_wc_barcode_type_field', true) && 'none' != get_post_meta($product->get_id(), 'sp_wc_barcode_type_field', true)) {
                $products_global_ids = get_post_meta($product->get_id(), 'sp_wc_barcode_type_field', true);
            } else {
                $products_global_ids = 'gtin';
            }
        }

        $products_global_ids_value = $schema_datas['global_ids_value'];

        if (isset($product) && method_exists($product, 'get_id')) {
            if('' != get_post_meta($product->get_id(), 'sp_wc_barcode_field', true)) {
                $products_global_ids_value = get_post_meta($product->get_id(), 'sp_wc_barcode_field', true);
            } else {
                $products_global_ids_value = $product->get_global_unique_id();
            }
        }

        $products_brand = $schema_datas['brand'];

        $products_currency = $schema_datas['currency'];
        if ('' == $products_currency && function_exists('get_woocommerce_currency') && get_woocommerce_currency()) {
            $products_currency = get_woocommerce_currency();
        } elseif ('' == $products_currency && function_exists('edd_get_currency') && edd_get_currency()) {
            $products_currency = edd_get_currency();
        } elseif ('' == $products_currency) {
            $products_currency = 'USD';
        }

        $products_condition = $schema_datas['condition'];
        if ('' == $products_condition) {
            $products_condition = seopress_check_ssl() . 'schema.org/NewCondition';
        }

        $products_availability = $schema_datas['availability'];

        if ('' == $products_availability) {
            $products_availability = seopress_check_ssl() . 'schema.org/InStock';
        }

        $json = [
            '@context' => seopress_check_ssl() . 'schema.org/',
            '@type' => 'Product',
            'name' => $products_name,
            'image' => $products_img,
            'description' => $products_description,
            'sku' => $products_sku,
        ];

        if ('' != $products_global_ids && $products_global_ids !='none' && '' != $products_global_ids_value) {
            $json[$products_global_ids] = $products_global_ids_value;
        }

        //brand
        if ('' != $products_brand) {
            $json['brand'] = [
                '@type' => 'Brand',
                'name' => $products_brand,
            ];
        }

        if (isset($product) && true === comments_open(get_the_ID())) {//If Reviews is true
            //review
            $args = [
                'meta_key' => 'rating',
                'number' => 1,
                'status' => 'approve',
                'post_status' => 'publish',
                'parent' => 0,
                'orderby' => 'meta_value_num',
                'order' => 'DESC',
                'post_id' => get_the_ID(),
                'post_type' => 'product',
            ];

            $comments = get_comments($args);

            if ( ! empty($comments)) {
                $json['review'] = [
                    '@type' => 'Review',
                    'reviewRating' => [
                        '@type' => 'Rating',
                        'ratingValue' => get_comment_meta($comments[0]->comment_ID, 'rating', true),
                    ],
                    'author' => [
                        '@type' => 'Person',
                        'name' => get_comment_author($comments[0]->comment_ID),
                    ],
                ];
            }

            //aggregateRating
            if (isset($product) && method_exists($product, 'get_review_count') && $product->get_review_count() >= 1) {
                $json['aggregateRating'] = [
                    '@type' => 'AggregateRating',
                    'ratingValue' => $product->get_average_rating(),
                    'reviewCount' => $product->get_review_count(),
                ];
            }
        }
        elseif(isset($schema_datas['positive_notes']) || isset($schema_datas['negative_notes'])) {

            $json['review'] = [
                '@type' => 'Review',
                'author' => [
                    '@type' => 'Person',
                    'name' => get_the_author(),
                ],


            ];
            if(!empty($schema_datas['positive_notes'])) {
                $json['review']['positiveNotes'] = [
                    '@type' => 'ItemList',
                    'itemListElement' => [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => $schema_datas['positive_notes']
                    ]
                ];

            }

            if(!empty($schema_datas['negative_notes'])) {
                $json['review']['negativeNotes'] = [
                    '@type' => 'ItemList',
                    'itemListElement' => [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => $schema_datas['negative_notes']
                    ]
                ];

            }
        }

        //Variable product
        if (isset($product) && method_exists($product, 'is_type') && $product->is_type('variable')) {
            $variations = $product->get_available_variations();

            $i = 1;
            
            foreach ($variations as $key => $value) {
                $product_global_ids = $schema_datas['global_ids'];
                $product_barcode = $schema_datas['global_ids_value'];
                $product_price = $schema_datas['price'];
                $variation = wc_get_product($value['variation_id']);
                
                if (isset($value['seopress_global_ids']) && ! empty($value['seopress_global_ids'])) {
                    $product_global_ids = $value['seopress_global_ids'];
                } else {
                    $product_global_ids = 'gtin';
                }
                if (isset($value['seopress_barcode']) && ! empty($value['seopress_barcode'])) {
                    $product_barcode = $value['seopress_barcode'];
                } elseif (isset($variation) && method_exists($variation, 'get_global_unique_id') && '' != $variation->get_global_unique_id()) {
                    $product_barcode = $variation->get_global_unique_id();
                }
                
                $variation_price_valid_date = '';
                if (isset($variation) && '' == $variation_price_valid_date && method_exists($variation, 'get_date_on_sale_to') && '' != $variation->get_date_on_sale_to()) {
                    $variation_price_valid_date = $variation->get_date_on_sale_to();
                    $variation_price_valid_date = $variation_price_valid_date->date('m-d-Y');
                } else {
                    if ( ! empty($schema_datas['price_valid_date'])) {
                        try {
                            $date = new \DateTime($schema_datas['price_valid_date']);
                            $variation_price_valid_date = $date->format('m-d-Y');
                        } catch (\Exception $e) {
                            $variation_price_valid_date = $schema_datas['price_valid_date'];
                        }
                    } else {
                        $variation_price_valid_date = gmdate( 'Y-12-31', time() + YEAR_IN_SECONDS );
                    }
                }

                if (!empty($product_global_ids) && 'none' === $product_global_ids) {
                    if (!empty($products_global_ids)) {
                        $product_global_ids = $products_global_ids;
                    } else {
                        $product_global_ids = 'gtin';
                    }
                }

                if (empty($product_barcode)) {
                    $product_barcode = $products_global_ids_value;
                }

                $availability = sprintf('%s%s/InStock', seopress_check_ssl(), 'schema.org');
                if ( ! $value['is_in_stock']) {
                    $availability = sprintf('%s%s/OutOfStock', seopress_check_ssl(), 'schema.org');
                }

                $sku = $schema_datas['sku'];
                if (empty($sku) || 'none' === $sku || $product->get_sku() === $sku) {
                    $sku = empty($value['sku']) ? $product->get_sku() : $value['sku'];
                }

                $variation_price = $product_price;
                if (isset($variation) && function_exists('wc_get_price_including_tax') && function_exists('wc_get_price_excluding_tax')) {
                    if ('incl' === get_option('woocommerce_tax_display_shop')) {
                        $variation_price = wc_get_price_including_tax($variation);
                    } else {
                        $variation_price = wc_get_price_excluding_tax($variation);
                    }
                }

                $offer = [
                    '@type' => 'Offer',
                    'url' => $variation->get_permalink(),
                    'sku' => $sku,
                    'price' => is_float($variation_price) ? number_format($variation_price, 2, '.', '') : $variation_price,
                    'priceCurrency' => $products_currency,
                    'itemCondition' => $products_condition,
                    'availability' => $availability,
                    'priceValidUntil' => $variation_price_valid_date,
                ];

                $shippingDetails = seopress_get_shipping_schema( $variation );
                if( ! empty( $shippingDetails ) ){
                    $offer['shippingDetails'] = $shippingDetails;
                }

                if (! empty($product_barcode)) {
                    $offer[$product_global_ids] = $product_barcode;
                }

                $json['offers'][] = $offer;

                ++$i;
            }
        } elseif ('' != $products_price) {
            $json['offers'] = [
                '@type' => 'Offer',
                'url' => get_permalink(),
                'priceCurrency' => $products_currency,
                'price' => is_float($products_price) ? number_format($products_price, 2, '.', '') : $products_price,
                'priceValidUntil' => $products_price_valid_date,
                'itemCondition' => $products_condition,
                'availability' => $products_availability,
            ];

            $shippingDetails = seopress_get_shipping_schema( $product );
            if( ! empty( $shippingDetails ) ){
                $json['offers']['shippingDetails'] = $shippingDetails;
            }
        }

        $json = array_filter($json);

        $json = apply_filters('seopress_schemas_auto_product_json', $json);

        $json = '<script type="application/ld+json">' . wp_json_encode($json) . '</script>' . "\n";

        $json = apply_filters('seopress_schemas_auto_product_html', $json);

        echo $json;
    }
}

 /**
 * @since 7.4.0
 *
 * @param   WC_Product  $wc_product
 * @return  array       $shippingOffers  Schema
 */
function seopress_get_shipping_schema( $wc_product ){
    if (!$wc_product) {
        return [];
    }

    if (!method_exists($wc_product, 'needs_shipping') && $wc_product->needs_shipping()) {
        return [];
    }

    $needs_shipping = $wc_product->needs_shipping();
    if( ! $needs_shipping ){
        return [];
    }

    $shipping_class_id = (int) $wc_product->get_shipping_class_id();
    $currency          = get_woocommerce_currency();

    // Create an offer for each rate in each zone
    $shippingOffers = [];
    foreach ( WC_Shipping_Zones::get_zones() as $zone ) {
        $zoneShippingDestination = [];
        $locations = $zone['zone_locations'] ?? [];
        foreach ( $locations as $location ) {
            if( $location->type === 'country' && $location->code ){
                $zoneShippingDestination[] = ['@type' => 'DefinedRegion', 'addressCountry' => $location->code];
            }
            if( $location->type === 'postcode' && $location->code ){
                $zoneShippingDestination[] = ['@type' => 'DefinedRegion', 'postalCode' => $location->code];
            }
        }

        foreach ( $zone['shipping_methods'] as $method ) {
            $instance = $method->instance_settings;
            $cost     = isset( $instance['cost'] ) ? (float) $instance['cost'] : ( isset( $instance['min_amount'] ) ? (float) $instance['min_amount'] : 0 );
            if ( $shipping_class_id && isset( $instance['type'] ) && $instance['type'] === 'class' ) {
                $cost_key = 'class_cost_' . (int) $shipping_class_id;
                if( ! empty( $instance[$cost_key] ) ){
                    $cost += (float) $instance[$cost_key];
                }
            }
            $shippingOffers[] = array(
                '@type' => 'OfferShippingDetails',
                'shippingDestination' => $zoneShippingDestination,
                'shippingRate' => array(
                    '@type'    => 'MonetaryAmount',
                    'value'    => $cost,
                    'currency' => $currency
                ),
            );
        }
    }

    return $shippingOffers;
}
