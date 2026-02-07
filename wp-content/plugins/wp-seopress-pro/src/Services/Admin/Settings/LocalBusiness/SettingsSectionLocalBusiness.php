<?php

namespace SEOPressPro\Services\Admin\Settings\LocalBusiness;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPressPro\Helpers\Settings\LocalBusinessHelper;
use SEOPressPro\Services\Admin\Settings\LocalBusiness\Fields\FieldAddressCountry;
use SEOPressPro\Services\Admin\Settings\LocalBusiness\Fields\FieldAddressLocality;
use SEOPressPro\Services\Admin\Settings\LocalBusiness\Fields\FieldAddressRegion;
use SEOPressPro\Services\Admin\Settings\LocalBusiness\Fields\FieldCuisine;
use SEOPressPro\Services\Admin\Settings\LocalBusiness\Fields\FieldLatitude;
use SEOPressPro\Services\Admin\Settings\LocalBusiness\Fields\FieldLongitude;
use SEOPressPro\Services\Admin\Settings\LocalBusiness\Fields\FieldOpeningHours;
use SEOPressPro\Services\Admin\Settings\LocalBusiness\Fields\FieldPage;
use SEOPressPro\Services\Admin\Settings\LocalBusiness\Fields\FieldPhone;
use SEOPressPro\Services\Admin\Settings\LocalBusiness\Fields\FieldPlaceId;
use SEOPressPro\Services\Admin\Settings\LocalBusiness\Fields\FieldPostalCode;
use SEOPressPro\Services\Admin\Settings\LocalBusiness\Fields\FieldPriceRange;
use SEOPressPro\Services\Admin\Settings\LocalBusiness\Fields\FieldStreetAddress;
use SEOPressPro\Services\Admin\Settings\LocalBusiness\Fields\FieldType;
use SEOPressPro\Services\Admin\Settings\LocalBusiness\Fields\FieldUrl;
use SEOPressPro\Services\Admin\Settings\LocalBusiness\Fields\FieldMenu;
use SEOPressPro\Services\Admin\Settings\LocalBusiness\Fields\FieldAcceptsReservations;

class SettingsSectionLocalBusiness {
    use FieldPage;
    use FieldType;
    use FieldStreetAddress;
    use FieldLatitude;
    use FieldLongitude;
    use FieldAddressCountry;
    use FieldAddressLocality;
    use FieldAddressRegion;
    use FieldPostalCode;
    use FieldUrl;
    use FieldPlaceId;
    use FieldPhone;
    use FieldPriceRange;
    use FieldCuisine;
    use FieldOpeningHours;
    use FieldMenu;
    use FieldAcceptsReservations;

    public function __call($name, $params) {
        do_action('seopress_pro_render_field_local_business', $name);
    }

    /**
     * @since 4.5.0
     *
     * @return void
     */
    public function renderSettings() {
        $settings = LocalBusinessHelper::getSettingsSection($this);

        if ( ! isset($settings['section']) ||
            ! isset($settings['section']['id'],  $settings['section']['title'],  $settings['section']['callback'],  $settings['section']['page'])) {
            return;
        }

        add_settings_section(
            $settings['section']['id'],
            $settings['section']['title'],
            $settings['section']['callback'],
            $settings['section']['page']
        );

        if ( ! isset($settings['fields']) || empty($settings['fields'])) {
            return;
        }

        foreach ($settings['fields'] as $key => $field) {
            if ( ! isset($field['id'], $field['title'], $field['callback'], $field['page'], $field['section'])) {
                continue;
            }

            add_settings_field(
                $field['id'],
                $field['title'],
                $field['callback'],
                $field['page'],
                $field['section']
            );
        }
    }

    /**
     * @since 4.5.0
     *
     * @return void
     */
    public function renderSection() {
        seopress_pro_get_service('RenderSectionPro')->render('local-business');
        $imgOption = seopress_get_service('SocialOption')->getSocialKnowledgeImage();

        if (empty($imgOption)) {
            ?>
<div class="seopress-notice is-error">
    <p>
        <?php esc_html_e('You have to set an image in Knowledge Graph settings, otherwise, your Google Local Business data will not be valid.', 'wp-seopress-pro'); ?>
        <a href="<?php echo esc_url(admin_url('admin.php?page=seopress-social')); ?>"
            class="btn btnPrimary">
            <?php esc_html_e('Fix this!', 'wp-seopress-pro'); ?>
        </a>
    </p>
</div>
<?php
        } ?>
<p>
    <a
        href="<?php echo esc_url(admin_url('admin.php?page=seopress-social#tab=tab_seopress_social_knowledge')); ?>">
        <?php esc_html_e('To edit your business name, visit this page.', 'wp-seopress-pro'); ?>
    </a>
</p>

<p class="seopress-notice">
    <?php echo wp_kses_post(sprintf(__('<strong>Need to add multiple locations?</strong> Create a manual Local Business schema from the schema metabox when you edit a post, page or post type, or an <a href="%s">automatic Local Business schema</a>.', 'wp-seopress-pro'), esc_url(admin_url('edit.php?post_type=seopress_schemas')))); ?>
</p>

<?php
    }
}
