<?php

namespace SEOPressPro\JsonSchemas;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Helpers\RichSnippetType;
use SEOPress\Models\GetJsonData;
use SEOPressPro\Models\JsonSchemaValue;

class Product extends JsonSchemaValue implements GetJsonData {
    const NAME = 'product';

    const ALIAS = ['products'];

    protected function getName() {
        return self::NAME;
    }

    /**
     * @since 4.7.0
     *
     * @return array
     */
    protected function getKeysForSchemaManual() {
        return [
            'type' => '_seopress_pro_rich_snippets_type',
            'name' => [
                'value' => '_seopress_pro_rich_snippets_product_name',
                'default' => '%%post_title%%',
            ],
            'description' => [
                'value' => '_seopress_pro_rich_snippets_product_description',
                'default' => '%%post_excerpt%%',
            ],
            'image' => [
                'value' => '_seopress_pro_rich_snippets_product_img',
                'default' => '%%post_thumbnail_url%%',
            ],
            'price' => [
                'value' => '_seopress_pro_rich_snippets_product_price',
                'default' => '%%wc_get_price%%',
            ],
            'priceValidDate' => [
                'value' => '_seopress_pro_rich_snippets_product_price_valid_date',
                'default' => '%%wc_price_valid_date%%',
            ],
            'sku' => [
                'value' => '_seopress_pro_rich_snippets_product_sku',
                'default' => '%%wc_sku%%',
            ],
            'brand' => '_seopress_pro_rich_snippets_product_brand',
            'globalIds' => '_seopress_pro_rich_snippets_product_global_ids',
            'globalIdsValue' => '_seopress_pro_rich_snippets_product_global_ids_value',
            'priceCurrency' => '_seopress_pro_rich_snippets_product_price_currency',
            'condition' => [
                'value' => '_seopress_pro_rich_snippets_product_condition',
                'default' => 'NewCondition',
            ],
            'availability' => '_seopress_pro_rich_snippets_product_availability',
            'positiveNotes' => '_seopress_pro_rich_snippets_product_positive_notes',
            'negativeNotes' => '_seopress_pro_rich_snippets_product_negative_notes',
            'energy_consumption' => '_seopress_pro_rich_snippets_product_energy_consumption',
        ];
    }

    /**
     * @since 4.6.0
     *
     * @return array
     *
     * @param array $keys
     * @param array $data
     */
    protected function getVariablesByKeysAndData($keys, $data = []) {
        $variables = parent::getVariablesByKeysAndData($keys, $data);

        if ('none' === $variables['globalIds']) {
            $variables['globalIds'] = '';
        }
        if ('none' === $variables['priceCurrency']) {
            $variables['priceCurrency'] = '';
        }

        return $variables;
    }

