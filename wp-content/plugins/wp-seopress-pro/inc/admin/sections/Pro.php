<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_print_pro_section($key)
{
	$docs = function_exists('seopress_get_docs_links') ? seopress_get_docs_links() : '';

	$breadcrumbs_desc =     '<div class="seopress-sub-tabs"><a href="#seopress-breadcrumbs-enable">' . esc_attr__('Enable', 'wp-seopress-pro'). '</a> | <a href="#seopress-breadcrumbs-customize">' . esc_attr__('Customize', 'wp-seopress-pro'). '</a> | <a href="#seopress-breadcrumbs-translations">' . esc_attr__('Translations', 'wp-seopress-pro') . '</a> | <a href="#seopress-breadcrumbs-misc">' . esc_attr__('Misc', 'wp-seopress-pro') . '</a></div> '. __('Configure your breadcrumbs, using schema.org markup, allowing it to appear in Google\'s search results.', 'wp-seopress-pro') . '
	<a class="seopress-help" href="https://developers.google.com/search/docs/data-types/breadcrumb" target="_blank" title="' . __('Google developers website (new window)', 'wp-seopress-pro') . '">
	' . __('Lean more on Google developers website', 'wp-seopress-pro') . '
	</a>
	<span class="seopress-help dashicons dashicons-external"></span>';

	$sections = [
		'local-business' => [
			'toggle' => 1,
			'title'  => __('Local Business', 'wp-seopress-pro'),
			'desc'   => /* translators: %1$s widgets admin URL, %2$s documentation URL */ sprintf(__('Local Business data type for Google. This schema will be displayed on the homepage. <br>You can also display these informations using our <a href="%1$s">Local Business widget</a> or Local Business block to optimize your site for <a class="seopress-help" href="%2$s" target="_blank" title="Optimizing WordPress sites for Google EAT (new window)">Google EAT</a><span class="seopress-help dashicons dashicons-external"></span>.', 'wp-seopress-pro'), esc_url(admin_url('widgets.php')), esc_url($docs['lb']['eat'])),
		],
		'edd' => [
			'toggle' => 1,
			'title'  => __('Easy Digital Downloads', 'wp-seopress-pro'),
			'desc'   => __('Improve Easy Digital Downloads SEO.', 'wp-seopress-pro'),
		],
		'woocommerce' => [
			'toggle' => 1,
			'title'  => __('WooCommerce', 'wp-seopress-pro'),
			'desc'   => __('Improve WooCommerce SEO. By enabling this feature, we‘ll automatically add <strong>product identifiers type</strong> and <strong>product identifiers value</strong> fields to the WooCommerce product metabox (barcode) for the Product schema.', 'wp-seopress-pro'),
			'alert'  => /* translators: %s href + ID attributes */ sprintf(__('We also recommend <a class="nav-tab" %s>adding WooCommerce directives to your robots.txt</a> file to reduce your crawl budget.', 'wp-seopress-pro'), 'id="tab_seopress_robots-tab" href="?page=seopress-pro-page#tab=tab_seopress_robots" style="
			margin: inherit;
            display: inline;
			padding: inherit;
			color: var(--primaryColor);
			opacity: inherit;
			font-size: inherit;
			text-decoration: underline;
			font-weight: bold;
			line-height: inherit;
		"'),
		],
		'alerts' => [
			'toggle' => 1,
			'title'  => __('SEO Alerts', 'wp-seopress-pro'),
			'desc'   => __('Receive alerts by email/Slack about important SEO issues before it‘s too late. We check major problem twice a day.', 'wp-seopress-pro'),
		],
		'dublin-core' => [
			'toggle' => 1,
			'title'  => __('Dublin Core', 'wp-seopress-pro'),
			'desc'   => __('Dublin Core is a set of meta tags to describe your content.<br> These tags are automatically generated. Recognized by states / governements, they are used by directories, Bing, Baidu and Yandex.', 'wp-seopress-pro'),
		],
		'rich-snippets' => [
			'toggle' => 1,
			'title'  => __('Structured Data Types (schema.org)', 'wp-seopress-pro'),
			'desc'   => /* translators: %s documentation URL */ sprintf(__('Add Structured Data Types support, mark your content, and get better Google Search Results. <span class="seopress-help dashicons dashicons-external"></span><a class="seopress-help" href="%s" target="_blank">Learn More</a>', 'wp-seopress-pro'), esc_url($docs['schemas']['ebook'])),
		],
		'page-speed' => [
			'title' => __('PageSpeed Insights', 'wp-seopress-pro'),
			'desc'  => __('Check your site performance with Google PageSpeed Insights.', 'wp-seopress-pro'),
		],
		'inspect-url' => [
			'toggle' => 1,
			'title' => __('Google Search Console', 'wp-seopress-pro'),
			'desc'  => __('Get insights from your post / page / post type list with <strong>clicks, positions, CTR and impressions</strong>. <br>Inspect your URL for details about crawling, indexing, mobile compatibility, schemas and more from the <strong>Content Analysis</strong> metabox / tab. <br>Display the Search Console widget from the SEO dashboard with useful insights.', 'wp-seopress-pro'),
		],
		'robots' => [
			'toggle' => 1,
			'title'  => __('robots.txt', 'wp-seopress-pro'),
			'desc'   => __('Configure your virtual robots.txt file.', 'wp-seopress-pro'),
		],
		'news' => [
			'toggle' => 1,
			'title'  => __('Google News', 'wp-seopress-pro'),
			'desc'   => __('Enable your Google News Sitemap.', 'wp-seopress-pro'),
		],
		'404' => [
			'toggle' => 1,
			'title'  => __('404 monitoring / Redirections', 'wp-seopress-pro'),
			'desc'   => __('Monitor 404 urls in your Dashboard. Crawlers (robots/spiders) will be automatically exclude (e.g. Google Bot, Yahoo, Bing...).', 'wp-seopress-pro'),
		],
		'htaccess' => [
			'title' => __('.htaccess', 'wp-seopress-pro'),
			'desc'  => __('Edit your htaccess file.', 'wp-seopress-pro'),
		],
		'rss' => [
			'title' => __('RSS feeds', 'wp-seopress-pro'),
			'desc'  => /* translators: %s home URL */ sprintf(__('Configure WordPress default feeds. <br><br><a href="%s" class="btn btnTertiary" target="_blank">View my RSS feed</a>', 'wp-seopress-pro'), esc_url(get_home_url() . '/feed')),
		],
		'white-label' => [
			'toggle' => 1,
			'title'  => __('White Label', 'wp-seopress-pro'),
			'desc'   => __('Enable White Label.', 'wp-seopress-pro'),
		],
		'breadcrumbs' => [
			'toggle' => 1,
			'title'  => __('Breadcrumbs', 'wp-seopress-pro'),
			'desc'   => $breadcrumbs_desc,
		],
		'ai' => [
			'toggle' => 1,
			'title'  => __('AI (beta)', 'wp-seopress-pro'),
			'desc'   => __('Use the power of <strong>OpenAI</strong> to improve your productivity.', 'wp-seopress-pro'),
		],
	];

	if (!empty($sections)) {
		if ('1' == seopress_get_toggle_option($key)) {
			$seopress_get_toggle_option = '1';
		} else {
			$seopress_get_toggle_option = '0';
		} ?>
		<div class="sp-section-header">
			<h2>
				<?php echo esc_html($sections[$key]['title']); ?>
			</h2>
			<?php if (!empty($sections[$key]['toggle']) && 1 == $sections[$key]['toggle']) { ?>
				<div class="wrap-toggle-checkboxes">
					<input type="checkbox" name="toggle-<?php echo esc_attr($key); ?>" id="toggle-<?php echo esc_attr($key); ?>" class="toggle" data-toggle="<?php echo absint($seopress_get_toggle_option); ?>">
					<label for="toggle-<?php echo esc_attr($key); ?>"></label>

					<?php
					if ('1' == $seopress_get_toggle_option) { ?>
						<span id="<?php echo esc_attr($key); ?>-state-default" class="feature-state">
							<span class="dashicons dashicons-arrow-left-alt"></span>
							<?php esc_html_e('Click to disable this feature', 'wp-seopress-pro'); ?>
						</span>
						<span id="<?php echo esc_attr($key); ?>-state" class="feature-state feature-state-off">
							<span class="dashicons dashicons-arrow-left-alt"></span>
							<?php esc_html_e('Click to enable this feature', 'wp-seopress-pro'); ?>
						</span>
					<?php } else { ?>
						<span id="<?php echo esc_attr($key); ?>-state-default" class="feature-state">
							<span class="dashicons dashicons-arrow-left-alt"></span>
							<?php esc_html_e('Click to enable this feature', 'wp-seopress-pro'); ?>
						</span>
						<span id="<?php echo esc_attr($key); ?>-state" class="feature-state feature-state-off">
							<span class="dashicons dashicons-arrow-left-alt"></span>
							<?php esc_html_e('Click to disable this feature', 'wp-seopress-pro'); ?>
						</span>
					<?php }
					?>
				</div>
			<?php } ?>
		</div>

		<p><?php echo wp_kses_post($sections[$key]['desc']); ?></p>

		<?php if (isset($sections[$key]['alert'])) { ?>
			<div class="seopress-notice">
				<p><?php echo $sections[$key]['alert']; ?></p>
			</div>
		<?php } ?>
<?php
	}
}
