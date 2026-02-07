<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

?>

<div class="wrap-rich-snippets-custom">
	<p>
		<label for="seopress_pro_rich_snippets_custom_meta">
			<?php esc_html_e('Custom schema', 'wp-seopress-pro'); ?>
		</label>
		<?php echo seopress_schemas_mapping_array('seopress_pro_rich_snippets_custom', 'custom'); ?>
	</p>

	<p class="description">
		<?php esc_html_e('⚠ Make sure to open and close the script tag.', 'wp-seopress-pro'); ?>
	</p>
	<p class="description">
		<span class="seopress-help dashicons dashicons-external"></span>
		<?php
			/* translators: %s link documentation */
			echo wp_kses_post(sprintf(__('<a href="%s" target="_blank">You can use dynamic variables in your schema.</a>', 'wp-seopress-pro'), esc_url($docs['schemas']['dynamic'])));
		?>
	</p>
</div>