    /**
     * @since 4.6.0
     *
     * @param array $context
     *
     * @return array
     */
    public function getJsonData($context = null) {
        $data = $this->getArrayJson();

        $typeSchema = isset($context['type']) ? $context['type'] : RichSnippetType::MANUAL;

        $variables = $this->getVariablesByType($typeSchema, $context);

        if (isset($variables['globalIds'],$variables['globalIdsValue']) && ! empty($variables['globalIds']) && ! empty($variables['globalIdsValue'])) {
            $data[$variables['globalIds']] = $variables['globalIdsValue'];
        }

        if (isset($variables['brand'], $context['post']->ID) && ! empty($variables['brand'])) {
            $term_list = wp_get_post_terms($context['post']->ID, $variables['brand'], ['fields' => 'names']);

            if ( ! empty($term_list) && ! is_wp_error($term_list)) {
                $variables['brand'] = $term_list[0];
            } else {
                unset($variables['brand']);
            }
        }

        //brand
        if (isset($variables['brand'])) {
            $contextWithVariables = $context;
            $contextWithVariables['variables'] = [
                'name' => $variables['brand'],
            ];
            $contextWithVariables['type'] = RichSnippetType::SUB_TYPE;
            $schema = seopress_get_service('JsonSchemaGenerator')->getJsonFromSchema(Brand::NAME, $contextWithVariables, ['remove_empty' => true]);
            if (count($schema) > 1) {
                $data['brand'] = $schema;
            }
        }

        if(isset($variables['energy_consumption'])){
            $data['hasEnergyConsumptionDetails'] = [
                '@type' => 'EnergyConsumptionDetails',
                'hasEnergyEfficiencyCategory' => $variables['energy_consumption'],
                "energyEfficiencyScaleMin" => "https://schema.org/EUEnergyEfficiencyCategoryG",
                "energyEfficiencyScaleMax" => "https://schema.org/EUEnergyEfficiencyCategoryA3Plus"
            ];
        }


        // Just for WooCommerce
        if (isset($context['product']) && null !== $context['product'] && isset($context['post']->ID) && comments_open($context['post']->ID)) {
            $args = [
                'meta_key' => 'rating',
                'number' => 1,
                'status' => 'approve',
                'post_status' => 'publish',
                'parent' => 0,
                'orderby' => 'meta_value_num',
                'order' => 'DESC',
                'post_id' => $context['post']->ID,
                'post_type' => 'product',
            ];

            $comments = get_comments($args);

            if ( ! empty($comments)) {
                $contextWithVariables = $context;
                $contextWithVariables['variables'] = [
                    'ratingValue' =>  get_comment_meta($comments[0]->comment_ID, 'rating', true),
                    'ratingAuthor' => get_comment_author($comments[0]->comment_ID),
                ];
                $contextWithVariables['type'] = RichSnippetType::SUB_TYPE;
                $schema = seopress_get_service('JsonSchemaGenerator')->getJsonFromSchema(Review::NAME, $contextWithVariables, ['remove_empty' => true]);
                if (count($schema) > 1) {
                    $data['review'] = $schema;
                }
            }

            if (function_exists('wc_get_product')) {
                $product = wc_get_product($context['post']->ID);

                if (method_exists($product, 'get_review_count') && $product->get_review_count() >= 1) {
                    $contextWithVariables = $context;
                    $contextWithVariables['variables'] = [
                        'ratingValue' => $product->get_average_rating(),
                        'reviewCount' => $product->get_review_count(),
                    ];
                    $contextWithVariables['type'] = RichSnippetType::SUB_TYPE;
                    $schema = seopress_get_service('JsonSchemaGenerator')->getJsonFromSchema(AggregateRating::NAME, $contextWithVariables, ['remove_empty' => true]);
                    if (count($schema) > 1) {
                        $data['aggregateRating'] = $schema;
                    }
                }
            }
        }
        // Like article review
        else if (isset($variables['positiveNotes']) ||  isset($variables['negativeNotes'])) {
            $contextWithVariables = $context;
            $contextWithVariables['variables'] = [
                'ratingAuthor' => '%%post_author%%',
                'positiveNotes' => isset($variables['positiveNotes']) ? $variables['positiveNotes'] : [],
                'negativeNotes' => isset($variables['negativeNotes']) ? $variables['negativeNotes'] : [],
            ];
            $contextWithVariables['type'] = RichSnippetType::SUB_TYPE;
            $schema = seopress_get_service('JsonSchemaGenerator')->getJsonFromSchema(Review::NAME, $contextWithVariables, ['remove_empty' => true]);
            if (count($schema) > 1) {
                $data['review'] = $schema;
            }
        }

        if (isset($variables['price'])) {
            $contextWithVariables = $context;
            $contextWithVariables['variables'] = [
                'url' => '%%post_url%%',
                'priceCurrency' => isset($variables['priceCurrency']) ? $variables['priceCurrency'] : '',
                'price' => isset($variables['price']) ? $variables['price'] : '',
                'priceValidUntil' => isset($variables['priceValidDate']) ? $variables['priceValidDate'] : '',
                'itemCondition' => isset($variables['condition']) ? sprintf('%sschema.org/%s', seopress_check_ssl(), $variables['condition']) : sprintf('%sschema.org/%s', seopress_check_ssl(), $variables['NewCondition']),
                'availability' => isset($variables['availability']) ? sprintf('%sschema.org/%s', seopress_check_ssl(), $variables['availability']) : '',
            ];

            // Get woocommerce currency if it is not set
            if( empty( $contextWithVariables['variables']['priceCurrency'] ) ){
                if ( isset( $context['product'] ) && null !== $context['product'] && is_a( $context['product'], 'WC_Product' ) ) {
                    $contextWithVariables['variables']['priceCurrency'] = get_woocommerce_currency();
                }
            }

            $contextWithVariables['type'] = RichSnippetType::SUB_TYPE;
            $schema = seopress_get_service('JsonSchemaGenerator')->getJsonFromSchema(Offer::NAME, $contextWithVariables, ['remove_empty' => true]);     
            
            if (count($schema) > 1) {
                $data['offers'] = $schema;
                // Woocommerce shipping details
                $shipping_details_schema = $this->get_woocommerce_shipping_details_schema( $context );
                if ( ! empty( $shipping_details_schema ) ){
                    $data['offers']['shippingDetails'] = $shipping_details_schema;
                }
            }
        }

        $data = seopress_get_service('VariablesToString')->replaceDataToString($data, $variables);

        return apply_filters('seopress_pro_get_json_data_product', $data, $context);
    }

