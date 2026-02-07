<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_breadcrumbs_enable_callback() {
	$options = get_option('seopress_pro_option_name');

	$check = isset($options['seopress_breadcrumbs_enable']); ?>

<label for="seopress_breadcrumbs_enable">
	<input id="seopress_breadcrumbs_enable" name="seopress_pro_option_name[seopress_breadcrumbs_enable]" type="checkbox"
		<?php if (true === $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php esc_html_e('Enable HTML Breadcrumbs', 'wp-seopress-pro'); ?>
</label>

<?php if (isset($options['seopress_breadcrumbs_enable'])) {
		esc_attr($options['seopress_breadcrumbs_enable']);
	}
}

function seopress_breadcrumbs_enable_json_callback() {
	$options = get_option('seopress_pro_option_name');

	$check = isset($options['seopress_breadcrumbs_json_enable']); ?>

<label for="seopress_breadcrumbs_json_enable">
	<input id="seopress_breadcrumbs_json_enable" name="seopress_pro_option_name[seopress_breadcrumbs_json_enable]"
		type="checkbox" <?php if (true === $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php esc_html_e('Enable JSON-LD Breadcrumbs', 'wp-seopress-pro'); ?>
</label>

<p class="description">
	<?php esc_html_e('To avoid duplicated schemas, we automatically remove the microdata on the HTML breadcrumbs.', 'wp-seopress-pro'); ?>
</p>
<p class="description">
	<?php esc_html_e('We automatically add the JSON-LD to the head of your document using the wp_head hook.', 'wp-seopress-pro'); ?>
</p>
<p class="description">
	<?php esc_html_e('You don\'t need to manually call the breadcrumbs function.', 'wp-seopress-pro'); ?>
</p>

<?php if (isset($options['seopress_breadcrumbs_json_enable'])) {
		esc_attr($options['seopress_breadcrumbs_json_enable']);
	}
}

function seopress_breadcrumbs_separator_callback() {
	$options = get_option('seopress_pro_option_name');
	$check = isset($options['seopress_breadcrumbs_separator']) ? $options['seopress_breadcrumbs_separator'] : null;
	$docs = function_exists('seopress_get_docs_links') ? seopress_get_docs_links() : '';

	printf(
		'<input type="text" class="seopress_breadcrumbs_sep" name="seopress_pro_option_name[seopress_breadcrumbs_separator]" aria-label="' . esc_html__('Breadcrumbs Separator', 'wp-seopress-pro') . '" placeholder="' . esc_html__('e.g. / ', 'wp-seopress-pro') . '" value="%s" />',
		esc_html($check)
	); ?>

<div class="wrap-tags">
	<button type="button" class="btn btnSecondary tag-title" id="seopress-tag-breadcrumbs-1" data-tag="-"><?php esc_html_e('-', 'wp-seopress-pro'); ?></button>
	<button type="button" class="btn btnSecondary tag-title" id="seopress-tag-breadcrumbs-2" data-tag="–"
		class="tag-title"><?php esc_html_e('–', 'wp-seopress-pro'); ?></button>
	<button type="button" class="btn btnSecondary tag-title" id="seopress-tag-breadcrumbs-3" data-tag=">"
		class="tag-title"><?php esc_html_e('>', 'wp-seopress-pro'); ?></button>
	<button type="button" class="btn btnSecondary tag-title" id="seopress-tag-breadcrumbs-4" data-tag="<"
		class="tag-title"><?php esc_html_e('<', 'wp-seopress-pro'); ?></button>
	<button type="button" class="btn btnSecondary tag-title" id="seopress-tag-breadcrumbs-5" data-tag="|"
		class="tag-title"><?php esc_html_e('|', 'wp-seopress-pro'); ?></button>
</div>

<?php echo seopress_tooltip_link(esc_url($docs['breadcrumbs']['sep']), esc_html__('Customize breadcrumbs separator with a hook', 'wp-seopress-pro')); ?>
<?php
}

function seopress_breadcrumbs_cpt_callback() {
	$none = ['name' => 'none', 'label' => 'None'];
	$none = json_decode(wp_json_encode($none));
	$none_a['none'] = $none;

	$serviceWpData = seopress_get_service('WordPressData');

	if ( ! $serviceWpData || ! method_exists($serviceWpData, 'getTaxonomies')) {
		$tax = [];
	} else {
		$tax = $serviceWpData->getTaxonomies();
	} ?>

	<p>
		<?php esc_html_e('Select the post types that you want to display in the breadcrumbs for this taxonomy.', 'wp-seopress-pro'); ?>
	</p>

	<?php
	if ( ! empty($tax)) {
		foreach ($tax as $taxonomy) { ?>
			<h3><?php echo esc_html($taxonomy->label); ?> <code>[<?php echo esc_html($taxonomy->name); ?>]</code></h3>

			<select id="seopress_breadcrumbs_cpt" name="seopress_pro_option_name[seopress_breadcrumbs_cpt][<?php echo esc_attr($taxonomy->name); ?>][cpt]">
				<?php
					if ( ! $serviceWpData) {
						$cpt = [];
					} else {
						$cpt = $serviceWpData->getPostTypes();
					}
					unset($cpt['page']);
					$cpt = array_merge($none_a, $cpt);

					if ( ! empty($cpt)) {
						foreach ($cpt as $post_type) {
							$options = get_option('seopress_pro_option_name');

							$selected = isset($options['seopress_breadcrumbs_cpt'][$taxonomy->name]['cpt']) ? $options['seopress_breadcrumbs_cpt'][$taxonomy->name]['cpt'] : null; ?>

							<option <?php if (esc_attr($post_type->name) == $selected) { ?>
								selected="selected"
								<?php } ?>
								value="<?php echo esc_attr($post_type->name); ?>"><?php echo esc_html($post_type->label) . ' [' . esc_html($post_type->name) . ']'; ?>
							</option>

						<?php if (isset($options['seopress_breadcrumbs_cpt'][$taxonomy->name]['cpt'])) {
							esc_attr($options['seopress_breadcrumbs_cpt'][$taxonomy->name]['cpt']);
						}
					}
				} ?>
			</select>
			<?php
		}
	}
}

function seopress_breadcrumbs_tax_callback() {
	$none = ['name' => 'none', 'label' => 'None'];
	$none = json_decode(wp_json_encode($none));
	$none_a['none'] = $none;

	$serviceWpData = seopress_get_service('WordPressData');
	$cpt = [];
	if ($serviceWpData) {
		$cpt = $serviceWpData->getPostTypes();
	}
	?>
	<p>
		<?php esc_html_e('Select the taxonomy that you want to display in the breadcrumbs for this post type.', 'wp-seopress-pro'); ?>
	</p>

	<?php
	if ( ! empty($cpt)) {
		foreach ($cpt as $post_type) {
			?>
			<h3><?php echo esc_html($post_type->label); ?> <code>[<?php echo esc_html($post_type->name); ?>]</code></h3>

			<select id="seopress_breadcrumbs_tax" name="seopress_pro_option_name[seopress_breadcrumbs_tax][<?php echo esc_attr($post_type->name); ?>][tax]">

				<?php
					$serviceWpData = seopress_get_service('WordPressData');
					$tax = [];
					if ($serviceWpData && method_exists($serviceWpData, 'getTaxonomies')) {
						$tax = $serviceWpData->getTaxonomies();
					}

					$tax = array_merge($none_a, $tax);

					if ( ! empty($tax)) {
						foreach ($tax as $taxonomy) {
							$options = get_option('seopress_pro_option_name');

							$selected = isset($options['seopress_breadcrumbs_tax'][$post_type->name]['tax']) ? $options['seopress_breadcrumbs_tax'][$post_type->name]['tax'] : null; ?>

							<option <?php if (esc_attr($taxonomy->name) == $selected) { ?>
								selected="selected"
								<?php } ?>
								value="<?php echo esc_attr($taxonomy->name); ?>"><?php echo esc_html($taxonomy->label) . ' [' . esc_html($taxonomy->name) . ']'; ?>
							</option>

							<?php if (isset($options['seopress_breadcrumbs_tax'][$post_type->name]['tax'])) {
								esc_attr($options['seopress_breadcrumbs_tax'][$post_type->name]['tax']);
							}
						}
					}
				?>
			</select>
			<?php 
		}
	}
}

function seopress_breadcrumbs_remove_blog_page_callback() {
	$options = get_option('seopress_pro_option_name');

	$check = isset($options['seopress_breadcrumbs_remove_blog_page']); ?>

<label for="seopress_breadcrumbs_remove_blog_page">
	<input id="seopress_breadcrumbs_remove_blog_page"
		name="seopress_pro_option_name[seopress_breadcrumbs_remove_blog_page]" type="checkbox" <?php if (true === $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php esc_html_e('Remove static Posts page defined in WordPress Reading settings', 'wp-seopress-pro'); ?>
</label>

<?php if (isset($options['seopress_breadcrumbs_remove_blog_page'])) {
		esc_attr($options['seopress_breadcrumbs_remove_blog_page']);
	}
}

function seopress_breadcrumbs_remove_shop_page_callback() {
	$options = get_option('seopress_pro_option_name');

	$check = isset($options['seopress_breadcrumbs_remove_shop_page']); ?>

<label for="seopress_breadcrumbs_remove_shop_page">
	<input id="seopress_breadcrumbs_remove_shop_page"
		name="seopress_pro_option_name[seopress_breadcrumbs_remove_shop_page]" type="checkbox" <?php if (true === $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>

	<?php esc_html_e('Remove the static Shop page defined in the WooCommerce settings', 'wp-seopress-pro'); ?>
</label>

<?php if (isset($options['seopress_breadcrumbs_remove_shop_page'])) {
		esc_attr($options['seopress_breadcrumbs_remove_shop_page']);
	}
}

function seopress_breadcrumbs_i18n_here_callback() {
	$options = get_option('seopress_pro_option_name');
	$check = isset($options['seopress_breadcrumbs_i18n_here']) ? $options['seopress_breadcrumbs_i18n_here'] : null;

	printf(
		'<input type="text" name="seopress_pro_option_name[seopress_breadcrumbs_i18n_here]" aria-label="' . esc_html__('e.g. You are here: ', 'wp-seopress-pro') . '" placeholder="' . esc_html__('e.g. You are here: ', 'wp-seopress-pro') . '" value="%s" />',
		esc_html($check)
	); ?>

<p class="description">
	<?php echo wp_kses_post(__('HTML tags allowed, e.g. <code>span</code>, <code>p</code>...', 'wp-seopress-pro')); ?>
</p>

<?php
}

function seopress_breadcrumbs_i18n_home_callback() {
	$options = get_option('seopress_pro_option_name');
	$check = isset($options['seopress_breadcrumbs_i18n_home']) ? $options['seopress_breadcrumbs_i18n_home'] : null;

	printf(
		'<input type="text" name="seopress_pro_option_name[seopress_breadcrumbs_i18n_home]" aria-label="' . esc_html__('Home', 'wp-seopress-pro') . '" placeholder="' . esc_html__('default: Home', 'wp-seopress-pro') . '" value="%s" />',
		esc_html($check)
	); ?>
<p class="description">
	<?php echo wp_kses_post(__('HTML tags allowed, e.g. <code>span</code>, <code>p</code>...', 'wp-seopress-pro')); ?>
</p>
<?php
}

function seopress_breadcrumbs_i18n_author_callback() {
	$options = get_option('seopress_pro_option_name');
	$check = isset($options['seopress_breadcrumbs_i18n_author']) ? $options['seopress_breadcrumbs_i18n_author'] : null;

	printf(
		'<input type="text" name="seopress_pro_option_name[seopress_breadcrumbs_i18n_author]" aria-label="' . esc_html__('Author:', 'wp-seopress-pro') . '" placeholder="' . esc_html__('default: Author:', 'wp-seopress-pro') . '" value="%s" />',
		esc_html($check)
	);
}

function seopress_breadcrumbs_i18n_404_callback() {
	$options = get_option('seopress_pro_option_name');
	$check = isset($options['seopress_breadcrumbs_i18n_404']) ? $options['seopress_breadcrumbs_i18n_404'] : null;

	printf(
		'<input type="text" name="seopress_pro_option_name[seopress_breadcrumbs_i18n_404]" aria-label="' . esc_html__('404 error', 'wp-seopress-pro') . '" placeholder="' . esc_html__('default: 404 error', 'wp-seopress-pro') . '" value="%s" />',
		esc_html($check)
	);
}

function seopress_breadcrumbs_i18n_search_callback() {
	$options = get_option('seopress_pro_option_name');
	$check = isset($options['seopress_breadcrumbs_i18n_search']) ? $options['seopress_breadcrumbs_i18n_search'] : null;

	printf(
		'<input type="text" name="seopress_pro_option_name[seopress_breadcrumbs_i18n_search]" aria-label="' . esc_html__('Search results for: ', 'wp-seopress-pro') . '" placeholder="' . esc_html__('default: Search results for: ', 'wp-seopress-pro') . '" value="%s" />',
		esc_html($check)
	);
}

function seopress_breadcrumbs_i18n_no_results_callback() {
	$options = get_option('seopress_pro_option_name');
	$check = isset($options['seopress_breadcrumbs_i18n_no_results']) ? $options['seopress_breadcrumbs_i18n_no_results'] : null;

	printf(
		'<input type="text" name="seopress_pro_option_name[seopress_breadcrumbs_i18n_no_results]" aria-label="' . esc_html__('No results', 'wp-seopress-pro') . '" placeholder="' . esc_html__('Default: No results', 'wp-seopress-pro') . '" value="%s" />',
		esc_html($check)
	);
}

function seopress_breadcrumbs_i18n_attachments_callback() {
	$options = get_option('seopress_pro_option_name');
	$check = isset($options['seopress_breadcrumbs_i18n_attachments']) ? $options['seopress_breadcrumbs_i18n_attachments'] : null;

	printf(
		'<input type="text" name="seopress_pro_option_name[seopress_breadcrumbs_i18n_attachments]" aria-label="' . esc_html__('Attachments', 'wp-seopress-pro') . '" placeholder="' . esc_html__('Default: Attachments', 'wp-seopress-pro') . '" value="%s" />',
		esc_html($check)
	);
}

function seopress_breadcrumbs_i18n_paged_callback() {
	$options = get_option('seopress_pro_option_name');
	$check = isset($options['seopress_breadcrumbs_i18n_paged']) ? $options['seopress_breadcrumbs_i18n_paged'] : null;

	printf(
		'<input type="text" name="seopress_pro_option_name[seopress_breadcrumbs_i18n_paged]" aria-label="' . esc_html__('Page ', 'wp-seopress-pro') . '" placeholder="' . esc_html__('Default: Page ', 'wp-seopress-pro') . '" value="%s" />',
		esc_html($check)
	);
}

function seopress_breadcrumbs_separator_disable_callback() {
	$options = get_option('seopress_pro_option_name');

	$check = isset($options['seopress_breadcrumbs_separator_disable']); ?>

<label for="seopress_breadcrumbs_separator_disable">
	<input id="seopress_breadcrumbs_separator_disable"
		name="seopress_pro_option_name[seopress_breadcrumbs_separator_disable]" type="checkbox" <?php if (true === $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>
	<?php esc_html_e('My theme is already displaying a separator in my breadcrumbs', 'wp-seopress-pro'); ?>
</label>

<?php if (isset($options['seopress_breadcrumbs_separator_disable'])) {
		esc_attr($options['seopress_breadcrumbs_separator_disable']);
	}
}

function seopress_breadcrumbs_storefront_callback() {
	$options = get_option('seopress_pro_option_name');

	$check = isset($options['seopress_breadcrumbs_storefront']); ?>

<label for="seopress_breadcrumbs_storefront">
	<input id="seopress_breadcrumbs_storefront" name="seopress_pro_option_name[seopress_breadcrumbs_storefront]"
		type="checkbox" <?php if (true === $check) { ?>
	checked="yes"
	<?php } ?>
	value="1"/>
	<?php esc_html_e('Try to automatically override Storefront‘s default breadcrumbs', 'wp-seopress-pro'); ?>
</label>

<?php if (isset($options['seopress_breadcrumbs_storefront'])) {
		esc_attr($options['seopress_breadcrumbs_storefront']);
	}
}
