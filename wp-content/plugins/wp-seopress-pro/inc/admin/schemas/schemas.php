<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

/**
 * Return the conditions for schemas.
 *
 * @since 3.8.1
 *
 * @author Julio Potier
 *
 * @return (array)
 **/
function seopress_get_schemas_conditions() {
    return ['equal' => __('is equal to', 'wp-seopress-pro'), 'not_equal' => __('is NOT equal to', 'wp-seopress-pro')];
}

/**
 * Return the filters for schemas.
 *
 * @since 3.8.1
 *
 * @author Julio Potier
 *
 * @return (array)
 **/
function seopress_get_schemas_filters() {
    return [
        'post_type' => __('Post Type', 'wp-seopress-pro'),
        'taxonomy' => __('Term Taxonomy', 'wp-seopress-pro'),
        'postId' => __('Post ID', 'wp-seopress-pro'),
    ];
}

/**
 * Return default values for retrocompat.
 *
 * @since 3.8.1
 *
 * @author Julio Potier
 *
 * @return (array)
 *
 * @param mixed $rule
 **/
function seopress_get_default_schemas_rules($rule) {
    return [
        [
            [
                'filter' => 'post_type',
                'cpt' => $rule, 'taxo' => 0,
                'cond' => 'equal',
            ],
        ],
    ];
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Register SEOPress Schemas Custom Post Type
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_schemas_fn() {
    $labels = [
        'name' => _x('Schemas', 'Post Type General Name', 'wp-seopress-pro'),
        'singular_name' => _x('Schema', 'Post Type Singular Name', 'wp-seopress-pro'),
        'menu_name' => __('Schemas', 'wp-seopress-pro'),
        'name_admin_bar' => __('Schemas', 'wp-seopress-pro'),
        'archives' => __('Item Archives', 'wp-seopress-pro'),
        'parent_item_colon' => __('Parent Item:', 'wp-seopress-pro'),
        'all_items' => __('All schemas', 'wp-seopress-pro'),
        'add_new_item' => __('Add New schema', 'wp-seopress-pro'),
        'add_new' => __('Add schema', 'wp-seopress-pro'),
        'new_item' => __('New schema', 'wp-seopress-pro'),
        'edit_item' => __('Edit schema', 'wp-seopress-pro'),
        'update_item' => __('Update schema', 'wp-seopress-pro'),
        'view_item' => __('View schema', 'wp-seopress-pro'),
        'search_items' => __('Search schema', 'wp-seopress-pro'),
        'not_found' => __('Not found', 'wp-seopress-pro'),
        'not_found_in_trash' => __('Not found in Trash', 'wp-seopress-pro'),
        'featured_image' => __('Featured Image', 'wp-seopress-pro'),
        'set_featured_image' => __('Set featured image', 'wp-seopress-pro'),
        'remove_featured_image' => __('Remove featured image', 'wp-seopress-pro'),
        'use_featured_image' => __('Use as featured image', 'wp-seopress-pro'),
        'insert_into_item' => __('Insert into item', 'wp-seopress-pro'),
        'uploaded_to_this_item' => __('Uploaded to this item', 'wp-seopress-pro'),
        'items_list' => __('Schemas list', 'wp-seopress-pro'),
        'items_list_navigation' => __('Schemas list navigation', 'wp-seopress-pro'),
        'filter_items_list' => __('Filter schema list', 'wp-seopress-pro'),
    ];
    $args = [
        'label' => __('Schemas', 'wp-seopress-pro'),
        'description' => __('List of Schemas', 'wp-seopress-pro'),
        'labels' => $labels,
        'supports' => ['title'],
        'hierarchical' => false,
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => false,
        'menu_icon' => 'dashicons-excerpt-view',
        'show_in_admin_bar' => false,
        'show_in_nav_menus' => false,
        'can_export' => true,
        'has_archive' => false,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
        'capability_type' => 'schema',
        'capabilities' => [
            'edit_post' => 'edit_schema',
            'edit_posts' => 'edit_schemas',
            'edit_others_posts' => 'edit_others_schemas',
            'publish_posts' => 'publish_schemas',
            'read_post' => 'read_schema',
            'read_private_posts' => 'read_private_schemas',
            'delete_post' => 'delete_schema',
            'delete_others_posts' => 'delete_others_schemas',
            'delete_published_posts' => 'delete_published_schemas',
        ],
    ];
    register_post_type('seopress_schemas', $args);
}
add_action('admin_init', 'seopress_schemas_fn', 10);

///////////////////////////////////////////////////////////////////////////////////////////////////
//Map SEOPress Schema caps
///////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('map_meta_cap', 'seopress_schemas_map_meta_cap', 10, 4);
function seopress_schemas_map_meta_cap($caps, $cap, $user_id, $args) {
    /* If editing, deleting, or reading a schema, get the post and post type object. */
    if ('edit_schema' === $cap || 'delete_schema' === $cap || 'read_schema' === $cap) {
        $post = get_post($args[0]);
        $post_type = get_post_type_object($post->post_type);

        /* Set an empty array for the caps. */
        $caps = [];
    }

    /* If editing a schema, assign the required capability. */
    if ('edit_schema' === $cap) {
        if ($user_id == $post->post_author) {
            $caps[] = $post_type->cap->edit_posts;
        } else {
            $caps[] = $post_type->cap->edit_others_posts;
        }
    }

    /* If deleting a schema, assign the required capability. */
    elseif ('delete_schema' === $cap) {
        if ($user_id == $post->post_author) {
            $caps[] = $post_type->cap->delete_published_posts;
        } else {
            $caps[] = $post_type->cap->delete_others_posts;
        }
    }

    /* If reading a private schema, assign the required capability. */
    elseif ('read_schema' === $cap) {
        if ('private' !== $post->post_status) {
            $caps[] = 'read';
        } elseif ($user_id == $post->post_author) {
            $caps[] = 'read';
        } else {
            $caps[] = $post_type->cap->read_private_posts;
        }
    }

    /* Return the capabilities required by the user. */
    return $caps;
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Set title placeholder for Schemas Custom Post Type
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_schemas_cpt_title($title) {
    $screen = get_current_screen();
    if ('seopress_schemas' == $screen->post_type) {
        $title = __('Enter the name of your schema', 'wp-seopress-pro');
    }

    return $title;
}

add_filter('enter_title_here', 'seopress_schemas_cpt_title');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Add custom buttons to SEOPress Schemas Post Type
///////////////////////////////////////////////////////////////////////////////////////////////////

function seopress_schemas_btn_cpt() {
    $screen = get_current_screen();
    if ('seopress_schemas' == $screen->post_type) {
        ?>
<script>
    jQuery(function() {
        jQuery("body.post-type-seopress_schemas .wrap h1 ~ a").after(
            '<a href="<?php echo admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_rich_snippets'); ?>" id="seopress-schemas-settings" class="page-title-action"><?php _e('Settings', 'wp-seopress-pro'); ?></a>'
        );
    });
</script>
<?php
    }
}
add_action('admin_head', 'seopress_schemas_btn_cpt');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Add buttons to post type list if empty
///////////////////////////////////////////////////////////////////////////////////////////////////
add_action('manage_posts_extra_tablenav', 'seopress_schemas_maybe_render_blank_state');

function seopress_schemas_render_blank_state() {
    $docs = function_exists('seopress_get_docs_links') ? seopress_get_docs_links() : ''; ?>
<div class="seopress-BlankState">

    <h2 class="seopress-BlankState-message">
        <?php esc_html_e('Boost your visibility in search results and increase your traffic and conversions.', 'wp-seopress-pro'); ?>
    </h2>

    <div class="seopress-BlankState-buttons">

        <a class="seopress-BlankState-cta btn btnPrimary"
            href="<?php echo esc_url(admin_url('post-new.php?post_type=seopress_schemas')); ?>">
            <?php esc_html_e('Create a schema', 'wp-seopress-pro'); ?>
        </a>

        <a class="seopress-BlankState-cta btn btnTertiary"
            href="<?php echo $docs['schemas']['add']; ?>"
            target="_blank">
            <?php esc_html_e('Learn more about schemas', 'wp-seopress-pro'); ?>
        </a>

    </div>

</div>

<?php
}
function seopress_schemas_maybe_render_blank_state($which) {
    global $post_type;

    if ('seopress_schemas' === $post_type && 'bottom' === $which) {
        $counts = (array) wp_count_posts($post_type);
        unset($counts['auto-draft']);
        $count = array_sum($counts);

        if (isset($_GET['seopress_support']) && '1' === $_GET['seopress_support']) {
            ?>
<a href="<?php
                echo wp_nonce_url(
                add_query_arg(
                    [
                        'action' => 'seopress_relaunch_upgrader',
                    ],
                    admin_url('admin-post.php')
                ),
                'seopress_relaunch_upgrader'
            ); ?>" class="btn btn-primary">
    Reload upgrader schema
</a>
<?php
        }

        if (0 < $count) {
            return;
        }

        seopress_schemas_render_blank_state();

        echo '<style type="text/css">#posts-filter .wp-list-table, #posts-filter .tablenav.top, .tablenav.bottom .actions, .wrap .subsubsub  { display: none; } #posts-filter .tablenav.bottom { height: auto; } </style>';
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Set messages for Schemas Custom Post Type
///////////////////////////////////////////////////////////////////////////////////////////////////

function seopress_schemas_set_messages($messages) {
    global $post, $post_ID, $typenow;
    $post_type = 'seopress_schemas';

    if ('seopress_schemas' === $typenow) {
        $obj = get_post_type_object($post_type);
        $singular = $obj->labels->singular_name;

        $messages[$post_type] = [
            0 => '', // Unused. Messages start at index 1.
            1 => sprintf(/* translators: %s schema name */ __('%s updated.', 'wp-seopress-pro'), $singular),
            2 => __('Custom field updated.', 'wp-seopress-pro'),
            3 => __('Custom field deleted.', 'wp-seopress-pro'),
            4 => sprintf(/* translators: %s schema name */ __('%s updated.', 'wp-seopress-pro'), $singular),
            5 => isset($_GET['revision']) ? sprintf(/* translators: %1$s schema name, %2$s schema name */ __('%1$s restored to revision from %2$s', 'wp-seopress-pro'), wp_post_revision_title((int) $_GET['revision'], false), $singular) : false,
            6 => sprintf(/* translators: %s schema name */ __('%s published.', 'wp-seopress-pro'), $singular),
            7 => sprintf(/* translators: %s schema name */ __('%s saved.', 'wp-seopress-pro'), $singular),
            8 => sprintf(/* translators: %s schema name */ __('%s submitted.', 'wp-seopress-pro'), $singular),
            9 => sprintf(/* translators: %1$s schema name, %2$s date */ __('%1$s scheduled for: <strong>%2$s</strong>. ', 'wp-seopress-pro'), $singular, date_i18n(__('M j, Y @ G:i', 'wp-seopress-pro'), strtotime($post->post_date))),
            10 => sprintf(/* translators: %s schema name */ __('%s draft updated.', 'wp-seopress-pro'), $singular),
        ];

        return $messages;
    } else {
        return $messages;
    }
}

add_filter('post_updated_messages', 'seopress_schemas_set_messages');

function seopress_schemas_set_messages_list($bulk_messages, $bulk_counts) {
    $bulk_messages['seopress_schemas'] = [
        'updated' => /* translators: %s schema name */ _n('%s schema updated.', '%s schemas updated.', $bulk_counts['updated'], 'wp-seopress-pro'),
        'locked' => /* translators: %s schema name */ _n('%s schema not updated, somebody is editing it.', '%s schemas not updated, somebody is editing them.', $bulk_counts['locked'], 'wp-seopress-pro'),
        'deleted' => /* translators: %s schema name */ _n('%s schema permanently deleted.', '%s schemas permanently deleted.', $bulk_counts['deleted'], 'wp-seopress-pro'),
        'trashed' => /* translators: %s schema name */ _n('%s schema moved to the Trash.', '%s schemas moved to the Trash.', $bulk_counts['trashed'], 'wp-seopress-pro'),
        'untrashed' => /* translators: %s schema name */ _n('%s schema restored from the Trash.', '%s schemas restored from the Trash.', $bulk_counts['untrashed'], 'wp-seopress-pro'),
    ];

    return $bulk_messages;
}
add_filter('bulk_post_updated_messages', 'seopress_schemas_set_messages_list', 10, 2);

///////////////////////////////////////////////////////////////////////////////////////////////////
//Columns for Schemas Custom Post Type
///////////////////////////////////////////////////////////////////////////////////////////////////

add_filter('manage_edit-seopress_schemas_columns', 'seopress_schemas_columns');
add_action('manage_seopress_schemas_posts_custom_column', 'seopress_schemas_display_column', 10, 2);

function seopress_schemas_columns($columns) {
    $columns['seopress_schemas_type'] = __('Data type', 'wp-seopress-pro');
    $columns['seopress_schemas_cpt'] = __('Post type', 'wp-seopress-pro');
    unset($columns['date']);

    return $columns;
}

function seopress_schemas_display_column($column, $post_id) {
    if ('seopress_schemas_type' == $column) {
        if (get_post_meta($post_id, '_seopress_pro_rich_snippets_type', true)) {
            echo get_post_meta($post_id, '_seopress_pro_rich_snippets_type', true);
        }
    }
    if ('seopress_schemas_cpt' == $column) {
        if (get_post_meta($post_id, '_seopress_pro_rich_snippets_rules', true)) {
            $rules = get_post_meta($post_id, '_seopress_pro_rich_snippets_rules', true);
            if ( ! is_array($rules)) {
                $rules = seopress_get_default_schemas_rules($rules);
            }
            $conditions = seopress_get_schemas_conditions();
            $filters = seopress_get_schemas_filters();
            $n = 0;
            $html = '';
            foreach ($rules as $or => $values) {
                foreach ($values as $and => $value) {
                    $filter = esc_html($filters[$value['filter']]);
                    $cond = $conditions[$value['cond']];
                    if ('post_type' === $value['filter'] && post_type_exists($value['cpt'])) {
                        $label = esc_html(get_post_type_object($value['cpt'])->label);
                        $html .= " <strong>$filter</strong> <em>$cond</em> \"$label\" ";
                    } elseif ('taxonomy' === $value['filter'] && term_exists((int) $value['taxo'])) {
                        $tax = get_term($value['taxo']);
                        if ( ! is_wp_error($tax) && is_object($tax)) {
                            $tax = esc_html(get_taxonomy($tax->taxonomy)->label);
                            $label = esc_html(get_term($value['taxo'])->name);
                            $html .= " <strong>$filter</strong> \"$tax\" <em>$cond</em> \"$label\" ";
                        }
                    } elseif ('postId' === $value['filter']) {
                        $label = esc_html($value['postId']);
                        $html .= " <strong>$filter</strong> <em>$cond</em> \"$label\" ";
                    }
                    $html .= __('and', 'wp-seopress-pro');
                    ++$n;
                    if (3 === $n) {
                        $html = trim($html, __('and', 'wp-seopress-pro') . ' ');
                        $html .= '&hellip;';
                        continue 2;
                    }
                }
                $html = trim($html, __('and', 'wp-seopress-pro'));
                $html .= __('or', 'wp-seopress-pro');
            }
            $html = trim($html, __('or', 'wp-seopress-pro'));
            echo $html;
        }
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Display metabox for Schemas Custom Post Type
///////////////////////////////////////////////////////////////////////////////////////////////////
add_action('add_meta_boxes', 'seopress_schemas_init_metabox');
function seopress_schemas_init_metabox() {
    add_meta_box('seopress_schemas', __('Your schema', 'wp-seopress-pro'), 'seopress_schemas_cpt', 'seopress_schemas', 'normal', 'default');
}

function seopress_schemas_cpt($post) {
    $prefix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

    wp_nonce_field(plugin_basename(__FILE__), 'seopress_schemas_cpt_nonce');

    global $typenow;

    //Enqueue scripts
    wp_enqueue_script('jquery-ui-accordion');

    wp_enqueue_script('seopress-pro-media-uploader', plugins_url('assets/js/seopress-pro-media-uploader' . $prefix . '.js', dirname(dirname(dirname(__FILE__)))), ['jquery'], SEOPRESS_PRO_VERSION, false);

    wp_enqueue_script('seopress-pro-rich-snippets', plugins_url('assets/js/seopress-pro-rich-snippets' . $prefix . '.js', dirname(dirname(dirname(__FILE__)))), ['jquery'], SEOPRESS_PRO_VERSION, false);

    wp_enqueue_media();

    wp_enqueue_script('jquery-ui-datepicker');

    //Filter taxonomies list to get WC product attributes
    add_filter('seopress_get_taxonomies_args', 'sp_get_taxonomies_args');
    function sp_get_taxonomies_args($args) {
        $args = [];

        return $args;
    }
    add_filter('seopress_get_taxonomies_list', 'sp_get_taxonomies_list');
    function sp_get_taxonomies_list($terms) {
        unset($terms['seopress_404_cat']);
        unset($terms['nav_menu']);
        unset($terms['link_category']);
        unset($terms['post_format']);

        return $terms;
    }

    //Mapping fields
    function seopress_schemas_mapping_array($post_meta_name, $cases) {
        global $post;

        //Custom fields
        if (function_exists('seopress_get_custom_fields')) {
            $seopress_get_custom_fields = seopress_get_custom_fields();
        }

        //init default case array
        $seopress_schemas_mapping_case = [
            'Select an option' => ['none' => __('None', 'wp-seopress-pro')],
            'Site Meta' => [
                'site_title' => __('Site Title', 'wp-seopress-pro'),
                'tagline' => __('Tagline', 'wp-seopress-pro'),
                'site_url' => __('Site URL', 'wp-seopress-pro'),
            ],
            'Post Meta' => [
                'post_id' => __('Post / Product ID', 'wp-seopress-pro'),
                'post_title' => __('Post Title / Product title', 'wp-seopress-pro'),
                'post_excerpt' => __('Excerpt / Product short description', 'wp-seopress-pro'),
                'post_content' => __('Content', 'wp-seopress-pro'),
                'post_permalink' => __('Permalink', 'wp-seopress-pro'),
                'post_author_name' => __('Author', 'wp-seopress-pro'),
                'post_date' => __('Publish date', 'wp-seopress-pro'),
                'post_updated' => __('Last update', 'wp-seopress-pro'),
            ],
            'Product meta (WooCommerce)' => [
                'product_regular_price' => __('Regular Price', 'wp-seopress-pro'),
                'product_sale_price' => __('Sale Price', 'wp-seopress-pro'),
                'product_price_with_tax' => __('Sales price, including tax', 'wp-seopress-pro'),
                'product_date_from' => __('Sale price dates "From"', 'wp-seopress-pro'),
                'product_date_to' => __('Sale price dates "To"', 'wp-seopress-pro'),
                'product_sku' => __('SKU', 'wp-seopress-pro'),
                'product_barcode_type' => __('Product Global Identifier type', 'wp-seopress-pro'),
                'product_barcode' => __('Product Global Identifier', 'wp-seopress-pro'),
                'product_category' => __('Product category', 'wp-seopress-pro'),
                'product_stock' => __('Product availability', 'wp-seopress-pro'),
            ],
            'Custom taxonomy / Product attribute (WooCommerce)' => [
                'custom_taxonomy' => __('Select your custom taxonomy / product attribute', 'wp-seopress-pro'),
            ],
            'Custom fields' => [
                'custom_fields' => __('Select your custom field', 'wp-seopress-pro'),
            ],
        ];

        //Custom field
        $post_meta_value = get_post_meta($post->ID, '_' . $post_meta_name . '_cf', true);

        $seopress_schemas_cf = '<select name="' . $post_meta_name . '_cf" class="cf">';
        $seopress_schemas_cf .= '<option value=""></option>';

        foreach ($seopress_get_custom_fields as $value) {
            $seopress_schemas_cf .= '<option ' . selected($value, $post_meta_value, false) . ' value="' . $value . '">' . $value . '</option>';
        }

        $seopress_schemas_cf .= '</select>';

        //Custom taxonomy
        $post_meta_value = get_post_meta($post->ID, '_' . $post_meta_name . '_tax', true);

        $seopress_schemas_tax = '<select name="' . $post_meta_name . '_tax" class="tax">';

        $serviceWpData = seopress_get_service('WordPressData');
        $seopress_get_taxonomies = [];
        if ($serviceWpData && method_exists($serviceWpData, 'getTaxonomies')) {
            $seopress_get_taxonomies = $serviceWpData->getTaxonomies();
        }

        $seopress_schemas_tax .= '<option value=""></option>';
        foreach ($seopress_get_taxonomies as $key => $value) {
            $seopress_schemas_tax .= '<option ' . selected($key, $post_meta_value, false) . ' value="' . $key . '">' . $key . '</option>';
        }
        $seopress_schemas_tax .= '</select>';

        if (is_string($cases)) {
            $cases = [$cases];
        }

        foreach ($cases as $case) {
            //LB types list
            if ('lb' === $case) {
                $post_meta_value = get_post_meta($post->ID, '_' . $post_meta_name . '_lb', true);

                $seopress_schemas_lb = '<select name="' . $post_meta_name . '_lb" class="lb">';

                foreach (seopress_lb_types_list() as $type_value => $type_i18n) {
                    $seopress_schemas_lb .= '<option ' . selected($type_value, $post_meta_value, false) . ' value="' . $type_value . '">' . $type_i18n . '</option>';
                }
                $seopress_schemas_lb .= '</select>';
            }

            switch ($case) {
                case 'default':
                    $seopress_schemas_mapping_case['Manual'] = [
                        'manual_global' => __('Manual text', 'wp-seopress-pro'),
                        'manual_single' => __('Manual text on each post', 'wp-seopress-pro'),
                    ];

                    $post_meta_value = get_post_meta($post->ID, '_' . $post_meta_name . '_manual_global', true);

                    $seopress_schemas_manual_global = '<input type="text" id="' . $post_meta_name . '_manual_global" name="' . $post_meta_name . '_manual_global" class="manual_global" placeholder="' . esc_html__('Enter a global value here', 'wp-seopress-pro') . '" aria-label="' . __('Manual value', 'wp-seopress-pro') . '" value="' . $post_meta_value . '" />';

                    break;
                case 'lb':
                    $seopress_schemas_mapping_case['Manual'] = [
                        'manual_global' => __('Manual text', 'wp-seopress-pro'),
                        'manual_single' => __('Manual text on each post', 'wp-seopress-pro'),
                    ];

                    $post_meta_value = get_post_meta($post->ID, '_' . $post_meta_name . '_manual_global', true);

                    $seopress_schemas_manual_global = '<input type="text" id="' . $post_meta_name . '_manual_global" name="' . $post_meta_name . '_manual_global" class="manual_global" placeholder="' . esc_html__('Enter a global value here', 'wp-seopress-pro') . '" aria-label="' . __('Manual value', 'wp-seopress-pro') . '" value="' . $post_meta_value . '" />';

                    //lb types case
                    $seopress_schemas_mapping_case['Local Business'] = [
                        'manual_lb_global' => __('Local Business type', 'wp-seopress-pro'),
                    ];

                    $post_meta_value = get_post_meta($post->ID, '_' . $post_meta_name . '_manual_lb_global', true);

                    break;
                case 'image':
                        $seopress_schemas_mapping_case = [
                            'Select an option' => ['none' => __('None', 'wp-seopress-pro')],
                            'Site Meta' => [
                                'knowledge_graph_logo' => __('Knowledge Graph logo (SEO > Social)', 'wp-seopress-pro'),
                            ],
                            'Post Meta' => [
                                'post_thumbnail' => __('Featured image / Product image', 'wp-seopress-pro'),
                                'post_author_picture' => __('Post author picture', 'wp-seopress-pro'),
                            ],
                            'Custom fields' => [
                                'custom_fields' => __('Select your custom field', 'wp-seopress-pro'),
                            ],
                            'Manual' => [
                                'manual_img_global' => __('Manual Image URL', 'wp-seopress-pro'),
                                'manual_img_library_global' => __('Manual Image from Library', 'wp-seopress-pro'),
                                'manual_img_single' => __('Manual text on each post', 'wp-seopress-pro'),
                            ],
                        ];

                        $post_meta_value = get_post_meta($post->ID, '_' . $post_meta_name . '_manual_img_global', true);

                        $seopress_schemas_manual_img_global = '<input type="text" id="' . $post_meta_name . '_manual_img_global" name="' . $post_meta_name . '_manual_img_global" class="manual_img_global" placeholder="' . esc_html__('Enter a global value here', 'wp-seopress-pro') . '" aria-label="' . __('Manual Image URL', 'wp-seopress-pro') . '" value="' . $post_meta_value . '" />';

                        $post_meta_value = get_post_meta($post->ID, '_' . $post_meta_name . '_manual_img_library_global', true);
                        $post_meta_value2 = get_post_meta($post->ID, '_' . $post_meta_name . '_manual_img_library_global_width', true);
                        $post_meta_value3 = get_post_meta($post->ID, '_' . $post_meta_name . '_manual_img_library_global_height', true);

                        $seopress_schemas_manual_img_library_global = '<input type="text" id="' . $post_meta_name . '_manual_img_library_global" name="' . $post_meta_name . '_manual_img_library_global" class="manual_img_library_global" placeholder="' . esc_html__('Select your global image from the media library', 'wp-seopress-pro') . '" aria-label="' . __('Manual Image URL', 'wp-seopress-pro') . '" value="' . $post_meta_value . '" />

						<input id="' . $post_meta_name . '_manual_img_library_global_width" type="hidden" name="' . $post_meta_name . '_manual_img_library_global_width" class="manual_img_library_global_width" value="' . $post_meta_value2 . '" />

						<input id="' . $post_meta_name . '_manual_img_library_global_height" type="hidden" name="' . $post_meta_name . '_manual_img_library_global_height" class="manual_img_library_global_height" value="' . $post_meta_value3 . '" />

						<input id="' . $post_meta_name . '_manual_img_library_global_btn" class="btn btnSecondary manual_img_library_global" type="button" value="' . __('Upload an Image', 'wp-seopress-pro') . '" />';

                    break;
                case 'events':
                        //Events Calendar
                        if (is_plugin_active('the-events-calendar/the-events-calendar.php')) {
                            $seopress_schemas_mapping_case['Events Calendar'] = [
                                'events_start_date' => __('Start date', 'wp-seopress-pro'),
                                'events_start_date_timezone' => __('Timezone start date', 'wp-seopress-pro'),
                                'events_start_time' => __('Start time', 'wp-seopress-pro'),
                                'events_end_date' => __('End date', 'wp-seopress-pro'),
                                'events_end_time' => __('End time', 'wp-seopress-pro'),
                                'events_location_name' => __('Event location name', 'wp-seopress-pro'),
                                'events_location_address' => __('Event location address', 'wp-seopress-pro'),
                                'events_website' => __('Event website', 'wp-seopress-pro'),
                                'events_cost' => __('Event cost', 'wp-seopress-pro'),
                                'events_currency' => __('Event currency', 'wp-seopress-pro'),
                            ];
                        }

                    break;
                case 'date':
                        //date case
                        $seopress_schemas_mapping_case['Manual'] = [
                            'manual_date_global' => __('Manual date', 'wp-seopress-pro'),
                            'manual_date_single' => __('Manual date on each post', 'wp-seopress-pro'),
                        ];

                        $post_meta_value = get_post_meta($post->ID, '_' . $post_meta_name . '_manual_date_global', true);

                        $seopress_schemas_manual_date_global = '<input type="text" class="seopress-date-picker manual_date_global" autocomplete="false" name="' . $post_meta_name . '_manual_date_global" class="manual_global" placeholder="' . esc_html__('e.g. YYYY-MM-DD', 'wp-seopress-pro') . '" aria-label="' . __('Global date', 'wp-seopress-pro') . '" value="' . $post_meta_value . '" />';
                    break;
                case 'time':
                        //time case
                        $seopress_schemas_mapping_case['Manual'] = [
                            'manual_time_global' => __('Manual time', 'wp-seopress-pro'),
                            'manual_time_single' => __('Manual time on each post', 'wp-seopress-pro'),
                        ];

                        $post_meta_value = get_post_meta($post->ID, '_' . $post_meta_name . '_manual_time_global', true);

                        $seopress_schemas_manual_time_global = '<input type="time" step="2" placeholder="' . __('HH:MM', 'wp-seopress-pro') . '" id="' . $post_meta_name . '_manual_time_global" name="' . $post_meta_name . '_manual_time_global" class="manual_time_global" aria-label="' . __('Time', 'wp-seopress-pro') . '" value="' . $post_meta_value . '" />';
                    break;
                case 'rating':
                        //rating case
                        $seopress_schemas_mapping_case['Manual'] = [
                            'manual_rating_global' => __('Manual rating', 'wp-seopress-pro'),
                            'manual_rating_single' => __('Manual rating on each post', 'wp-seopress-pro'),
                        ];

                        $post_meta_value = get_post_meta($post->ID, '_' . $post_meta_name . '_manual_rating_global', true);

                        $seopress_schemas_manual_rating_global = '<input type="number" id="' . $post_meta_name . '_manual_rating_global" name="' . $post_meta_name . '_manual_rating_global" min="1" step="0.1" class="manual_rating_global" aria-label="' . __('Rating', 'wp-seopress-pro') . '" value="' . $post_meta_value . '" />';
                    break;
                case 'custom':
                        //custom case
                        $seopress_schemas_mapping_case = [];
                        $seopress_schemas_mapping_case['custom'] = [
                            'manual_custom_global' => __('Manual custom schema', 'wp-seopress-pro'),
                            'manual_custom_single' => __('Manual custom schema on each post', 'wp-seopress-pro'),
                        ];

                        $post_meta_value = get_post_meta($post->ID, '_' . $post_meta_name . '_manual_custom_global', true);

                        $seopress_schemas_manual_custom_global = '<textarea rows="25" id="' . $post_meta_name . '_manual_custom_global" name="' . $post_meta_name . '_manual_custom_global" class="manual_custom_global" aria-label="' . __('Custom schema', 'wp-seopress-pro') . '" value="' . htmlspecialchars($post_meta_value) . '" placeholder="' . esc_html__('e.g. <script type="application/ld+json">{
                            "@context": "https://schema.org/",
                            "@type": "Review",
                            "itemReviewed": {
                            "@type": "Restaurant",
                            "image": "http://www.example.com/seafood-restaurant.jpg",
                            "name": "Legal Seafood",
                            "servesCuisine": "Seafood",
                            "telephone": "1234567",
                            "address" :{
                                "@type": "PostalAddress",
                                "streetAddress": "123 William St",
                                "addressLocality": "New York",
                                "addressRegion": "NY",
                                "postalCode": "10038",
                                "addressCountry": "US"
                            }
                            },
                            "reviewRating": {
                            "@type": "Rating",
                            "ratingValue": "4"
                            },
                            "name": "A good seafood place.",
                            "author": {
                            "@type": "Person",
                            "name": "Bob Smith"
                            },
                            "reviewBody": "The seafood is great.",
                            "publisher": {
                            "@type": "Organization",
                            "name": "Washington Times"
                            }
                        }</script>', 'wp-seopress-pro') . '">' . htmlspecialchars($post_meta_value) . '</textarea>';
                    break;
            }
        }

        $post_meta_value = get_post_meta($post->ID, '_' . $post_meta_name, true);

        $seopress_schemas_mapping_case = apply_filters('seopress_schemas_mapping_select', $seopress_schemas_mapping_case);

        $html = '<select name="' . $post_meta_name . '" class="dyn">';
        foreach ($seopress_schemas_mapping_case as $key => $value) {
            $html .= '<optgroup label="' . $key . '">';
            foreach ($value as $_key => $_value) {
                $html .= '<option ' . selected($_key, $post_meta_value, false) . ' value="' . $_key . '">' . $_value . '</option>';
            }
            $html .= '</optgroup>';
        }
        $html .= '</select>';

        if (isset($seopress_schemas_manual_global)) {
            $html .= $seopress_schemas_manual_global;
        }
        if (isset($seopress_schemas_manual_img_global)) {
            $html .= $seopress_schemas_manual_img_global;
        }
        if (isset($seopress_schemas_manual_img_library_global)) {
            $html .= $seopress_schemas_manual_img_library_global;
        }
        if (isset($seopress_schemas_manual_date_global)) {
            $html .= $seopress_schemas_manual_date_global;
        }
        if (isset($seopress_schemas_manual_time_global)) {
            $html .= $seopress_schemas_manual_time_global;
        }
        if (isset($seopress_schemas_manual_rating_global)) {
            $html .= $seopress_schemas_manual_rating_global;
        }
        if (isset($seopress_schemas_cf) && 'custom' != $case) {
            $html .= $seopress_schemas_cf;
        }
        if (isset($seopress_schemas_tax) && 'custom' != $case) {
            $html .= $seopress_schemas_tax;
        }
        if (isset($seopress_schemas_lb) && 'custom' != $case) {
            $html .= $seopress_schemas_lb;
        }
        if (isset($seopress_schemas_manual_custom_global)) {
            $html .= $seopress_schemas_manual_custom_global;
        }

        return $html;
    }
    $docs = function_exists('seopress_get_docs_links') ? seopress_get_docs_links() : '';

    //Get datas
    $seopress_pro_rich_snippets_type = get_post_meta($post->ID, '_seopress_pro_rich_snippets_type', true);

    //Article
    $seopress_pro_rich_snippets_article_type = get_post_meta($post->ID, '_seopress_pro_rich_snippets_article_type', true);

    //Local Business
    $seopress_pro_rich_snippets_lb_opening_hours = get_post_meta($post->ID, '_seopress_pro_rich_snippets_lb_opening_hours', false); ?>
<tr id="term-seopress" class="form-field">
    <td>
        <div id="seopress_pro_cpt" class="seopress-your-schema">
            <div class="inside">
                <div id="seopress-your-schema">
                    <div class="box-left">
                        <div class="wrap-rich-snippets-type">
                            <p>
                                <strong>
                                    <?php _e('Schema Type', 'wp-seopress-pro'); ?>
                                </strong>
                                <span class="description"><?php _e('Select the type of structured data you want to add to your content', 'wp-seopress-pro'); ?></span>
                            </p>
                            <div class="seopress-schema-type-grid">
                            <?php
                            $schema_types = [
                                [
                                    'value' => 'none',
                                    'label' => __('None', 'wp-seopress-pro'),
                                    'icon'  => 'dashicons-no',
                                    'desc'  => __('No schema will be added.', 'wp-seopress-pro')
                                ],
                                [
                                    'value' => 'articles',
                                    'label' => __('Article (WebPage)', 'wp-seopress-pro'),
                                    'icon'  => 'dashicons-media-text',
                                    'desc'  => __('For blog posts, news, and articles.', 'wp-seopress-pro')
                                ],
                                [
                                    'value' => 'localbusiness',
                                    'label' => __('Local Business', 'wp-seopress-pro'),
                                    'icon'  => 'dashicons-store',
                                    'desc'  => __('For local businesses and organizations.', 'wp-seopress-pro')
                                ],
                                [
                                    'value' => 'faq',
                                    'label' => __('FAQ', 'wp-seopress-pro'),
                                    'icon'  => 'dashicons-editor-help',
                                    'desc'  => __('For frequently asked questions.', 'wp-seopress-pro')
                                ],
                                [
                                    'value' => 'courses',
                                    'label' => __('Course', 'wp-seopress-pro'),
                                    'icon'  => 'dashicons-welcome-learn-more',
                                    'desc'  => __('For educational courses.', 'wp-seopress-pro')
                                ],
                                [
                                    'value' => 'recipes',
                                    'label' => __('Recipe', 'wp-seopress-pro'),
                                    'icon'  => 'dashicons-carrot',
                                    'desc'  => __('For food and recipe content.', 'wp-seopress-pro')
                                ],
                                [
                                    'value' => 'jobs',
                                    'label' => __('Job', 'wp-seopress-pro'),
                                    'icon'  => 'dashicons-businessman',
                                    'desc'  => __('For job postings.', 'wp-seopress-pro')
                                ],
                                [
                                    'value' => 'videos',
                                    'label' => __('Video', 'wp-seopress-pro'),
                                    'icon'  => 'dashicons-format-video',
                                    'desc'  => __('For video content.', 'wp-seopress-pro')
                                ],
                                [
                                    'value' => 'events',
                                    'label' => __('Event', 'wp-seopress-pro'),
                                    'icon'  => 'dashicons-calendar-alt',
                                    'desc'  => __('For events and happenings.', 'wp-seopress-pro')
                                ],
                                [
                                    'value' => 'products',
                                    'label' => __('Product', 'wp-seopress-pro'),
                                    'icon'  => 'dashicons-cart',
                                    'desc'  => __('For products and e-commerce.', 'wp-seopress-pro')
                                ],
                                [
                                    'value' => 'services',
                                    'label' => __('Service', 'wp-seopress-pro'),
                                    'icon'  => 'dashicons-hammer',
                                    'desc'  => __('For service offerings.', 'wp-seopress-pro')
                                ],
                                [
                                    'value' => 'softwareapp',
                                    'label' => __('Software Application', 'wp-seopress-pro'),
                                    'icon'  => 'dashicons-admin-generic',
                                    'desc'  => __('For software and apps.', 'wp-seopress-pro')
                                ],
                                [
                                    'value' => 'review',
                                    'label' => __('Review', 'wp-seopress-pro'),
                                    'icon'  => 'dashicons-star-filled',
                                    'desc'  => __('For reviews and ratings.', 'wp-seopress-pro')
                                ],
                                [
                                    'value' => 'custom',
                                    'label' => __('Custom', 'wp-seopress-pro'),
                                    'icon'  => 'dashicons-editor-code',
                                    'desc'  => __('Add your own custom schema.', 'wp-seopress-pro')
                                ],
                            ];
                            $selected = isset($seopress_pro_rich_snippets_type) ? $seopress_pro_rich_snippets_type : 'none';
                            foreach ($schema_types as $type) : ?>
                                <label class="seopress-schema-card<?php if ($selected == $type['value']) echo ' selected'; ?>">
                                    <input type="radio" name="seopress_pro_rich_snippets_type" value="<?php echo esc_attr($type['value']); ?>" <?php checked($selected, $type['value']); ?> />
                                    <span class="dashicons <?php echo esc_attr($type['icon']); ?>"></span>
                                    <div class="schema-content">
                                        <span class="schema-title"><?php echo esc_html($type['label']); ?></span>
                                        <span class="schema-desc"><?php echo esc_html($type['desc']); ?></span>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="wrap-rich-snippets-rules schema-steps">
                            <p>
                                <label for="seopress_pro_rich_snippets_rules_meta"><?php _e('Show this schema if your singular post, page or post type has:', 'wp-seopress-pro'); ?></label>
                                <?php
    $_id_name_for = 'seopress_pro_rich_snippets_rules';
    $snippets_rules = get_post_meta($post->ID, '_seopress_pro_rich_snippets_rules', true);
    $_available_rules_filters = seopress_get_schemas_filters();
    $_available_rules_conditions = seopress_get_schemas_conditions();
    // Retrocompat < 3.8.2
    if ( ! is_array($snippets_rules) || empty($snippets_rules)) {
        $snippets_rules = seopress_get_default_schemas_rules($snippets_rules);
    }
    $_g = 0;
    foreach ($snippets_rules as $_group => $_rules) {
        $_group = $_g++;
        $_n = 0;
        echo '<div data-group="' . $_group . '">';
        foreach ($_rules as $_index => $_rule) {
            $_index = $_n++;

            echo '<p data-group="' . $_group . '">';

            // Filters
            echo "\t<select id=\"{$_id_name_for}[g{$_group}][i{$_index}][filter]\" name=\"{$_id_name_for}[g{$_group}][i{$_index}][filter]\" class=\"small-text\">\n";
            foreach ($_available_rules_filters as $_filter => $_filter_label) {
                echo "\t\t" . '<option value="' . $_filter . '" ' . selected($_rule['filter'], $_filter, false) . '>' . $_filter_label . '</option>' . "\n";
            }
            echo '</select>';

            // Condition.
            echo "\t<select id=\"{$_id_name_for}[g{$_group}][i{$_index}][cond]\" name=\"{$_id_name_for}[g{$_group}][i{$_index}][cond]\" class=\"small-text\">\n";
            foreach ($_available_rules_conditions as $_cond => $_cond_label) {
                echo "\t\t" . '<option value="' . $_cond . '" ' . selected($_rule['cond'], $_cond, false) . '>' . $_cond_label . '</option>' . "\n";
            }
            echo '</select>';

            // CPT
            $class = 'post_type' === $_rule['filter'] ? '' : 'hidden';
            echo "\t<select id=\"{$_id_name_for}[g{$_group}][i{$_index}][cpt]\" name=\"{$_id_name_for}[g{$_group}][i{$_index}][cpt]\" class=\"{$class}\">\n";
            $postTypes = seopress_get_service('WordPressData')->getPostTypes();
            foreach ($postTypes as $_cpt_slug => $_post_type_obj) {
                echo "\t\t" . '<option ' . selected($_rule['cpt'], $_cpt_slug, false) . ' value="' . $_cpt_slug . '">' . $_post_type_obj->labels->name . '</option>' . "\n";
            }
            echo '</select>';

            // TAXO
            $class = 'taxonomy' === $_rule['filter'] ? '' : 'hidden';
            echo "\t<select id=\"{$_id_name_for}[g{$_group}][i{$_index}][taxo]\" name=\"{$_id_name_for}[g{$_group}][i{$_index}][taxo]\" class=\"{$class}\">\n";
            foreach (seopress_get_service('WordPressData')->getTaxonomies(true) as $_tax_slug => $_tax) {
                echo "\t\t" . '<optgroup label="' . $_tax->label . '">' . "\n";
                if (isset($_tax->terms)) { // Free version is up to date.
                    foreach ($_tax->terms as $_term) {
                        echo "\t\t" . '<option ' . selected($_rule['taxo'], $_term->term_id, false) . ' value="' . $_term->term_id . '">' . esc_html($_term->name) . '</option>' . "\n";
                    }
                }
                echo '</optgroup>';
            }
            echo '</select>';

            // INPUT
            $class = 'postId' === $_rule['filter'] ? '' : 'hidden';
            $valuePostId = isset($_rule['postId']) ? $_rule['postId'] : '';
            echo "\t<input type=\"text\" id=\"{$_id_name_for}[g{$_group}][i{$_index}][postId]\" name=\"{$_id_name_for}[g{$_group}][i{$_index}][postId]\" class=\"{$class}\" value=\"{$valuePostId}\" />\n";

            // Buttons
            echo ' <span class="dashicons dashicons-plus-alt ' . $_id_name_for . '_and" data-group="' . $_group . '"></span>';
            echo ' <span class="hidden dashicons dashicons-no-alt ' . $_id_name_for . '_del" data-group="' . $_group . '"></span>';

            echo '</p>';
        }
        echo '</div>';
        echo '<p class="separat_or"><strong>' . __('or', 'wp-seopress-pro') . '</strong></p>';
    } ?>
                            <p>
                                <button type="button" class="button button-secondary"
                                    id="<?php echo $_id_name_for; ?>_add">
                                    <?php _e('Add a rule', 'wp-seopress-pro'); ?>
                                </button>
                            </p>
                        </div>
                        <p>
                            <label><?php _e('Map all schema properties to a value:', 'wp-seopress-pro'); ?></label>
                        </p>

                        <?php
                            require_once dirname(__FILE__) . '/automatic/Article.php';
    require_once dirname(__FILE__) . '/automatic/LocalBusiness.php';
    require_once dirname(__FILE__) . '/automatic/Faq.php';
    require_once dirname(__FILE__) . '/automatic/Course.php';
    require_once dirname(__FILE__) . '/automatic/Recipe.php';
    require_once dirname(__FILE__) . '/automatic/Job.php';
    require_once dirname(__FILE__) . '/automatic/Video.php';
    require_once dirname(__FILE__) . '/automatic/Event.php';
    require_once dirname(__FILE__) . '/automatic/Product.php';
    require_once dirname(__FILE__) . '/automatic/SoftwareApp.php';
    require_once dirname(__FILE__) . '/automatic/Service.php';
    require_once dirname(__FILE__) . '/automatic/Review.php';
    require_once dirname(__FILE__) . '/automatic/Custom.php'; ?>
                    </div>
                    <div class="seopress-notice seopress-help">
                        <h3><?php _e('Common issues','wp-seopress-pro'); ?></h3>
                        <p>
                            <?php
                                /* translators: %s documentation URL */
                                printf(__('How to add your own <a href="%s" target="_blank">predefined dynamic variables</a>.','wp-seopress-pro'), $docs['schemas']['variables']);
                            ?>
                            <span class="seopress-help dashicons dashicons-external"></span>
                        </p>
                        <hr>
                        <p>
                            <?php
                                /* translators: %s documentation URL */
                                printf(__('I don’t see <a href="%s" target="_blank">all my custom fields from the list</a>!','wp-seopress-pro'), $docs['schemas']['custom_fields']);
                            ?>
                            <span class="seopress-help dashicons dashicons-external"></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>

<?php

    global $pagenow;

    if (isset($pagenow) && $pagenow === 'post-new.php'):

?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const $ = jQuery

        $("#seopress_pro_rich_snippets_type").on("change", function(e) {
            const val = $(this).val()
            if (!val === "products") {
                return;
            }

            $("select[name='seopress_pro_rich_snippets_product_name']").val("post_title")
            $("select[name='seopress_pro_rich_snippets_product_description']").val("post_excerpt")
            $("select[name='seopress_pro_rich_snippets_product_img']").val("post_thumbnail")
            $("select[name='seopress_pro_rich_snippets_product_price']").val(
                "product_regular_price")
            $("select[name='seopress_pro_rich_snippets_product_sku']").val("product_sku")
            $("select[name='seopress_pro_rich_snippets_product_price_valid_date']").val(
                "product_date_to")
            $("select[name='seopress_pro_rich_snippets_product_global_ids']").val(
                "product_barcode_type")
            $("select[name='seopress_pro_rich_snippets_product_global_ids_value']").val(
                "product_barcode")
            $("select[name='seopress_pro_rich_snippets_product_availability']").val("product_stock")
        })
    })
</script>
<?php
    endif;
}

function seopress_save_inputs_schema_automatic($inputs, $postId){
    foreach($inputs as $key => $item) {
        if(isset($_POST[$key]) && !empty($_POST[$key])){
            if ($item['key'] === '_seopress_pro_rich_snippets_lb_opening_hours') {
                update_post_meta($postId, $item['key'], $_POST[$key]);
            } elseif ($_POST[$key] =='none') {
                delete_post_meta($postId, $item['key']);
            } else {
                update_post_meta($postId, $item['key'], esc_html($_POST[$key]));
            }
        } elseif (empty($_POST[$key])) {
            delete_post_meta($postId, $item['key']);
        }
    }
}

add_action('save_post', 'seopress_schemas_save_metabox', 10, 2);
function seopress_schemas_save_metabox($post_id, $post) {
    //Nonce
    if ( ! isset($_POST['seopress_schemas_cpt_nonce']) || ! wp_verify_nonce(
        $_POST['seopress_schemas_cpt_nonce'],
        plugin_basename(__FILE__)
    )) {
        return $post_id;
    }

    //Post type object
    $post_type = get_post_type_object($post->post_type);

    //Check permission
    if ( ! current_user_can('edit_schemas', $post_id)) {
        return $post_id;
    }

    if (isset($_POST['seopress_pro_rich_snippets_rules'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_rules', $_POST['seopress_pro_rich_snippets_rules']);
    }
    if (isset($_POST['seopress_pro_rich_snippets_type'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_type', esc_html($_POST['seopress_pro_rich_snippets_type']));
    }

    //Article
    $inputsArticle = [
        'seopress_pro_rich_snippets_article_type' => [
            'key' => '_seopress_pro_rich_snippets_article_type',
        ],
        'seopress_pro_rich_snippets_article_title' => [
            'key' => '_seopress_pro_rich_snippets_article_title',
        ],
        'seopress_pro_rich_snippets_article_title_cf' => [
            'key' => '_seopress_pro_rich_snippets_article_title_cf',
        ],
        'seopress_pro_rich_snippets_article_title_tax' => [
            'key' => '_seopress_pro_rich_snippets_article_title_tax',
        ],
        'seopress_pro_rich_snippets_article_title_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_article_title_manual_global',
        ],
        'seopress_pro_rich_snippets_article_desc' => [
            'key' => '_seopress_pro_rich_snippets_article_desc',
        ],
        'seopress_pro_rich_snippets_article_desc_cf' => [
            'key' => '_seopress_pro_rich_snippets_article_desc_cf',
        ],
        'seopress_pro_rich_snippets_article_desc_tax' => [
            'key' => '_seopress_pro_rich_snippets_article_desc_tax',
        ],
        'seopress_pro_rich_snippets_article_desc_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_article_desc_manual_global',
        ],
        'seopress_pro_rich_snippets_article_author' => [
            'key' => '_seopress_pro_rich_snippets_article_author',
        ],
        'seopress_pro_rich_snippets_article_author_cf' => [
            'key' => '_seopress_pro_rich_snippets_article_author_cf',
        ],
        'seopress_pro_rich_snippets_article_author_tax' => [
            'key' => '_seopress_pro_rich_snippets_article_author_tax',
        ],
        'seopress_pro_rich_snippets_article_author_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_article_author_manual_global',
        ],
        'seopress_pro_rich_snippets_article_img' => [
            'key' => '_seopress_pro_rich_snippets_article_img',
        ],
        'seopress_pro_rich_snippets_article_img_manual_img_global' => [
            'key' => '_seopress_pro_rich_snippets_article_img_manual_img_global',
        ],
        'seopress_pro_rich_snippets_article_img_cf' => [
            'key' => '_seopress_pro_rich_snippets_article_img_cf',
        ],
        'seopress_pro_rich_snippets_article_img_tax' => [
            'key' => '_seopress_pro_rich_snippets_article_img_tax',
        ],
        'seopress_pro_rich_snippets_article_img_manual_img_library_global' => [
            'key' => '_seopress_pro_rich_snippets_article_img_manual_img_library_global',
        ],
        'seopress_pro_rich_snippets_article_img_manual_img_library_global_width' => [
            'key' => '_seopress_pro_rich_snippets_article_img_manual_img_library_global_width',
        ],
        'seopress_pro_rich_snippets_article_img_manual_img_library_global_height' => [
            'key' => '_seopress_pro_rich_snippets_article_img_manual_img_library_global_height',
        ],
        'seopress_pro_rich_snippets_article_coverage_start_date' => [
            'key' => '_seopress_pro_rich_snippets_article_coverage_start_date',
        ],
        'seopress_pro_rich_snippets_article_coverage_start_date_cf' => [
            'key' => '_seopress_pro_rich_snippets_article_coverage_start_date_cf',
        ],
        'seopress_pro_rich_snippets_article_coverage_start_date_tax' => [
            'key' => '_seopress_pro_rich_snippets_article_coverage_start_date_tax',
        ],
        'seopress_pro_rich_snippets_article_coverage_start_date_manual_date_global' => [
            'key' => '_seopress_pro_rich_snippets_article_coverage_start_date_manual_date_global',
        ],
        'seopress_pro_rich_snippets_article_coverage_start_time' => [
            'key' => '_seopress_pro_rich_snippets_article_coverage_start_time',
        ],
        'seopress_pro_rich_snippets_article_coverage_start_time_cf' => [
            'key' => '_seopress_pro_rich_snippets_article_coverage_start_time_cf',
        ],
        'seopress_pro_rich_snippets_article_coverage_start_time_tax' => [
            'key' => '_seopress_pro_rich_snippets_article_coverage_start_time_tax',
        ],
        'seopress_pro_rich_snippets_article_coverage_start_time_manual_time_global' => [
            'key' => '_seopress_pro_rich_snippets_article_coverage_start_time_manual_time_global',
        ],
        'seopress_pro_rich_snippets_article_coverage_end_date' => [
            'key' => '_seopress_pro_rich_snippets_article_coverage_end_date',
        ],
        'seopress_pro_rich_snippets_article_coverage_end_date_cf' => [
            'key' => '_seopress_pro_rich_snippets_article_coverage_end_date_cf',
        ],
        'seopress_pro_rich_snippets_article_coverage_end_date_tax' => [
            'key' => '_seopress_pro_rich_snippets_article_coverage_end_date_tax',
        ],
        'seopress_pro_rich_snippets_article_coverage_end_date_manual_date_global' => [
            'key' => '_seopress_pro_rich_snippets_article_coverage_end_date_manual_date_global',
        ],
        'seopress_pro_rich_snippets_article_coverage_end_time' => [
            'key' => '_seopress_pro_rich_snippets_article_coverage_end_time',
        ],
        'seopress_pro_rich_snippets_article_coverage_end_time_cf' => [
            'key' => '_seopress_pro_rich_snippets_article_coverage_end_time_cf',
        ],
        'seopress_pro_rich_snippets_article_coverage_end_time_tax' => [
            'key' => '_seopress_pro_rich_snippets_article_coverage_end_time_tax',
        ],
        'seopress_pro_rich_snippets_article_coverage_end_time_manual_time_global' => [
            'key' => '_seopress_pro_rich_snippets_article_coverage_end_time_manual_time_global',
        ],
        'seopress_pro_rich_snippets_article_speakable' => [
            'key' => '_seopress_pro_rich_snippets_article_speakable',
        ],
        'seopress_pro_rich_snippets_article_speakable_cf' => [
            'key' => '_seopress_pro_rich_snippets_article_speakable_cf',
        ],
        'seopress_pro_rich_snippets_article_speakable_tax' => [
            'key' => '_seopress_pro_rich_snippets_article_speakable_tax',
        ],
        'seopress_pro_rich_snippets_article_speakable_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_article_speakable_manual_global',
        ]
    ];

    seopress_save_inputs_schema_automatic($inputsArticle, $post_id);

    //Local Business
    $inputsLocalBusiness = [
        'seopress_pro_rich_snippets_lb_name' => [
            'key' => '_seopress_pro_rich_snippets_lb_name'
        ],
        'seopress_pro_rich_snippets_lb_name_cf' => [
            'key' => '_seopress_pro_rich_snippets_lb_name_cf'
        ],
        'seopress_pro_rich_snippets_lb_name_tax' => [
            'key' => '_seopress_pro_rich_snippets_lb_name_tax'
        ],
        'seopress_pro_rich_snippets_lb_name_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_lb_name_manual_global'
        ],
        'seopress_pro_rich_snippets_lb_type' => [
            'key' => '_seopress_pro_rich_snippets_lb_type'
        ],
        'seopress_pro_rich_snippets_lb_type_cf' => [
            'key' => '_seopress_pro_rich_snippets_lb_type_cf'
        ],
        'seopress_pro_rich_snippets_lb_type_tax' => [
            'key' => '_seopress_pro_rich_snippets_lb_type_tax'
        ],
        'seopress_pro_rich_snippets_lb_type_lb' => [
            'key' => '_seopress_pro_rich_snippets_lb_type_lb'
        ],
        'seopress_pro_rich_snippets_lb_type_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_lb_type_manual_global'
        ],
        'seopress_pro_rich_snippets_lb_img' => [
            'key' => '_seopress_pro_rich_snippets_lb_img'
        ],
        'seopress_pro_rich_snippets_lb_img_manual_img_global' => [
            'key' => '_seopress_pro_rich_snippets_lb_img_manual_img_global'
        ],
        'seopress_pro_rich_snippets_lb_img_cf' => [
            'key' => '_seopress_pro_rich_snippets_lb_img_cf'
        ],
        'seopress_pro_rich_snippets_lb_img_tax' => [
            'key' => '_seopress_pro_rich_snippets_lb_img_tax'
        ],
        'seopress_pro_rich_snippets_lb_img_manual_img_library_global' => [
            'key' => '_seopress_pro_rich_snippets_lb_img_manual_img_library_global'
        ],
        'seopress_pro_rich_snippets_lb_img_manual_img_library_global_width' => [
            'key' => '_seopress_pro_rich_snippets_lb_img_manual_img_library_global_width'
        ],
        'seopress_pro_rich_snippets_lb_img_manual_img_library_global_height' => [
            'key' => '_seopress_pro_rich_snippets_lb_img_manual_img_library_global_height'
        ],
        'seopress_pro_rich_snippets_lb_street_addr' => [
            'key' => '_seopress_pro_rich_snippets_lb_street_addr'
        ],
        'seopress_pro_rich_snippets_lb_street_addr_cf' => [
            'key' => '_seopress_pro_rich_snippets_lb_street_addr_cf'
        ],
        'seopress_pro_rich_snippets_lb_street_addr_tax' => [
            'key' => '_seopress_pro_rich_snippets_lb_street_addr_tax'
        ],
        'seopress_pro_rich_snippets_lb_street_addr_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_lb_street_addr_manual_global'
        ],
        'seopress_pro_rich_snippets_lb_city' => [
            'key' => '_seopress_pro_rich_snippets_lb_city'
        ],
        'seopress_pro_rich_snippets_lb_city_cf' => [
            'key' => '_seopress_pro_rich_snippets_lb_city_cf'
        ],
        'seopress_pro_rich_snippets_lb_city_tax' => [
            'key' => '_seopress_pro_rich_snippets_lb_city_tax'
        ],
        'seopress_pro_rich_snippets_lb_city_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_lb_city_manual_global'
        ],
        'seopress_pro_rich_snippets_lb_state' => [
            'key' => '_seopress_pro_rich_snippets_lb_state'
        ],
        'seopress_pro_rich_snippets_lb_state_cf' => [
            'key' => '_seopress_pro_rich_snippets_lb_state_cf'
        ],
        'seopress_pro_rich_snippets_lb_state_tax' => [
            'key' => '_seopress_pro_rich_snippets_lb_state_tax'
        ],
        'seopress_pro_rich_snippets_lb_state_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_lb_state_manual_global'
        ],
        'seopress_pro_rich_snippets_lb_pc' => [
            'key' => '_seopress_pro_rich_snippets_lb_pc'
        ],
        'seopress_pro_rich_snippets_lb_pc_cf' => [
            'key' => '_seopress_pro_rich_snippets_lb_pc_cf'
        ],
        'seopress_pro_rich_snippets_lb_pc_tax' => [
            'key' => '_seopress_pro_rich_snippets_lb_pc_tax'
        ],
        'seopress_pro_rich_snippets_lb_pc_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_lb_pc_manual_global'
        ],
        'seopress_pro_rich_snippets_lb_country' => [
            'key' => '_seopress_pro_rich_snippets_lb_country'
        ],
        'seopress_pro_rich_snippets_lb_country_cf' => [
            'key' => '_seopress_pro_rich_snippets_lb_country_cf'
        ],
        'seopress_pro_rich_snippets_lb_country_tax' => [
            'key' => '_seopress_pro_rich_snippets_lb_country_tax'
        ],
        'seopress_pro_rich_snippets_lb_country_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_lb_country_manual_global'
        ],
        'seopress_pro_rich_snippets_lb_lat' => [
            'key' => '_seopress_pro_rich_snippets_lb_lat'
        ],
        'seopress_pro_rich_snippets_lb_lat_cf' => [
            'key' => '_seopress_pro_rich_snippets_lb_lat_cf'
        ],
        'seopress_pro_rich_snippets_lb_lat_tax' => [
            'key' => '_seopress_pro_rich_snippets_lb_lat_tax'
        ],
        'seopress_pro_rich_snippets_lb_lat_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_lb_lat_manual_global'
        ],
        'seopress_pro_rich_snippets_lb_lon' => [
            'key' => '_seopress_pro_rich_snippets_lb_lon'
        ],
        'seopress_pro_rich_snippets_lb_lon_cf' => [
            'key' => '_seopress_pro_rich_snippets_lb_lon_cf'
        ],
        'seopress_pro_rich_snippets_lb_lon_tax' => [
            'key' => '_seopress_pro_rich_snippets_lb_lon_tax'
        ],
        'seopress_pro_rich_snippets_lb_lon_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_lb_lon_manual_global'
        ],
        'seopress_pro_rich_snippets_lb_website' => [
            'key' => '_seopress_pro_rich_snippets_lb_website'
        ],
        'seopress_pro_rich_snippets_lb_website_cf' => [
            'key' => '_seopress_pro_rich_snippets_lb_website_cf'
        ],
        'seopress_pro_rich_snippets_lb_website_tax' => [
            'key' => '_seopress_pro_rich_snippets_lb_website_tax'
        ],
        'seopress_pro_rich_snippets_lb_website_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_lb_website_manual_global'
        ],
        'seopress_pro_rich_snippets_lb_tel' => [
            'key' => '_seopress_pro_rich_snippets_lb_tel'
        ],
        'seopress_pro_rich_snippets_lb_tel_cf' => [
            'key' => '_seopress_pro_rich_snippets_lb_tel_cf'
        ],
        'seopress_pro_rich_snippets_lb_tel_tax' => [
            'key' => '_seopress_pro_rich_snippets_lb_tel_tax'
        ],
        'seopress_pro_rich_snippets_lb_tel_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_lb_tel_manual_global'
        ],
        'seopress_pro_rich_snippets_lb_price' => [
            'key' => '_seopress_pro_rich_snippets_lb_price'
        ],
        'seopress_pro_rich_snippets_lb_price_cf' => [
            'key' => '_seopress_pro_rich_snippets_lb_price_cf'
        ],
        'seopress_pro_rich_snippets_lb_price_tax' => [
            'key' => '_seopress_pro_rich_snippets_lb_price_tax'
        ],
        'seopress_pro_rich_snippets_lb_price_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_lb_price_manual_global'
        ],
        'seopress_pro_rich_snippets_lb_serves_cuisine' => [
            'key' => '_seopress_pro_rich_snippets_lb_serves_cuisine'
        ],
        'seopress_pro_rich_snippets_lb_serves_cuisine_cf' => [
            'key' => '_seopress_pro_rich_snippets_lb_serves_cuisine_cf'
        ],
        'seopress_pro_rich_snippets_lb_serves_cuisine_tax' => [
            'key' => '_seopress_pro_rich_snippets_lb_serves_cuisine_tax'
        ],
        'seopress_pro_rich_snippets_lb_serves_cuisine_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_lb_serves_cuisine_manual_global'
        ],
        'seopress_pro_rich_snippets_lb_menu' => [
            'key' => '_seopress_pro_rich_snippets_lb_menu'
        ],
        'seopress_pro_rich_snippets_lb_menu_cf' => [
            'key' => '_seopress_pro_rich_snippets_lb_menu_cf'
        ],
        'seopress_pro_rich_snippets_lb_menu_tax' => [
            'key' => '_seopress_pro_rich_snippets_lb_menu_tax'
        ],
        'seopress_pro_rich_snippets_lb_menu_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_lb_menu_manual_global'
        ],
        'seopress_pro_rich_snippets_lb_accepts_reservations' => [
            'key' => '_seopress_pro_rich_snippets_lb_accepts_reservations'
        ],
        'seopress_pro_rich_snippets_lb_accepts_reservations_cf' => [
            'key' => '_seopress_pro_rich_snippets_lb_accepts_reservations_cf'
        ],
        'seopress_pro_rich_snippets_lb_accepts_reservations_tax' => [
            'key' => '_seopress_pro_rich_snippets_lb_accepts_reservations_tax'
        ],
        'seopress_pro_rich_snippets_lb_accepts_reservations_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_lb_accepts_reservations_manual_global'
        ],
        'seopress_pro_rich_snippets_lb_opening_hours' => [
            'key' => '_seopress_pro_rich_snippets_lb_opening_hours'
        ],
    ];

    seopress_save_inputs_schema_automatic($inputsLocalBusiness, $post_id);

    //FAQ
    $inputsFaq = [
        'seopress_pro_rich_snippets_faq_q' => [
            'key' => '_seopress_pro_rich_snippets_faq_q'
        ],
        'seopress_pro_rich_snippets_faq_q_cf' => [
            'key' => '_seopress_pro_rich_snippets_faq_q_cf'
        ],
        'seopress_pro_rich_snippets_faq_q_tax' => [
            'key' => '_seopress_pro_rich_snippets_faq_q_tax'
        ],
        'seopress_pro_rich_snippets_faq_q_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_faq_q_manual_global'
        ],
        'seopress_pro_rich_snippets_faq_a' => [
            'key' => '_seopress_pro_rich_snippets_faq_a'
        ],
        'seopress_pro_rich_snippets_faq_a_cf' => [
            'key' => '_seopress_pro_rich_snippets_faq_a_cf'
        ],
        'seopress_pro_rich_snippets_faq_a_tax' => [
            'key' => '_seopress_pro_rich_snippets_faq_a_tax'
        ],
        'seopress_pro_rich_snippets_faq_a_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_faq_a_manual_global'
        ],
    ];

    seopress_save_inputs_schema_automatic($inputsFaq, $post_id);

    //Course
    $inputsCourse = [
        'seopress_pro_rich_snippets_courses_title' => [
            'key' => '_seopress_pro_rich_snippets_courses_title'
        ],
        'seopress_pro_rich_snippets_courses_title_cf' => [
            'key' => '_seopress_pro_rich_snippets_courses_title_cf'
        ],
        'seopress_pro_rich_snippets_courses_title_tax' => [
            'key' => '_seopress_pro_rich_snippets_courses_title_tax'
        ],
        'seopress_pro_rich_snippets_courses_title_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_courses_title_manual_global'
        ],
        'seopress_pro_rich_snippets_courses_desc' => [
            'key' => '_seopress_pro_rich_snippets_courses_desc'
        ],
        'seopress_pro_rich_snippets_courses_desc_cf' => [
            'key' => '_seopress_pro_rich_snippets_courses_desc_cf'
        ],
        'seopress_pro_rich_snippets_courses_desc_tax' => [
            'key' => '_seopress_pro_rich_snippets_courses_desc_tax'
        ],
        'seopress_pro_rich_snippets_courses_desc_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_courses_desc_manual_global'
        ],
        'seopress_pro_rich_snippets_courses_school' => [
            'key' => '_seopress_pro_rich_snippets_courses_school'
        ],
        'seopress_pro_rich_snippets_courses_school_cf' => [
            'key' => '_seopress_pro_rich_snippets_courses_school_cf'
        ],
        'seopress_pro_rich_snippets_courses_school_tax' => [
            'key' => '_seopress_pro_rich_snippets_courses_school_tax'
        ],
        'seopress_pro_rich_snippets_courses_school_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_courses_school_manual_global'
        ],
        'seopress_pro_rich_snippets_courses_website' => [
            'key' => '_seopress_pro_rich_snippets_courses_website'
        ],
        'seopress_pro_rich_snippets_courses_website_cf' => [
            'key' => '_seopress_pro_rich_snippets_courses_website_cf'
        ],
        'seopress_pro_rich_snippets_courses_website_tax' => [
            'key' => '_seopress_pro_rich_snippets_courses_website_tax'
        ],
        'seopress_pro_rich_snippets_courses_website_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_courses_website_manual_global'
        ],
        'seopress_pro_rich_snippets_courses_offers' => [
            'key' => '_seopress_pro_rich_snippets_courses_offers'
        ],
        'seopress_pro_rich_snippets_courses_offers_cf' => [
            'key' => '_seopress_pro_rich_snippets_courses_offers_cf'
        ],
        'seopress_pro_rich_snippets_courses_offers_tax' => [
            'key' => '_seopress_pro_rich_snippets_courses_offers_tax'
        ],
        'seopress_pro_rich_snippets_courses_offers_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_courses_offers_manual_global'
        ],
        'seopress_pro_rich_snippets_courses_instances' => [
            'key' => '_seopress_pro_rich_snippets_courses_instances'
        ],
        'seopress_pro_rich_snippets_courses_instances_cf' => [
            'key' => '_seopress_pro_rich_snippets_courses_instances_cf'
        ],
        'seopress_pro_rich_snippets_courses_instances_tax' => [
            'key' => '_seopress_pro_rich_snippets_courses_instances_tax'
        ],
        'seopress_pro_rich_snippets_courses_instances_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_courses_instances_manual_global'
        ],

    ];

    seopress_save_inputs_schema_automatic($inputsCourse, $post_id);

    //Recipe
    $inputsRecipe = [
        'seopress_pro_rich_snippets_recipes_name' => [
            'key' => '_seopress_pro_rich_snippets_recipes_name'
        ],
        'seopress_pro_rich_snippets_recipes_name_cf' => [
            'key' => '_seopress_pro_rich_snippets_recipes_name_cf'
        ],
        'seopress_pro_rich_snippets_recipes_name_tax' => [
            'key' => '_seopress_pro_rich_snippets_recipes_name_tax'
        ],
        'seopress_pro_rich_snippets_recipes_name_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_recipes_name_manual_global'
        ],
        'seopress_pro_rich_snippets_recipes_desc' => [
            'key' => '_seopress_pro_rich_snippets_recipes_desc'
        ],
        'seopress_pro_rich_snippets_recipes_desc_cf' => [
            'key' => '_seopress_pro_rich_snippets_recipes_desc_cf'
        ],
        'seopress_pro_rich_snippets_recipes_desc_tax' => [
            'key' => '_seopress_pro_rich_snippets_recipes_desc_tax'
        ],
        'seopress_pro_rich_snippets_recipes_desc_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_recipes_desc_manual_global'
        ],
        'seopress_pro_rich_snippets_recipes_cat' => [
            'key' => '_seopress_pro_rich_snippets_recipes_cat'
        ],
        'seopress_pro_rich_snippets_recipes_cat_cf' => [
            'key' => '_seopress_pro_rich_snippets_recipes_cat_cf'
        ],
        'seopress_pro_rich_snippets_recipes_cat_tax' => [
            'key' => '_seopress_pro_rich_snippets_recipes_cat_tax'
        ],
        'seopress_pro_rich_snippets_recipes_cat_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_recipes_cat_manual_global'
        ],
        'seopress_pro_rich_snippets_recipes_img' => [
            'key' => '_seopress_pro_rich_snippets_recipes_img'
        ],
        'seopress_pro_rich_snippets_recipes_img_manual_img_global' => [
            'key' => '_seopress_pro_rich_snippets_recipes_img_manual_img_global'
        ],
        'seopress_pro_rich_snippets_recipes_img_cf' => [
            'key' => '_seopress_pro_rich_snippets_recipes_img_cf'
        ],
        'seopress_pro_rich_snippets_recipes_img_tax' => [
            'key' => '_seopress_pro_rich_snippets_recipes_img_tax'
        ],
        'seopress_pro_rich_snippets_recipes_img_manual_img_library_global' => [
            'key' => '_seopress_pro_rich_snippets_recipes_img_manual_img_library_global'
        ],
        'seopress_pro_rich_snippets_recipes_img_manual_img_library_global_width' => [
            'key' => '_seopress_pro_rich_snippets_recipes_img_manual_img_library_global_width'
        ],
        'seopress_pro_rich_snippets_recipes_img_manual_img_library_global_height' => [
            'key' => '_seopress_pro_rich_snippets_recipes_img_manual_img_library_global_height'
        ],
        'seopress_pro_rich_snippets_recipes_video' => [
            'key' => '_seopress_pro_rich_snippets_recipes_video'
        ],
        'seopress_pro_rich_snippets_recipes_video_cf' => [
            'key' => '_seopress_pro_rich_snippets_recipes_video_cf'
        ],
        'seopress_pro_rich_snippets_recipes_video_tax' => [
            'key' => '_seopress_pro_rich_snippets_recipes_video_tax'
        ],
        'seopress_pro_rich_snippets_recipes_video_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_recipes_video_manual_global'
        ],
        'seopress_pro_rich_snippets_recipes_prep_time' => [
            'key' => '_seopress_pro_rich_snippets_recipes_prep_time'
        ],
        'seopress_pro_rich_snippets_recipes_prep_time_cf' => [
            'key' => '_seopress_pro_rich_snippets_recipes_prep_time_cf'
        ],
        'seopress_pro_rich_snippets_recipes_prep_time_tax' => [
            'key' => '_seopress_pro_rich_snippets_recipes_prep_time_tax'
        ],
        'seopress_pro_rich_snippets_recipes_prep_time_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_recipes_prep_time_manual_global'
        ],
        'seopress_pro_rich_snippets_recipes_cook_time' => [
            'key' => '_seopress_pro_rich_snippets_recipes_cook_time'
        ],
        'seopress_pro_rich_snippets_recipes_cook_time_cf' => [
            'key' => '_seopress_pro_rich_snippets_recipes_cook_time_cf'
        ],
        'seopress_pro_rich_snippets_recipes_cook_time_tax' => [
            'key' => '_seopress_pro_rich_snippets_recipes_cook_time_tax'
        ],
        'seopress_pro_rich_snippets_recipes_cook_time_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_recipes_cook_time_manual_global'
        ],
        'seopress_pro_rich_snippets_recipes_calories' => [
            'key' => '_seopress_pro_rich_snippets_recipes_calories'
        ],
        'seopress_pro_rich_snippets_recipes_calories_cf' => [
            'key' => '_seopress_pro_rich_snippets_recipes_calories_cf'
        ],
        'seopress_pro_rich_snippets_recipes_calories_tax' => [
            'key' => '_seopress_pro_rich_snippets_recipes_calories_tax'
        ],
        'seopress_pro_rich_snippets_recipes_calories_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_recipes_calories_manual_global'
        ],
        'seopress_pro_rich_snippets_recipes_yield' => [
            'key' => '_seopress_pro_rich_snippets_recipes_yield'
        ],
        'seopress_pro_rich_snippets_recipes_yield_cf' => [
            'key' => '_seopress_pro_rich_snippets_recipes_yield_cf'
        ],
        'seopress_pro_rich_snippets_recipes_yield_tax' => [
            'key' => '_seopress_pro_rich_snippets_recipes_yield_tax'
        ],
        'seopress_pro_rich_snippets_recipes_yield_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_recipes_yield_manual_global'
        ],
        'seopress_pro_rich_snippets_recipes_keywords' => [
            'key' => '_seopress_pro_rich_snippets_recipes_keywords'
        ],
        'seopress_pro_rich_snippets_recipes_keywords_cf' => [
            'key' => '_seopress_pro_rich_snippets_recipes_keywords_cf'
        ],
        'seopress_pro_rich_snippets_recipes_keywords_tax' => [
            'key' => '_seopress_pro_rich_snippets_recipes_keywords_tax'
        ],
        'seopress_pro_rich_snippets_recipes_keywords_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_recipes_keywords_manual_global'
        ],
        'seopress_pro_rich_snippets_recipes_cuisine' => [
            'key' => '_seopress_pro_rich_snippets_recipes_cuisine'
        ],
        'seopress_pro_rich_snippets_recipes_cuisine_cf' => [
            'key' => '_seopress_pro_rich_snippets_recipes_cuisine_cf'
        ],
        'seopress_pro_rich_snippets_recipes_cuisine_tax' => [
            'key' => '_seopress_pro_rich_snippets_recipes_cuisine_tax'
        ],
        'seopress_pro_rich_snippets_recipes_cuisine_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_recipes_cuisine_manual_global'
        ],
        'seopress_pro_rich_snippets_recipes_ingredient' => [
            'key' => '_seopress_pro_rich_snippets_recipes_ingredient'
        ],
        'seopress_pro_rich_snippets_recipes_ingredient_cf' => [
            'key' => '_seopress_pro_rich_snippets_recipes_ingredient_cf'
        ],
        'seopress_pro_rich_snippets_recipes_ingredient_tax' => [
            'key' => '_seopress_pro_rich_snippets_recipes_ingredient_tax'
        ],
        'seopress_pro_rich_snippets_recipes_ingredient_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_recipes_ingredient_manual_global'
        ],
        'seopress_pro_rich_snippets_recipes_instructions' => [
            'key' => '_seopress_pro_rich_snippets_recipes_instructions'
        ],
        'seopress_pro_rich_snippets_recipes_instructions_cf' => [
            'key' => '_seopress_pro_rich_snippets_recipes_instructions_cf'
        ],
        'seopress_pro_rich_snippets_recipes_instructions_tax' => [
            'key' => '_seopress_pro_rich_snippets_recipes_instructions_tax'
        ],
        'seopress_pro_rich_snippets_recipes_instructions_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_recipes_instructions_manual_global'
        ],
    ];

    seopress_save_inputs_schema_automatic($inputsRecipe, $post_id);

    //Job
    $inputsJob = [
        'seopress_pro_rich_snippets_jobs_name' => [
            'key' => '_seopress_pro_rich_snippets_jobs_name'
        ],
        'seopress_pro_rich_snippets_jobs_name_cf' => [
            'key' => '_seopress_pro_rich_snippets_jobs_name_cf'
        ],
        'seopress_pro_rich_snippets_jobs_name_tax' => [
            'key' => '_seopress_pro_rich_snippets_jobs_name_tax'
        ],
        'seopress_pro_rich_snippets_jobs_name_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_jobs_name_manual_global'
        ],
        'seopress_pro_rich_snippets_jobs_desc' => [
            'key' => '_seopress_pro_rich_snippets_jobs_desc'
        ],
        'seopress_pro_rich_snippets_jobs_desc_cf' => [
            'key' => '_seopress_pro_rich_snippets_jobs_desc_cf'
        ],
        'seopress_pro_rich_snippets_jobs_desc_tax' => [
            'key' => '_seopress_pro_rich_snippets_jobs_desc_tax'
        ],
        'seopress_pro_rich_snippets_jobs_desc_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_jobs_desc_manual_global'
        ],
        'seopress_pro_rich_snippets_jobs_date_posted' => [
            'key' => '_seopress_pro_rich_snippets_jobs_date_posted'
        ],
        'seopress_pro_rich_snippets_jobs_date_posted_cf' => [
            'key' => '_seopress_pro_rich_snippets_jobs_date_posted_cf'
        ],
        'seopress_pro_rich_snippets_jobs_date_posted_tax' => [
            'key' => '_seopress_pro_rich_snippets_jobs_date_posted_tax'
        ],
        'seopress_pro_rich_snippets_jobs_date_posted_manual_date_global' => [
            'key' => '_seopress_pro_rich_snippets_jobs_date_posted_manual_date_global'
        ],
        'seopress_pro_rich_snippets_jobs_valid_through' => [
            'key' => '_seopress_pro_rich_snippets_jobs_valid_through'
        ],
        'seopress_pro_rich_snippets_jobs_valid_through_cf' => [
            'key' => '_seopress_pro_rich_snippets_jobs_valid_through_cf'
        ],
        'seopress_pro_rich_snippets_jobs_valid_through_tax' => [
            'key' => '_seopress_pro_rich_snippets_jobs_valid_through_tax'
        ],
        'seopress_pro_rich_snippets_jobs_valid_through_manual_date_global' => [
            'key' => '_seopress_pro_rich_snippets_jobs_valid_through_manual_date_global'
        ],
        'seopress_pro_rich_snippets_jobs_employment_type' => [
            'key' => '_seopress_pro_rich_snippets_jobs_employment_type'
        ],
        'seopress_pro_rich_snippets_jobs_employment_type_cf' => [
            'key' => '_seopress_pro_rich_snippets_jobs_employment_type_cf'
        ],
        'seopress_pro_rich_snippets_jobs_employment_type_tax' => [
            'key' => '_seopress_pro_rich_snippets_jobs_employment_type_tax'
        ],
        'seopress_pro_rich_snippets_jobs_employment_type_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_jobs_employment_type_manual_global'
        ],
        'seopress_pro_rich_snippets_jobs_identifier_name' => [
            'key' => '_seopress_pro_rich_snippets_jobs_identifier_name'
        ],
        'seopress_pro_rich_snippets_jobs_identifier_name_cf' => [
            'key' => '_seopress_pro_rich_snippets_jobs_identifier_name_cf'
        ],
        'seopress_pro_rich_snippets_jobs_identifier_name_tax' => [
            'key' => '_seopress_pro_rich_snippets_jobs_identifier_name_tax'
        ],
        'seopress_pro_rich_snippets_jobs_identifier_name_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_jobs_identifier_name_manual_global'
        ],
        'seopress_pro_rich_snippets_jobs_identifier_value' => [
            'key' => '_seopress_pro_rich_snippets_jobs_identifier_value'
        ],
        'seopress_pro_rich_snippets_jobs_identifier_value_cf' => [
            'key' => '_seopress_pro_rich_snippets_jobs_identifier_value_cf'
        ],
        'seopress_pro_rich_snippets_jobs_identifier_value_tax' => [
            'key' => '_seopress_pro_rich_snippets_jobs_identifier_value_tax'
        ],
        'seopress_pro_rich_snippets_jobs_identifier_value_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_jobs_identifier_value_manual_global'
        ],
        'seopress_pro_rich_snippets_jobs_hiring_organization' => [
            'key' => '_seopress_pro_rich_snippets_jobs_hiring_organization'
        ],
        'seopress_pro_rich_snippets_jobs_hiring_organization_cf' => [
            'key' => '_seopress_pro_rich_snippets_jobs_hiring_organization_cf'
        ],
        'seopress_pro_rich_snippets_jobs_hiring_organization_tax' => [
            'key' => '_seopress_pro_rich_snippets_jobs_hiring_organization_tax'
        ],
        'seopress_pro_rich_snippets_jobs_hiring_organization_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_jobs_hiring_organization_manual_global'
        ],
        'seopress_pro_rich_snippets_jobs_hiring_same_as' => [
            'key' => '_seopress_pro_rich_snippets_jobs_hiring_same_as'
        ],
        'seopress_pro_rich_snippets_jobs_hiring_same_as_cf' => [
            'key' => '_seopress_pro_rich_snippets_jobs_hiring_same_as_cf'
        ],
        'seopress_pro_rich_snippets_jobs_hiring_same_as_tax' => [
            'key' => '_seopress_pro_rich_snippets_jobs_hiring_same_as_tax'
        ],
        'seopress_pro_rich_snippets_jobs_hiring_same_as_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_jobs_hiring_same_as_manual_global'
        ],
        'seopress_pro_rich_snippets_jobs_hiring_logo' => [
            'key' => '_seopress_pro_rich_snippets_jobs_hiring_logo'
        ],
        'seopress_pro_rich_snippets_jobs_hiring_logo_manual_img_global' => [
            'key' => '_seopress_pro_rich_snippets_jobs_hiring_logo_manual_img_global'
        ],
        'seopress_pro_rich_snippets_jobs_hiring_logo_cf' => [
            'key' => '_seopress_pro_rich_snippets_jobs_hiring_logo_cf'
        ],
        'seopress_pro_rich_snippets_jobs_hiring_logo_tax' => [
            'key' => '_seopress_pro_rich_snippets_jobs_hiring_logo_tax'
        ],
        'seopress_pro_rich_snippets_jobs_hiring_logo_manual_img_library_global' => [
            'key' => '_seopress_pro_rich_snippets_jobs_hiring_logo_manual_img_library_global'
        ],
        'seopress_pro_rich_snippets_jobs_hiring_logo_manual_img_library_global_width' => [
            'key' => '_seopress_pro_rich_snippets_jobs_hiring_logo_manual_img_library_global_width'
        ],
        'seopress_pro_rich_snippets_jobs_hiring_logo_manual_img_library_global_height' => [
            'key' => '_seopress_pro_rich_snippets_jobs_hiring_logo_manual_img_library_global_height'
        ],
        'seopress_pro_rich_snippets_jobs_address_street' => [
            'key' => '_seopress_pro_rich_snippets_jobs_address_street'
        ],
        'seopress_pro_rich_snippets_jobs_address_street_cf' => [
            'key' => '_seopress_pro_rich_snippets_jobs_address_street_cf'
        ],
        'seopress_pro_rich_snippets_jobs_address_street_tax' => [
            'key' => '_seopress_pro_rich_snippets_jobs_address_street_tax'
        ],
        'seopress_pro_rich_snippets_jobs_address_street_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_jobs_address_street_manual_global'
        ],
        'seopress_pro_rich_snippets_jobs_address_locality' => [
            'key' => '_seopress_pro_rich_snippets_jobs_address_locality'
        ],
        'seopress_pro_rich_snippets_jobs_address_locality_cf' => [
            'key' => '_seopress_pro_rich_snippets_jobs_address_locality_cf'
        ],
        'seopress_pro_rich_snippets_jobs_address_locality_tax' => [
            'key' => '_seopress_pro_rich_snippets_jobs_address_locality_tax'
        ],
        'seopress_pro_rich_snippets_jobs_address_locality_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_jobs_address_locality_manual_global'
        ],
        'seopress_pro_rich_snippets_jobs_address_region' => [
            'key' => '_seopress_pro_rich_snippets_jobs_address_region'
        ],
        'seopress_pro_rich_snippets_jobs_address_region_cf' => [
            'key' => '_seopress_pro_rich_snippets_jobs_address_region_cf'
        ],
        'seopress_pro_rich_snippets_jobs_address_region_tax' => [
            'key' => '_seopress_pro_rich_snippets_jobs_address_region_tax'
        ],
        'seopress_pro_rich_snippets_jobs_address_region_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_jobs_address_region_manual_global'
        ],
        'seopress_pro_rich_snippets_jobs_postal_code' => [
            'key' => '_seopress_pro_rich_snippets_jobs_postal_code'
        ],
        'seopress_pro_rich_snippets_jobs_postal_code_cf' => [
            'key' => '_seopress_pro_rich_snippets_jobs_postal_code_cf'
        ],
        'seopress_pro_rich_snippets_jobs_postal_code_tax' => [
            'key' => '_seopress_pro_rich_snippets_jobs_postal_code_tax'
        ],
        'seopress_pro_rich_snippets_jobs_postal_code_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_jobs_postal_code_manual_global'
        ],
        'seopress_pro_rich_snippets_jobs_country' => [
            'key' => '_seopress_pro_rich_snippets_jobs_country'
        ],
        'seopress_pro_rich_snippets_jobs_country_cf' => [
            'key' => '_seopress_pro_rich_snippets_jobs_country_cf'
        ],
        'seopress_pro_rich_snippets_jobs_country_tax' => [
            'key' => '_seopress_pro_rich_snippets_jobs_country_tax'
        ],
        'seopress_pro_rich_snippets_jobs_country_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_jobs_country_manual_global'
        ],
        'seopress_pro_rich_snippets_jobs_remote' => [
            'key' => '_seopress_pro_rich_snippets_jobs_remote'
        ],
        'seopress_pro_rich_snippets_jobs_remote_cf' => [
            'key' => '_seopress_pro_rich_snippets_jobs_remote_cf'
        ],
        'seopress_pro_rich_snippets_jobs_remote_tax' => [
            'key' => '_seopress_pro_rich_snippets_jobs_remote_tax'
        ],
        'seopress_pro_rich_snippets_jobs_remote_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_jobs_remote_manual_global'
        ],
        'seopress_pro_rich_snippets_jobs_direct_apply' => [
            'key' => '_seopress_pro_rich_snippets_jobs_direct_apply'
        ],
        'seopress_pro_rich_snippets_jobs_direct_apply_cf' => [
            'key' => '_seopress_pro_rich_snippets_jobs_direct_apply_cf'
        ],
        'seopress_pro_rich_snippets_jobs_direct_apply_tax' => [
            'key' => '_seopress_pro_rich_snippets_jobs_direct_apply_tax'
        ],
        'seopress_pro_rich_snippets_jobs_direct_apply_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_jobs_direct_apply_manual_global'
        ],
        'seopress_pro_rich_snippets_jobs_salary' => [
            'key' => '_seopress_pro_rich_snippets_jobs_salary'
        ],
        'seopress_pro_rich_snippets_jobs_salary_cf' => [
            'key' => '_seopress_pro_rich_snippets_jobs_salary_cf'
        ],
        'seopress_pro_rich_snippets_jobs_salary_tax' => [
            'key' => '_seopress_pro_rich_snippets_jobs_salary_tax'
        ],
        'seopress_pro_rich_snippets_jobs_salary_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_jobs_salary_manual_global'
        ],
        'seopress_pro_rich_snippets_jobs_salary_currency' => [
            'key' => '_seopress_pro_rich_snippets_jobs_salary_currency'
        ],
        'seopress_pro_rich_snippets_jobs_salary_currency_cf' => [
            'key' => '_seopress_pro_rich_snippets_jobs_salary_currency_cf'
        ],
        'seopress_pro_rich_snippets_jobs_salary_currency_tax' => [
            'key' => '_seopress_pro_rich_snippets_jobs_salary_currency_tax'
        ],
        'seopress_pro_rich_snippets_jobs_salary_currency_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_jobs_salary_currency_manual_global'
        ],
        'seopress_pro_rich_snippets_jobs_salary_unit' => [
            'key' => '_seopress_pro_rich_snippets_jobs_salary_unit'
        ],
        'seopress_pro_rich_snippets_jobs_salary_unit_cf' => [
            'key' => '_seopress_pro_rich_snippets_jobs_salary_unit_cf'
        ],
        'seopress_pro_rich_snippets_jobs_salary_unit_tax' => [
            'key' => '_seopress_pro_rich_snippets_jobs_salary_unit_tax'
        ],
        'seopress_pro_rich_snippets_jobs_salary_unit_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_jobs_salary_unit_manual_global'
        ],
        'seopress_pro_rich_snippets_jobs_location_requirement' => [
            'key' => '_seopress_pro_rich_snippets_jobs_location_requirement'
        ],
        'seopress_pro_rich_snippets_jobs_location_requirement_cf' => [
            'key' => '_seopress_pro_rich_snippets_jobs_location_requirement_cf'
        ],
        'seopress_pro_rich_snippets_jobs_location_requirement_tax' => [
            'key' => '_seopress_pro_rich_snippets_jobs_location_requirement_tax'
        ],
        'seopress_pro_rich_snippets_jobs_location_requirement_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_jobs_location_requirement_manual_global'
        ],
    ];
    seopress_save_inputs_schema_automatic($inputsJob, $post_id);

    //Video
    $inputsVideo = [
        'seopress_pro_rich_snippets_videos_name' => [
            'key' => '_seopress_pro_rich_snippets_videos_name'
        ],
        'seopress_pro_rich_snippets_videos_name_cf' => [
            'key' => '_seopress_pro_rich_snippets_videos_name_cf'
        ],
        'seopress_pro_rich_snippets_videos_name_tax' => [
            'key' => '_seopress_pro_rich_snippets_videos_name_tax'
        ],
        'seopress_pro_rich_snippets_videos_name_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_videos_name_manual_global'
        ],
        'seopress_pro_rich_snippets_videos_description' => [
            'key' => '_seopress_pro_rich_snippets_videos_description'
        ],
        'seopress_pro_rich_snippets_videos_description_cf' => [
            'key' => '_seopress_pro_rich_snippets_videos_description_cf'
        ],
        'seopress_pro_rich_snippets_videos_description_tax' => [
            'key' => '_seopress_pro_rich_snippets_videos_description_tax'
        ],
        'seopress_pro_rich_snippets_videos_description_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_videos_description_manual_global'
        ],
        'seopress_pro_rich_snippets_videos_date_posted' => [
            'key' => '_seopress_pro_rich_snippets_videos_date_posted'
        ],
        'seopress_pro_rich_snippets_videos_date_posted_cf' => [
            'key' => '_seopress_pro_rich_snippets_videos_date_posted_cf'
        ],
        'seopress_pro_rich_snippets_videos_date_posted_tax' => [
            'key' => '_seopress_pro_rich_snippets_videos_date_posted_tax'
        ],
        'seopress_pro_rich_snippets_videos_date_posted_manual_date_global' => [
            'key' => '_seopress_pro_rich_snippets_videos_date_posted_manual_date_global'
        ],
        'seopress_pro_rich_snippets_videos_img' => [
            'key' => '_seopress_pro_rich_snippets_videos_img'
        ],
        'seopress_pro_rich_snippets_videos_img_manual_img_global' => [
            'key' => '_seopress_pro_rich_snippets_videos_img_manual_img_global'
        ],
        'seopress_pro_rich_snippets_videos_img_cf' => [
            'key' => '_seopress_pro_rich_snippets_videos_img_cf'
        ],
        'seopress_pro_rich_snippets_videos_img_tax' => [
            'key' => '_seopress_pro_rich_snippets_videos_img_tax'
        ],
        'seopress_pro_rich_snippets_videos_img_manual_img_library_global' => [
            'key' => '_seopress_pro_rich_snippets_videos_img_manual_img_library_global'
        ],
        'seopress_pro_rich_snippets_videos_img_manual_img_library_global_width' => [
            'key' => '_seopress_pro_rich_snippets_videos_img_manual_img_library_global_width'
        ],
        'seopress_pro_rich_snippets_videos_img_manual_img_library_global_height' => [
            'key' => '_seopress_pro_rich_snippets_videos_img_manual_img_library_global_height'
        ],
        'seopress_pro_rich_snippets_videos_duration' => [
            'key' => '_seopress_pro_rich_snippets_videos_duration'
        ],
        'seopress_pro_rich_snippets_videos_duration_cf' => [
            'key' => '_seopress_pro_rich_snippets_videos_duration_cf'
        ],
        'seopress_pro_rich_snippets_videos_duration_tax' => [
            'key' => '_seopress_pro_rich_snippets_videos_duration_tax'
        ],
        'seopress_pro_rich_snippets_videos_duration_manual_time_global' => [
            'key' => '_seopress_pro_rich_snippets_videos_duration_manual_time_global'
        ],
        'seopress_pro_rich_snippets_videos_url' => [
            'key' => '_seopress_pro_rich_snippets_videos_url'
        ],
        'seopress_pro_rich_snippets_videos_url_cf' => [
            'key' => '_seopress_pro_rich_snippets_videos_url_cf'
        ],
        'seopress_pro_rich_snippets_videos_url_tax' => [
            'key' => '_seopress_pro_rich_snippets_videos_url_tax'
        ],
        'seopress_pro_rich_snippets_videos_url_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_videos_url_manual_global'
        ],
    ];
    seopress_save_inputs_schema_automatic($inputsVideo, $post_id);


    //Event
    $inputsEvent = [
        'seopress_pro_rich_snippets_events_type' => [
            'key' => '_seopress_pro_rich_snippets_events_type'
        ],
        'seopress_pro_rich_snippets_events_type_cf' => [
            'key' => '_seopress_pro_rich_snippets_events_type_cf'
        ],
        'seopress_pro_rich_snippets_events_type_tax' => [
            'key' => '_seopress_pro_rich_snippets_events_type_tax'
        ],
        'seopress_pro_rich_snippets_events_type_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_events_type_manual_global'
        ],
        'seopress_pro_rich_snippets_events_name' => [
            'key' => '_seopress_pro_rich_snippets_events_name'
        ],
        'seopress_pro_rich_snippets_events_name_cf' => [
            'key' => '_seopress_pro_rich_snippets_events_name_cf'
        ],
        'seopress_pro_rich_snippets_events_name_tax' => [
            'key' => '_seopress_pro_rich_snippets_events_name_tax'
        ],
        'seopress_pro_rich_snippets_events_name_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_events_name_manual_global'
        ],
        'seopress_pro_rich_snippets_events_desc' => [
            'key' => '_seopress_pro_rich_snippets_events_desc'
        ],
        'seopress_pro_rich_snippets_events_desc_cf' => [
            'key' => '_seopress_pro_rich_snippets_events_desc_cf'
        ],
        'seopress_pro_rich_snippets_events_desc_tax' => [
            'key' => '_seopress_pro_rich_snippets_events_desc_tax'
        ],
        'seopress_pro_rich_snippets_events_desc_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_events_desc_manual_global'
        ],
        'seopress_pro_rich_snippets_events_img' => [
            'key' => '_seopress_pro_rich_snippets_events_img'
        ],
        'seopress_pro_rich_snippets_events_img_manual_img_global' => [
            'key' => '_seopress_pro_rich_snippets_events_img_manual_img_global'
        ],
        'seopress_pro_rich_snippets_events_img_cf' => [
            'key' => '_seopress_pro_rich_snippets_events_img_cf'
        ],
        'seopress_pro_rich_snippets_events_img_tax' => [
            'key' => '_seopress_pro_rich_snippets_events_img_tax'
        ],
        'seopress_pro_rich_snippets_events_img_manual_img_library_global' => [
            'key' => '_seopress_pro_rich_snippets_events_img_manual_img_library_global'
        ],
        'seopress_pro_rich_snippets_events_img_manual_img_library_global_width' => [
            'key' => '_seopress_pro_rich_snippets_events_img_manual_img_library_global_width'
        ],
        'seopress_pro_rich_snippets_events_img_manual_img_library_global_height' => [
            'key' => '_seopress_pro_rich_snippets_events_img_manual_img_library_global_height'
        ],
        'seopress_pro_rich_snippets_events_desc' => [
            'key' => '_seopress_pro_rich_snippets_events_desc'
        ],
        'seopress_pro_rich_snippets_events_desc_cf' => [
            'key' => '_seopress_pro_rich_snippets_events_desc_cf'
        ],
        'seopress_pro_rich_snippets_events_desc_tax' => [
            'key' => '_seopress_pro_rich_snippets_events_desc_tax'
        ],
        'seopress_pro_rich_snippets_events_desc_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_events_desc_manual_global'
        ],
        'seopress_pro_rich_snippets_events_start_date' => [
            'key' => '_seopress_pro_rich_snippets_events_start_date'
        ],
        'seopress_pro_rich_snippets_events_start_date_cf' => [
            'key' => '_seopress_pro_rich_snippets_events_start_date_cf'
        ],
        'seopress_pro_rich_snippets_events_start_date_tax' => [
            'key' => '_seopress_pro_rich_snippets_events_start_date_tax'
        ],
        'seopress_pro_rich_snippets_events_start_date_manual_date_global' => [
            'key' => '_seopress_pro_rich_snippets_events_start_date_manual_date_global'
        ],
        'seopress_pro_rich_snippets_events_start_date_timezone' => [
            'key' => '_seopress_pro_rich_snippets_events_start_date_timezone'
        ],
        'seopress_pro_rich_snippets_events_start_date_timezone_cf' => [
            'key' => '_seopress_pro_rich_snippets_events_start_date_timezone_cf'
        ],
        'seopress_pro_rich_snippets_events_start_date_timezone_tax' => [
            'key' => '_seopress_pro_rich_snippets_events_start_date_timezone_tax'
        ],
        'seopress_pro_rich_snippets_events_start_date_timezone_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_events_start_date_timezone_manual_global'
        ],
        'seopress_pro_rich_snippets_events_start_time' => [
            'key' => '_seopress_pro_rich_snippets_events_start_time'
        ],
        'seopress_pro_rich_snippets_events_start_time_cf' => [
            'key' => '_seopress_pro_rich_snippets_events_start_time_cf'
        ],
        'seopress_pro_rich_snippets_events_start_time_tax' => [
            'key' => '_seopress_pro_rich_snippets_events_start_time_tax'
        ],
        'seopress_pro_rich_snippets_events_start_time_manual_time_global' => [
            'key' => '_seopress_pro_rich_snippets_events_start_time_manual_time_global'
        ],
        'seopress_pro_rich_snippets_events_end_date' => [
            'key' => '_seopress_pro_rich_snippets_events_end_date'
        ],
        'seopress_pro_rich_snippets_events_end_date_cf' => [
            'key' => '_seopress_pro_rich_snippets_events_end_date_cf'
        ],
        'seopress_pro_rich_snippets_events_end_date_tax' => [
            'key' => '_seopress_pro_rich_snippets_events_end_date_tax'
        ],
        'seopress_pro_rich_snippets_events_end_date_manual_date_global' => [
            'key' => '_seopress_pro_rich_snippets_events_end_date_manual_date_global'
        ],
        'seopress_pro_rich_snippets_events_end_time' => [
            'key' => '_seopress_pro_rich_snippets_events_end_time'
        ],
        'seopress_pro_rich_snippets_events_end_time_cf' => [
            'key' => '_seopress_pro_rich_snippets_events_end_time_cf'
        ],
        'seopress_pro_rich_snippets_events_end_time_tax' => [
            'key' => '_seopress_pro_rich_snippets_events_end_time_tax'
        ],
        'seopress_pro_rich_snippets_events_end_time_manual_time_global' => [
            'key' => '_seopress_pro_rich_snippets_events_end_time_manual_time_global'
        ],
        'seopress_pro_rich_snippets_events_previous_start_date' => [
            'key' => '_seopress_pro_rich_snippets_events_previous_start_date'
        ],
        'seopress_pro_rich_snippets_events_previous_start_date_cf' => [
            'key' => '_seopress_pro_rich_snippets_events_previous_start_date_cf'
        ],
        'seopress_pro_rich_snippets_events_previous_start_date_tax' => [
            'key' => '_seopress_pro_rich_snippets_events_previous_start_date_tax'
        ],
        'seopress_pro_rich_snippets_events_previous_start_date_manual_date_global' => [
            'key' => '_seopress_pro_rich_snippets_events_previous_start_date_manual_date_global'
        ],
        'seopress_pro_rich_snippets_events_previous_start_time' => [
            'key' => '_seopress_pro_rich_snippets_events_previous_start_time'
        ],
        'seopress_pro_rich_snippets_events_previous_start_time_cf' => [
            'key' => '_seopress_pro_rich_snippets_events_previous_start_time_cf'
        ],
        'seopress_pro_rich_snippets_events_previous_start_time_tax' => [
            'key' => '_seopress_pro_rich_snippets_events_previous_start_time_tax'
        ],
        'seopress_pro_rich_snippets_events_previous_start_time_manual_time_global' => [
            'key' => '_seopress_pro_rich_snippets_events_previous_start_time_manual_time_global'
        ],
        'seopress_pro_rich_snippets_events_location_name' => [
            'key' => '_seopress_pro_rich_snippets_events_location_name'
        ],
        'seopress_pro_rich_snippets_events_location_name_cf' => [
            'key' => '_seopress_pro_rich_snippets_events_location_name_cf'
        ],
        'seopress_pro_rich_snippets_events_location_name_tax' => [
            'key' => '_seopress_pro_rich_snippets_events_location_name_tax'
        ],
        'seopress_pro_rich_snippets_events_location_name_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_events_location_name_manual_global'
        ],
        'seopress_pro_rich_snippets_events_location_url' => [
            'key' => '_seopress_pro_rich_snippets_events_location_url'
        ],
        'seopress_pro_rich_snippets_events_location_url_cf' => [
            'key' => '_seopress_pro_rich_snippets_events_location_url_cf'
        ],
        'seopress_pro_rich_snippets_events_location_url_tax' => [
            'key' => '_seopress_pro_rich_snippets_events_location_url_tax'
        ],
        'seopress_pro_rich_snippets_events_location_url_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_events_location_url_manual_global'
        ],
        'seopress_pro_rich_snippets_events_location_address' => [
            'key' => '_seopress_pro_rich_snippets_events_location_address'
        ],
        'seopress_pro_rich_snippets_events_location_address_cf' => [
            'key' => '_seopress_pro_rich_snippets_events_location_address_cf'
        ],
        'seopress_pro_rich_snippets_events_location_address_tax' => [
            'key' => '_seopress_pro_rich_snippets_events_location_address_tax'
        ],
        'seopress_pro_rich_snippets_events_location_address_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_events_location_address_manual_global'
        ],
        'seopress_pro_rich_snippets_events_offers_name' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_name'
        ],
        'seopress_pro_rich_snippets_events_offers_name_cf' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_name_cf'
        ],
        'seopress_pro_rich_snippets_events_offers_name_tax' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_name_tax'
        ],
        'seopress_pro_rich_snippets_events_offers_name_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_name_manual_global'
        ],
        'seopress_pro_rich_snippets_events_offers_cat' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_cat'
        ],
        'seopress_pro_rich_snippets_events_offers_cat_cf' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_cat_cf'
        ],
        'seopress_pro_rich_snippets_events_offers_cat_tax' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_cat_tax'
        ],
        'seopress_pro_rich_snippets_events_offers_cat_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_cat_manual_global'
        ],
        'seopress_pro_rich_snippets_events_offers_price' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_price'
        ],
        'seopress_pro_rich_snippets_events_offers_price_cf' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_price_cf'
        ],
        'seopress_pro_rich_snippets_events_offers_price_tax' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_price_tax'
        ],
        'seopress_pro_rich_snippets_events_offers_price_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_price_manual_global'
        ],
        'seopress_pro_rich_snippets_events_offers_price_currency' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_price_currency'
        ],
        'seopress_pro_rich_snippets_events_offers_price_currency_cf' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_price_currency_cf'
        ],
        'seopress_pro_rich_snippets_events_offers_price_currency_tax' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_price_currency_tax'
        ],
        'seopress_pro_rich_snippets_events_offers_price_currency_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_price_currency_manual_global'
        ],
        'seopress_pro_rich_snippets_events_offers_availability' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_availability'
        ],
        'seopress_pro_rich_snippets_events_offers_availability_cf' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_availability_cf'
        ],
        'seopress_pro_rich_snippets_events_offers_availability_tax' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_availability_tax'
        ],
        'seopress_pro_rich_snippets_events_offers_availability_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_availability_manual_global'
        ],
        'seopress_pro_rich_snippets_events_offers_valid_from_date' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_valid_from_date'
        ],
        'seopress_pro_rich_snippets_events_offers_valid_from_date_cf' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_valid_from_date_cf'
        ],
        'seopress_pro_rich_snippets_events_offers_valid_from_date_tax' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_valid_from_date_tax'
        ],
        'seopress_pro_rich_snippets_events_offers_valid_from_date_manual_date_global' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_valid_from_date_manual_date_global'
        ],
        'seopress_pro_rich_snippets_events_offers_valid_from_time' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_valid_from_time'
        ],
        'seopress_pro_rich_snippets_events_offers_valid_from_time_cf' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_valid_from_time_cf'
        ],
        'seopress_pro_rich_snippets_events_offers_valid_from_time_tax' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_valid_from_time_tax'
        ],
        'seopress_pro_rich_snippets_events_offers_valid_from_time_manual_time_global' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_valid_from_time_manual_time_global'
        ],
        'seopress_pro_rich_snippets_events_offers_url' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_url'
        ],
        'seopress_pro_rich_snippets_events_offers_url_cf' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_url_cf'
        ],
        'seopress_pro_rich_snippets_events_offers_url_tax' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_url_tax'
        ],
        'seopress_pro_rich_snippets_events_offers_url_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_events_offers_url_manual_global'
        ],
        'seopress_pro_rich_snippets_events_performer' => [
            'key' => '_seopress_pro_rich_snippets_events_performer'
        ],
        'seopress_pro_rich_snippets_events_performer_cf' => [
            'key' => '_seopress_pro_rich_snippets_events_performer_cf'
        ],
        'seopress_pro_rich_snippets_events_performer_tax' => [
            'key' => '_seopress_pro_rich_snippets_events_performer_tax'
        ],
        'seopress_pro_rich_snippets_events_performer_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_events_performer_manual_global'
        ],
        'seopress_pro_rich_snippets_events_organizer_name' => [
            'key' => '_seopress_pro_rich_snippets_events_organizer_name'
        ],
        'seopress_pro_rich_snippets_events_organizer_name_cf' => [
            'key' => '_seopress_pro_rich_snippets_events_organizer_name_cf'
        ],
        'seopress_pro_rich_snippets_events_organizer_name_tax' => [
            'key' => '_seopress_pro_rich_snippets_events_organizer_name_tax'
        ],
        'seopress_pro_rich_snippets_events_organizer_name_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_events_organizer_name_manual_global'
        ],
        'seopress_pro_rich_snippets_events_organizer_url' => [
            'key' => '_seopress_pro_rich_snippets_events_organizer_url'
        ],
        'seopress_pro_rich_snippets_events_organizer_url_cf' => [
            'key' => '_seopress_pro_rich_snippets_events_organizer_url_cf'
        ],
        'seopress_pro_rich_snippets_events_organizer_url_tax' => [
            'key' => '_seopress_pro_rich_snippets_events_organizer_url_tax'
        ],
        'seopress_pro_rich_snippets_events_organizer_url_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_events_organizer_url_manual_global'
        ],
        'seopress_pro_rich_snippets_events_status' => [
            'key' => '_seopress_pro_rich_snippets_events_status'
        ],
        'seopress_pro_rich_snippets_events_status_cf' => [
            'key' => '_seopress_pro_rich_snippets_events_status_cf'
        ],
        'seopress_pro_rich_snippets_events_status_tax' => [
            'key' => '_seopress_pro_rich_snippets_events_status_tax'
        ],
        'seopress_pro_rich_snippets_events_status_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_events_status_manual_global'
        ],
        'seopress_pro_rich_snippets_events_attendance_mode' => [
            'key' => '_seopress_pro_rich_snippets_events_attendance_mode'
        ],
        'seopress_pro_rich_snippets_events_attendance_mode_cf' => [
            'key' => '_seopress_pro_rich_snippets_events_attendance_mode_cf'
        ],
        'seopress_pro_rich_snippets_events_attendance_mode_tax' => [
            'key' => '_seopress_pro_rich_snippets_events_attendance_mode_tax'
        ],
        'seopress_pro_rich_snippets_events_attendance_mode_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_events_attendance_mode_manual_global'
        ],
    ];
    seopress_save_inputs_schema_automatic($inputsEvent, $post_id);

    //Product
    $inputsProduct = [
        'seopress_pro_rich_snippets_product_name' => [
            'key' => '_seopress_pro_rich_snippets_product_name'
        ],
        'seopress_pro_rich_snippets_product_name_cf' => [
            'key' => '_seopress_pro_rich_snippets_product_name_cf'
        ],
        'seopress_pro_rich_snippets_product_name_tax' => [
            'key' => '_seopress_pro_rich_snippets_product_name_tax'
        ],
        'seopress_pro_rich_snippets_product_name_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_product_name_manual_global'
        ],
        'seopress_pro_rich_snippets_product_description' => [
            'key' => '_seopress_pro_rich_snippets_product_description'
        ],
        'seopress_pro_rich_snippets_product_description_cf' => [
            'key' => '_seopress_pro_rich_snippets_product_description_cf'
        ],
        'seopress_pro_rich_snippets_product_description_tax' => [
            'key' => '_seopress_pro_rich_snippets_product_description_tax'
        ],
        'seopress_pro_rich_snippets_product_description_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_product_description_manual_global'
        ],
        'seopress_pro_rich_snippets_product_img' => [
            'key' => '_seopress_pro_rich_snippets_product_img'
        ],
        'seopress_pro_rich_snippets_product_img_manual_img_global' => [
            'key' => '_seopress_pro_rich_snippets_product_img_manual_img_global'
        ],
        'seopress_pro_rich_snippets_product_img_cf' => [
            'key' => '_seopress_pro_rich_snippets_product_img_cf'
        ],
        'seopress_pro_rich_snippets_product_img_tax' => [
            'key' => '_seopress_pro_rich_snippets_product_img_tax'
        ],
        'seopress_pro_rich_snippets_product_img_manual_img_library_global' => [
            'key' => '_seopress_pro_rich_snippets_product_img_manual_img_library_global'
        ],
        'seopress_pro_rich_snippets_product_img_manual_img_library_global_width' => [
            'key' => '_seopress_pro_rich_snippets_product_img_manual_img_library_global_width'
        ],
        'seopress_pro_rich_snippets_product_img_manual_img_library_global_height' => [
            'key' => '_seopress_pro_rich_snippets_product_img_manual_img_library_global_height'
        ],
        'seopress_pro_rich_snippets_product_price' => [
            'key' => '_seopress_pro_rich_snippets_product_price'
        ],
        'seopress_pro_rich_snippets_product_price_cf' => [
            'key' => '_seopress_pro_rich_snippets_product_price_cf'
        ],
        'seopress_pro_rich_snippets_product_price_tax' => [
            'key' => '_seopress_pro_rich_snippets_product_price_tax'
        ],
        'seopress_pro_rich_snippets_product_price_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_product_price_manual_global'
        ],
        'seopress_pro_rich_snippets_product_price_valid_date' => [
            'key' => '_seopress_pro_rich_snippets_product_price_valid_date'
        ],
        'seopress_pro_rich_snippets_product_price_valid_date_cf' => [
            'key' => '_seopress_pro_rich_snippets_product_price_valid_date_cf'
        ],
        'seopress_pro_rich_snippets_product_price_valid_date_tax' => [
            'key' => '_seopress_pro_rich_snippets_product_price_valid_date_tax'
        ],
        'seopress_pro_rich_snippets_product_price_valid_date_manual_date_global' => [
            'key' => '_seopress_pro_rich_snippets_product_price_valid_date_manual_date_global'
        ],
        'seopress_pro_rich_snippets_product_sku' => [
            'key' => '_seopress_pro_rich_snippets_product_sku'
        ],
        'seopress_pro_rich_snippets_product_sku_cf' => [
            'key' => '_seopress_pro_rich_snippets_product_sku_cf'
        ],
        'seopress_pro_rich_snippets_product_sku_tax' => [
            'key' => '_seopress_pro_rich_snippets_product_sku_tax'
        ],
        'seopress_pro_rich_snippets_product_sku_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_product_sku_manual_global'
        ],
        'seopress_pro_rich_snippets_product_global_ids' => [
            'key' => '_seopress_pro_rich_snippets_product_global_ids'
        ],
        'seopress_pro_rich_snippets_product_global_ids_cf' => [
            'key' => '_seopress_pro_rich_snippets_product_global_ids_cf'
        ],
        'seopress_pro_rich_snippets_product_global_ids_tax' => [
            'key' => '_seopress_pro_rich_snippets_product_global_ids_tax'
        ],
        'seopress_pro_rich_snippets_product_global_ids_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_product_global_ids_manual_global'
        ],
        'seopress_pro_rich_snippets_product_global_ids_value' => [
            'key' => '_seopress_pro_rich_snippets_product_global_ids_value'
        ],
        'seopress_pro_rich_snippets_product_global_ids_value_cf' => [
            'key' => '_seopress_pro_rich_snippets_product_global_ids_value_cf'
        ],
        'seopress_pro_rich_snippets_product_global_ids_value_tax' => [
            'key' => '_seopress_pro_rich_snippets_product_global_ids_value_tax'
        ],
        'seopress_pro_rich_snippets_product_global_ids_value_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_product_global_ids_value_manual_global'
        ],
        'seopress_pro_rich_snippets_product_brand' => [
            'key' => '_seopress_pro_rich_snippets_product_brand'
        ],
        'seopress_pro_rich_snippets_product_brand_cf' => [
            'key' => '_seopress_pro_rich_snippets_product_brand_cf'
        ],
        'seopress_pro_rich_snippets_product_brand_tax' => [
            'key' => '_seopress_pro_rich_snippets_product_brand_tax'
        ],
        'seopress_pro_rich_snippets_product_brand_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_product_brand_manual_global'
        ],
        'seopress_pro_rich_snippets_product_price_currency' => [
            'key' => '_seopress_pro_rich_snippets_product_price_currency'
        ],
        'seopress_pro_rich_snippets_product_price_currency_cf' => [
            'key' => '_seopress_pro_rich_snippets_product_price_currency_cf'
        ],
        'seopress_pro_rich_snippets_product_price_currency_tax' => [
            'key' => '_seopress_pro_rich_snippets_product_price_currency_tax'
        ],
        'seopress_pro_rich_snippets_product_price_currency_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_product_price_currency_manual_global'
        ],
        'seopress_pro_rich_snippets_product_condition' => [
            'key' => '_seopress_pro_rich_snippets_product_condition'
        ],
        'seopress_pro_rich_snippets_product_condition_cf' => [
            'key' => '_seopress_pro_rich_snippets_product_condition_cf'
        ],
        'seopress_pro_rich_snippets_product_condition_tax' => [
            'key' => '_seopress_pro_rich_snippets_product_condition_tax'
        ],
        'seopress_pro_rich_snippets_product_condition_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_product_condition_manual_global'
        ],
        'seopress_pro_rich_snippets_product_availability' => [
            'key' => '_seopress_pro_rich_snippets_product_availability'
        ],
        'seopress_pro_rich_snippets_product_availability_cf' => [
            'key' => '_seopress_pro_rich_snippets_product_availability_cf'
        ],
        'seopress_pro_rich_snippets_product_availability_tax' => [
            'key' => '_seopress_pro_rich_snippets_product_availability_tax'
        ],
        'seopress_pro_rich_snippets_product_availability_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_product_availability_manual_global'
        ],
        'seopress_pro_rich_snippets_product_positive_notes' => [
            'key' => '_seopress_pro_rich_snippets_product_positive_notes'
        ],
        'seopress_pro_rich_snippets_product_positive_notes_cf' => [
            'key' => '_seopress_pro_rich_snippets_product_positive_notes_cf'
        ],
        'seopress_pro_rich_snippets_product_positive_notes_tax' => [
            'key' => '_seopress_pro_rich_snippets_product_positive_notes_tax'
        ],
        'seopress_pro_rich_snippets_product_positive_notes_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_product_positive_notes_manual_global'
        ],
        'seopress_pro_rich_snippets_product_negative_notes' => [
            'key' => '_seopress_pro_rich_snippets_product_negative_notes'
        ],
        'seopress_pro_rich_snippets_product_negative_notes_cf' => [
            'key' => '_seopress_pro_rich_snippets_product_negative_notes_cf'
        ],
        'seopress_pro_rich_snippets_product_negative_notes_tax' => [
            'key' => '_seopress_pro_rich_snippets_product_negative_notes_tax'
        ],
        'seopress_pro_rich_snippets_product_negative_notes_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_product_negative_notes_manual_global'
        ],
        'seopress_pro_rich_snippets_product_energy_consumption' => [
            'key' => '_seopress_pro_rich_snippets_product_energy_consumption'
        ],
        'seopress_pro_rich_snippets_product_energy_consumption_cf' => [
            'key' => '_seopress_pro_rich_snippets_product_energy_consumption_cf'
        ],
        'seopress_pro_rich_snippets_product_energy_consumption_tax' => [
            'key' => '_seopress_pro_rich_snippets_product_energy_consumption_tax'
        ],
        'seopress_pro_rich_snippets_product_energy_consumption_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_product_energy_consumption_manual_global'
        ],
    ];
    seopress_save_inputs_schema_automatic($inputsProduct, $post_id);

    //Software App
    $inputsSoftware = [
        'seopress_pro_rich_snippets_softwareapp_name' => [
            'key' => '_seopress_pro_rich_snippets_softwareapp_name'
        ],
        'seopress_pro_rich_snippets_softwareapp_name_cf' => [
            'key' => '_seopress_pro_rich_snippets_softwareapp_name_cf'
        ],
        'seopress_pro_rich_snippets_softwareapp_name_tax' => [
            'key' => '_seopress_pro_rich_snippets_softwareapp_name_tax'
        ],
        'seopress_pro_rich_snippets_softwareapp_name_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_softwareapp_name_manual_global'
        ],
        'seopress_pro_rich_snippets_softwareapp_os' => [
            'key' => '_seopress_pro_rich_snippets_softwareapp_os'
        ],
        'seopress_pro_rich_snippets_softwareapp_os_cf' => [
            'key' => '_seopress_pro_rich_snippets_softwareapp_os_cf'
        ],
        'seopress_pro_rich_snippets_softwareapp_os_tax' => [
            'key' => '_seopress_pro_rich_snippets_softwareapp_os_tax'
        ],
        'seopress_pro_rich_snippets_softwareapp_os_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_softwareapp_os_manual_global'
        ],
        'seopress_pro_rich_snippets_softwareapp_cat' => [
            'key' => '_seopress_pro_rich_snippets_softwareapp_cat'
        ],
        'seopress_pro_rich_snippets_softwareapp_cat_cf' => [
            'key' => '_seopress_pro_rich_snippets_softwareapp_cat_cf'
        ],
        'seopress_pro_rich_snippets_softwareapp_cat_tax' => [
            'key' => '_seopress_pro_rich_snippets_softwareapp_cat_tax'
        ],
        'seopress_pro_rich_snippets_softwareapp_cat_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_softwareapp_cat_manual_global'
        ],
        'seopress_pro_rich_snippets_softwareapp_price' => [
            'key' => '_seopress_pro_rich_snippets_softwareapp_price'
        ],
        'seopress_pro_rich_snippets_softwareapp_price_cf' => [
            'key' => '_seopress_pro_rich_snippets_softwareapp_price_cf'
        ],
        'seopress_pro_rich_snippets_softwareapp_price_tax' => [
            'key' => '_seopress_pro_rich_snippets_softwareapp_price_tax'
        ],
        'seopress_pro_rich_snippets_softwareapp_price_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_softwareapp_price_manual_global'
        ],
        'seopress_pro_rich_snippets_softwareapp_currency' => [
            'key' => '_seopress_pro_rich_snippets_softwareapp_currency'
        ],
        'seopress_pro_rich_snippets_softwareapp_currency_cf' => [
            'key' => '_seopress_pro_rich_snippets_softwareapp_currency_cf'
        ],
        'seopress_pro_rich_snippets_softwareapp_currency_tax' => [
            'key' => '_seopress_pro_rich_snippets_softwareapp_currency_tax'
        ],
        'seopress_pro_rich_snippets_softwareapp_currency_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_softwareapp_currency_manual_global'
        ],
        'seopress_pro_rich_snippets_softwareapp_rating' => [
            'key' => '_seopress_pro_rich_snippets_softwareapp_rating'
        ],
        'seopress_pro_rich_snippets_softwareapp_rating_cf' => [
            'key' => '_seopress_pro_rich_snippets_softwareapp_rating_cf'
        ],
        'seopress_pro_rich_snippets_softwareapp_rating_tax' => [
            'key' => '_seopress_pro_rich_snippets_softwareapp_rating_tax'
        ],
        'seopress_pro_rich_snippets_softwareapp_rating_manual_rating_global' => [
            'key' => '_seopress_pro_rich_snippets_softwareapp_rating_manual_rating_global'
        ],
        'seopress_pro_rich_snippets_softwareapp_max_rating' => [
            'key' => '_seopress_pro_rich_snippets_softwareapp_max_rating'
        ],
        'seopress_pro_rich_snippets_softwareapp_max_rating_cf' => [
            'key' => '_seopress_pro_rich_snippets_softwareapp_max_rating_cf'
        ],
        'seopress_pro_rich_snippets_softwareapp_max_rating_tax' => [
            'key' => '_seopress_pro_rich_snippets_softwareapp_max_rating_tax'
        ],
        'seopress_pro_rich_snippets_softwareapp_max_rating_manual_rating_global' => [
            'key' => '_seopress_pro_rich_snippets_softwareapp_max_rating_manual_rating_global'
        ],
    ];

    seopress_save_inputs_schema_automatic($inputsSoftware, $post_id);

    //Service
    $inputsService = [
        'seopress_pro_rich_snippets_service_name' => [
            'key' => '_seopress_pro_rich_snippets_service_name'
        ],
        'seopress_pro_rich_snippets_service_name_cf' => [
            'key' => '_seopress_pro_rich_snippets_service_name_cf'
        ],
        'seopress_pro_rich_snippets_service_name_tax' => [
            'key' => '_seopress_pro_rich_snippets_service_name_tax'
        ],
        'seopress_pro_rich_snippets_service_name_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_service_name_manual_global'
        ],
        'seopress_pro_rich_snippets_service_type' => [
            'key' => '_seopress_pro_rich_snippets_service_type'
        ],
        'seopress_pro_rich_snippets_service_type_cf' => [
            'key' => '_seopress_pro_rich_snippets_service_type_cf'
        ],
        'seopress_pro_rich_snippets_service_type_tax' => [
            'key' => '_seopress_pro_rich_snippets_service_type_tax'
        ],
        'seopress_pro_rich_snippets_service_type_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_service_type_manual_global'
        ],
        'seopress_pro_rich_snippets_service_description' => [
            'key' => '_seopress_pro_rich_snippets_service_description'
        ],
        'seopress_pro_rich_snippets_service_description_cf' => [
            'key' => '_seopress_pro_rich_snippets_service_description_cf'
        ],
        'seopress_pro_rich_snippets_service_description_tax' => [
            'key' => '_seopress_pro_rich_snippets_service_description_tax'
        ],
        'seopress_pro_rich_snippets_service_description_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_service_description_manual_global'
        ],
        'seopress_pro_rich_snippets_service_img' => [
            'key' => '_seopress_pro_rich_snippets_service_img'
        ],
        'seopress_pro_rich_snippets_service_img_manual_img_global' => [
            'key' => '_seopress_pro_rich_snippets_service_img_manual_img_global'
        ],
        'seopress_pro_rich_snippets_service_img_cf' => [
            'key' => '_seopress_pro_rich_snippets_service_img_cf'
        ],
        'seopress_pro_rich_snippets_service_img_tax' => [
            'key' => '_seopress_pro_rich_snippets_service_img_tax'
        ],
        'seopress_pro_rich_snippets_service_img_manual_img_library_global' => [
            'key' => '_seopress_pro_rich_snippets_service_img_manual_img_library_global'
        ],
        'seopress_pro_rich_snippets_service_img_manual_img_library_global_width' => [
            'key' => '_seopress_pro_rich_snippets_service_img_manual_img_library_global_width'
        ],
        'seopress_pro_rich_snippets_service_img_manual_img_library_global_height' => [
            'key' => '_seopress_pro_rich_snippets_service_img_manual_img_library_global_height'
        ],
        'seopress_pro_rich_snippets_service_area' => [
            'key' => '_seopress_pro_rich_snippets_service_area'
        ],
        'seopress_pro_rich_snippets_service_area_cf' => [
            'key' => '_seopress_pro_rich_snippets_service_area_cf'
        ],
        'seopress_pro_rich_snippets_service_area_tax' => [
            'key' => '_seopress_pro_rich_snippets_service_area_tax'
        ],
        'seopress_pro_rich_snippets_service_area_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_service_area_manual_global'
        ],
        'seopress_pro_rich_snippets_service_provider_name' => [
            'key' => '_seopress_pro_rich_snippets_service_provider_name'
        ],
        'seopress_pro_rich_snippets_service_provider_name_cf' => [
            'key' => '_seopress_pro_rich_snippets_service_provider_name_cf'
        ],
        'seopress_pro_rich_snippets_service_provider_name_tax' => [
            'key' => '_seopress_pro_rich_snippets_service_provider_name_tax'
        ],
        'seopress_pro_rich_snippets_service_provider_name_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_service_provider_name_manual_global'
        ],
        'seopress_pro_rich_snippets_service_lb_img' => [
            'key' => '_seopress_pro_rich_snippets_service_lb_img'
        ],
        'seopress_pro_rich_snippets_service_lb_img_manual_img_global' => [
            'key' => '_seopress_pro_rich_snippets_service_lb_img_manual_img_global'
        ],
        'seopress_pro_rich_snippets_service_lb_img_cf' => [
            'key' => '_seopress_pro_rich_snippets_service_lb_img_cf'
        ],
        'seopress_pro_rich_snippets_service_lb_img_tax' => [
            'key' => '_seopress_pro_rich_snippets_service_lb_img_tax'
        ],
        'seopress_pro_rich_snippets_service_lb_img_manual_img_library_global' => [
            'key' => '_seopress_pro_rich_snippets_service_lb_img_manual_img_library_global'
        ],
        'seopress_pro_rich_snippets_service_lb_img_manual_img_library_global_width' => [
            'key' => '_seopress_pro_rich_snippets_service_lb_img_manual_img_library_global_width'
        ],
        'seopress_pro_rich_snippets_service_lb_img_manual_img_library_global_height' => [
            'key' => '_seopress_pro_rich_snippets_service_lb_img_manual_img_library_global_height'
        ],
        'seopress_pro_rich_snippets_service_provider_mobility' => [
            'key' => '_seopress_pro_rich_snippets_service_provider_mobility'
        ],
        'seopress_pro_rich_snippets_service_provider_mobility_cf' => [
            'key' => '_seopress_pro_rich_snippets_service_provider_mobility_cf'
        ],
        'seopress_pro_rich_snippets_service_provider_mobility_tax' => [
            'key' => '_seopress_pro_rich_snippets_service_provider_mobility_tax'
        ],
        'seopress_pro_rich_snippets_service_provider_mobility_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_service_provider_mobility_manual_global'
        ],
        'seopress_pro_rich_snippets_service_slogan' => [
            'key' => '_seopress_pro_rich_snippets_service_slogan'
        ],
        'seopress_pro_rich_snippets_service_slogan_cf' => [
            'key' => '_seopress_pro_rich_snippets_service_slogan_cf'
        ],
        'seopress_pro_rich_snippets_service_slogan_tax' => [
            'key' => '_seopress_pro_rich_snippets_service_slogan_tax'
        ],
        'seopress_pro_rich_snippets_service_slogan_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_service_slogan_manual_global'
        ],
        'seopress_pro_rich_snippets_service_street_addr' => [
            'key' => '_seopress_pro_rich_snippets_service_street_addr'
        ],
        'seopress_pro_rich_snippets_service_street_addr_cf' => [
            'key' => '_seopress_pro_rich_snippets_service_street_addr_cf'
        ],
        'seopress_pro_rich_snippets_service_street_addr_tax' => [
            'key' => '_seopress_pro_rich_snippets_service_street_addr_tax'
        ],
        'seopress_pro_rich_snippets_service_street_addr_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_service_street_addr_manual_global'
        ],
        'seopress_pro_rich_snippets_service_city' => [
            'key' => '_seopress_pro_rich_snippets_service_city'
        ],
        'seopress_pro_rich_snippets_service_city_cf' => [
            'key' => '_seopress_pro_rich_snippets_service_city_cf'
        ],
        'seopress_pro_rich_snippets_service_city_tax' => [
            'key' => '_seopress_pro_rich_snippets_service_city_tax'
        ],
        'seopress_pro_rich_snippets_service_city_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_service_city_manual_global'
        ],
        'seopress_pro_rich_snippets_service_state' => [
            'key' => '_seopress_pro_rich_snippets_service_state'
        ],
        'seopress_pro_rich_snippets_service_state_cf' => [
            'key' => '_seopress_pro_rich_snippets_service_state_cf'
        ],
        'seopress_pro_rich_snippets_service_state_tax' => [
            'key' => '_seopress_pro_rich_snippets_service_state_tax'
        ],
        'seopress_pro_rich_snippets_service_state_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_service_state_manual_global'
        ],
        'seopress_pro_rich_snippets_service_pc' => [
            'key' => '_seopress_pro_rich_snippets_service_pc'
        ],
        'seopress_pro_rich_snippets_service_pc_cf' => [
            'key' => '_seopress_pro_rich_snippets_service_pc_cf'
        ],
        'seopress_pro_rich_snippets_service_pc_tax' => [
            'key' => '_seopress_pro_rich_snippets_service_pc_tax'
        ],
        'seopress_pro_rich_snippets_service_pc_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_service_pc_manual_global'
        ],
        'seopress_pro_rich_snippets_service_country' => [
            'key' => '_seopress_pro_rich_snippets_service_country'
        ],
        'seopress_pro_rich_snippets_service_country_cf' => [
            'key' => '_seopress_pro_rich_snippets_service_country_cf'
        ],
        'seopress_pro_rich_snippets_service_country_tax' => [
            'key' => '_seopress_pro_rich_snippets_service_country_tax'
        ],
        'seopress_pro_rich_snippets_service_country_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_service_country_manual_global'
        ],
        'seopress_pro_rich_snippets_service_lat' => [
            'key' => '_seopress_pro_rich_snippets_service_lat'
        ],
        'seopress_pro_rich_snippets_service_lat_cf' => [
            'key' => '_seopress_pro_rich_snippets_service_lat_cf'
        ],
        'seopress_pro_rich_snippets_service_lat_tax' => [
            'key' => '_seopress_pro_rich_snippets_service_lat_tax'
        ],
        'seopress_pro_rich_snippets_service_lat_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_service_lat_manual_global'
        ],
        'seopress_pro_rich_snippets_service_lon' => [
            'key' => '_seopress_pro_rich_snippets_service_lon'
        ],
        'seopress_pro_rich_snippets_service_lon_cf' => [
            'key' => '_seopress_pro_rich_snippets_service_lon_cf'
        ],
        'seopress_pro_rich_snippets_service_lon_tax' => [
            'key' => '_seopress_pro_rich_snippets_service_lon_tax'
        ],
        'seopress_pro_rich_snippets_service_lon_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_service_lon_manual_global'
        ],
        'seopress_pro_rich_snippets_service_tel' => [
            'key' => '_seopress_pro_rich_snippets_service_tel'
        ],
        'seopress_pro_rich_snippets_service_tel_cf' => [
            'key' => '_seopress_pro_rich_snippets_service_tel_cf'
        ],
        'seopress_pro_rich_snippets_service_tel_tax' => [
            'key' => '_seopress_pro_rich_snippets_service_tel_tax'
        ],
        'seopress_pro_rich_snippets_service_tel_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_service_tel_manual_global'
        ],
        'seopress_pro_rich_snippets_service_price' => [
            'key' => '_seopress_pro_rich_snippets_service_price'
        ],
        'seopress_pro_rich_snippets_service_price_cf' => [
            'key' => '_seopress_pro_rich_snippets_service_price_cf'
        ],
        'seopress_pro_rich_snippets_service_price_tax' => [
            'key' => '_seopress_pro_rich_snippets_service_price_tax'
        ],
        'seopress_pro_rich_snippets_service_price_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_service_price_manual_global'
        ],
    ];
    seopress_save_inputs_schema_automatic($inputsService, $post_id);

    //Review
    $inputsReview = [
        'seopress_pro_rich_snippets_review_item' => [
            'key' => '_seopress_pro_rich_snippets_review_item'
        ],
        'seopress_pro_rich_snippets_review_item_cf' => [
            'key' => '_seopress_pro_rich_snippets_review_item_cf'
        ],
        'seopress_pro_rich_snippets_review_item_tax' => [
            'key' => '_seopress_pro_rich_snippets_review_item_tax'
        ],
        'seopress_pro_rich_snippets_review_item_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_review_item_manual_global'
        ],
        'seopress_pro_rich_snippets_review_item_type' => [
            'key' => '_seopress_pro_rich_snippets_review_item_type'
        ],
        'seopress_pro_rich_snippets_review_item_type_cf' => [
            'key' => '_seopress_pro_rich_snippets_review_item_type_cf'
        ],
        'seopress_pro_rich_snippets_review_item_type_tax' => [
            'key' => '_seopress_pro_rich_snippets_review_item_type_tax'
        ],
        'seopress_pro_rich_snippets_review_item_type_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_review_item_type_manual_global'
        ],
        'seopress_pro_rich_snippets_review_img' => [
            'key' => '_seopress_pro_rich_snippets_review_img'
        ],
        'seopress_pro_rich_snippets_review_img_manual_img_global' => [
            'key' => '_seopress_pro_rich_snippets_review_img_manual_img_global'
        ],
        'seopress_pro_rich_snippets_review_img_cf' => [
            'key' => '_seopress_pro_rich_snippets_review_img_cf'
        ],
        'seopress_pro_rich_snippets_review_img_tax' => [
            'key' => '_seopress_pro_rich_snippets_review_img_tax'
        ],
        'seopress_pro_rich_snippets_review_img_manual_img_library_global' => [
            'key' => '_seopress_pro_rich_snippets_review_img_manual_img_library_global'
        ],
        'seopress_pro_rich_snippets_review_img_manual_img_library_global_width' => [
            'key' => '_seopress_pro_rich_snippets_review_img_manual_img_library_global_width'
        ],
        'seopress_pro_rich_snippets_review_img_manual_img_library_global_height' => [
            'key' => '_seopress_pro_rich_snippets_review_img_manual_img_library_global_height'
        ],
        'seopress_pro_rich_snippets_review_rating' => [
            'key' => '_seopress_pro_rich_snippets_review_rating'
        ],
        'seopress_pro_rich_snippets_review_rating_cf' => [
            'key' => '_seopress_pro_rich_snippets_review_rating_cf'
        ],
        'seopress_pro_rich_snippets_review_rating_tax' => [
            'key' => '_seopress_pro_rich_snippets_review_rating_tax'
        ],
        'seopress_pro_rich_snippets_review_rating_manual_rating_global' => [
            'key' => '_seopress_pro_rich_snippets_review_rating_manual_rating_global'
        ],
        'seopress_pro_rich_snippets_review_max_rating' => [
            'key' => '_seopress_pro_rich_snippets_review_max_rating'
        ],
        'seopress_pro_rich_snippets_review_max_rating_cf' => [
            'key' => '_seopress_pro_rich_snippets_review_max_rating_cf'
        ],
        'seopress_pro_rich_snippets_review_max_rating_tax' => [
            'key' => '_seopress_pro_rich_snippets_review_max_rating_tax'
        ],
        'seopress_pro_rich_snippets_review_max_rating_manual_rating_global' => [
            'key' => '_seopress_pro_rich_snippets_review_max_rating_manual_rating_global'
        ],
        'seopress_pro_rich_snippets_review_body' => [
            'key' => '_seopress_pro_rich_snippets_review_body'
        ],
        'seopress_pro_rich_snippets_review_body_cf' => [
            'key' => '_seopress_pro_rich_snippets_review_body_cf'
        ],
        'seopress_pro_rich_snippets_review_body_tax' => [
            'key' => '_seopress_pro_rich_snippets_review_body_tax'
        ],
        'seopress_pro_rich_snippets_review_body_manual_global' => [
            'key' => '_seopress_pro_rich_snippets_review_body_manual_global'
        ],
    ];
    seopress_save_inputs_schema_automatic($inputsReview, $post_id);

    //Custom
    if (isset($_POST['seopress_pro_rich_snippets_custom'])) {
        update_post_meta($post_id, '_seopress_pro_rich_snippets_custom', esc_html($_POST['seopress_pro_rich_snippets_custom']));
    }
    if (isset($_POST['seopress_pro_rich_snippets_custom_manual_custom_global']) && !empty($_POST['seopress_pro_rich_snippets_custom_manual_custom_global'])) {
        update_post_meta(
            $post_id,
            '_seopress_pro_rich_snippets_custom_manual_custom_global',
            $_POST['seopress_pro_rich_snippets_custom_manual_custom_global']
        );
    }
}