    /**
     * @since 4.6.0
     *
     * @param  $data
     *
     * @return array
     */
    public function cleanValues($data) {
        if (isset($data['review']) && isset($data['review']['@context'])) {
            unset($data['review']['@context']);
        }

        return parent::cleanValues($data);
    }

    /**
     * @since 7.4.0
     * 
     * Returns shippingDetails schema for a Woocommerce product
     * 
     * @param   array  $context
     * @return  array  $schema
     */
    public function get_woocommerce_shipping_details_schema( $context ){
        $schema = [];
        if ( isset( $context['product'] ) && null !== $context['product'] ) {
            $product = $context['product'];
            if ( is_a( $product, 'WC_Product' ) && $product->needs_shipping() ) {
                foreach ( \WC_Shipping_Zones::get_zones() as $zone ) {
                    $destinationSchema = $this->get_woocommerce_shipping_destination_schema( $zone, $context );
                    foreach ( $zone['shipping_methods'] as $method ) {
                        $contextWithVariables = $context;
                        $contextWithVariables['variables'] = [
                            'shippingAmount'      => $this->get_woocommerce_shipping_amount( $method, $product ),
                            'currency'            => get_woocommerce_currency(),
                            'shippingDestination' => $destinationSchema
                        ];
                        $schema[] = seopress_get_service('JsonSchemaGenerator')->getJsonFromSchema(OfferShippingDetails::NAME, $contextWithVariables, ['remove_empty' => false]);
                    }
                }
            }
        }
        return $schema;
    }

    /**
     * @since 7.4.0
     * 
     * @param   array  $zone     WC zone data
     * @param   array  $context  
     * @return  array  $schema   Array of DefinedRegion schemas
     */
    public function get_woocommerce_shipping_destination_schema( $zone, $context ){
        $schema    = [];
        $locations = $zone['zone_locations'] ?? [];
        foreach ( $locations as $location ) {
            if( $location->type === 'country' && $location->code ){
                $context['variables']['addressCountry'] = $location->code;
                $schema[] = seopress_get_service('JsonSchemaGenerator')->getJsonFromSchema(DefinedRegion::NAME, $context, ['remove_empty' => true]);
            }
            if( $location->type === 'postcode' && $location->code ){
                $context['variables']['postalCode'] = $location->code;
                $schema[] = seopress_get_service('JsonSchemaGenerator')->getJsonFromSchema(DefinedRegion::NAME, $context, ['remove_empty' => true]);
            }
        }
        return $schema;
    }

    /**
     * @since 7.4.0
     * 
     * @param   WC_Shipping_Method  $method
     * @param   WC_Product          $product
     * @return  string              $cost
     */
    public function get_woocommerce_shipping_amount( $method, $product ){
        $shipping_class_id = (int) $product->get_shipping_class_id();
        $instance = $method->instance_settings;
        $cost     = isset( $instance['cost'] ) ? (float) $instance['cost'] : ( isset( $instance['min_amount'] ) ? (float) $instance['min_amount'] : 0 );
        if ( $shipping_class_id && isset( $instance['type'] ) && $instance['type'] === 'class' ) {
            $cost_key = 'class_cost_' . (int) $shipping_class_id;
            if( ! empty( $instance[$cost_key] ) ){
                $cost += (float) $instance[$cost_key];
            }
        }
        return $cost;
    }
}
