<?php
defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

$license = defined('SEOPRESS_LICENSE_KEY') && ! empty(SEOPRESS_LICENSE_KEY) && is_string(SEOPRESS_LICENSE_KEY) ? SEOPRESS_LICENSE_KEY : get_option('seopress_pro_license_key');
$selected = $license ? '********************************' : '';
$status = get_option('seopress_pro_license_status');
$docs = function_exists('seopress_get_docs_links') ? seopress_get_docs_links() : '';

if (is_plugin_active('wp-seopress/seopress.php')) {
    if (function_exists('seopress_admin_header')) {
        echo seopress_admin_header();
    }
} ?>

<form class="seopress-option" method="post"
    action="<?php echo admin_url('options.php'); ?>">
    <?php echo $this->feature_title(null); ?>

    <div id="seopress-tabs" class="wrap full-width">
        <div class="seopress-tab active">
            <?php settings_fields('seopress_license'); ?>
            <p>
                <?php esc_html_e('The license key is used to access automatic updates and support.', 'wp-seopress-pro'); ?>
            </p>

            <p>
                <a href="<?php echo $docs['license']['account']; ?>"
                    class="btn btnTertiary" target="_blank">
                    <?php esc_html_e('View my account', 'wp-seopress-pro'); ?>
                </a>
                <button type="button" id="seopress_pro_license_reset" class="btn btnTertiary">
                    <?php esc_html_e('Reset your license', 'wp-seopress-pro'); ?>
                </button>
            </p>

            <div class="seopress-notice">
                <p>
                    <strong><?php esc_html_e('Steps to follow to activate your license:', 'wp-seopress-pro'); ?></strong>
                </p>

                <ol>
                    <li><?php esc_html_e('Paste your license key', 'wp-seopress-pro'); ?>
                    </li>
                    <li><?php esc_html_e('Save changes', 'wp-seopress-pro'); ?>
                    </li>
                    <li><?php esc_html_e('Activate License', 'wp-seopress-pro'); ?>
                    </li>
                </ol>

                <p>
                    <?php esc_html_e('That\'s it!', 'wp-seopress-pro'); ?>
                </p>

                <p>
                    <?php
                        /* translators: %1$s displays the define SEOPRESS_LICENSE_KEY, %2$s documentation URL */
                        echo wp_kses_post(sprintf(__('You can also use the define %1$s to automatically activate your license key. <a href="%2$s" target="_blank" class="seopress-help">Learn more</a>', 'wp-seopress-pro'), '<code>SEOPRESS_LICENSE_KEY</code>', esc_url($docs['license']['license_define'])));
?>
                    <span class="seopress-help dashicons dashicons-external"></span>
                </p>

                <p>
                    <a class="seopress-help"
                        href="<?php echo esc_url($docs['license']['license_errors']); ?>"
                        target="_blank">
                        <?php esc_html_e('Download unauthorized? - Can‘t activate?', 'wp-seopress-pro'); ?>
                    </a>
                    <span class="seopress-help dashicons dashicons-external"></span>
                </p>
            </div>

            <?php if (get_option('seopress_pro_license_key_error')) { ?>
            <div class="seopress-notice is-error">
                <p>
                    <?php echo get_option('seopress_pro_license_key_error'); ?>
                </p>
            </div>
            <?php } ?>

            <table class="form-table" role="presentation">
                <tbody>
                    <tr valign="top">
                        <th scope="row">
                            <?php esc_html_e('License Key', 'wp-seopress-pro'); ?>
                        </th>

                        <td valign="top">
                            <input id="seopress_pro_license_key" name="seopress_pro_license_key" type="text" autocomplete="off" spellcheck="false" autocorrect="off" autocapitalize="off" class="regular-text" value="<?php echo esc_attr($selected); ?>" />
                            <p class="description">
                                <?php esc_html_e('Enter your license key', 'wp-seopress-pro'); ?>
                            </p>
                            <?php if (defined('SEOPRESS_LICENSE_KEY') && ! empty(SEOPRESS_LICENSE_KEY) && is_string(SEOPRESS_LICENSE_KEY)) { ?>
                            <p class="seopress-notice">
                                <?php esc_html_e('Your license key is defined in wp-config.php.', 'wp-seopress-pro'); ?>
                            </p>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php if (false !== $license) { ?>
                    <tr valign="top">
                        <th scope="row">
                            <?php esc_html_e('Activate License', 'wp-seopress-pro'); ?>
                        </th>

                        <td scope="row" valign="top">
                            <?php if (false !== $status && 'valid' == $status) { ?>
                            <div class="seopress-notice is-success">
                                <p>
                                    <?php esc_html_e('active', 'wp-seopress-pro'); ?>
                                </p>
                            </div>
                            <?php wp_nonce_field('seopress_nonce', 'seopress_nonce'); ?>

                            <input id="seopress-edd-license-btn" type="submit" class="btn btnSecondary" name="seopress_license_deactivate" value="<?php esc_html_e('Deactivate License', 'wp-seopress-pro'); ?>" />
                            <div class="spinner"></div>

                            <?php } else {
                                wp_nonce_field('seopress_nonce', 'seopress_nonce'); ?>
                                <input id="seopress-edd-license-btn" type="submit" class="btn btnSecondary" name="seopress_license_activate" value="<?php esc_html_e('Activate License', 'wp-seopress-pro'); ?>" />
                                <div class="spinner"></div>
                            <?php
                            } ?>
                        </td>
                    </tr>

                    <?php if (isset($_GET['sl_activation']) && ! empty($_GET['message'])) { ?>
                    <tr valign="top">
                        <th scope="row">
                            <?php esc_html_e('License status', 'wp-seopress-pro'); ?>
                        </th>

                        <td scope="row" valign="top">
                            <?php
                                switch ($_GET['sl_activation']) {
                                    case 'false':
                                        $message = htmlspecialchars(urldecode($_GET['message']));
                                        ?>
                            <p>
                                <?php echo $message; ?>
                            </p>
                            <div class="seopress-notice">
                                <p>
                                    <?php echo wp_kses_post(__('Click <strong>Reset license</strong> button above, enter your <strong>license key</strong>, <strong>save changes</strong>, and click <strong>Activate</strong>.', 'wp-seopress-pro')); ?>
                                </p>
                                <p>
                                    <?php esc_html_e('Still can\'t activate your license? Please contact us, thank you!', 'wp-seopress-pro'); ?>
                                </p>
                            </div>
                            <?php
                                break;
                            case 'true':
                            default:
                                ?>
                            <div class="seopress-notice is-success">
                                <p><?php esc_html_e('Your license has been successfully activated!', 'wp-seopress-pro'); ?>
                                </p>
                            </div>
                            <?php
        break;
} ?>
                        </td>
                    </tr>
                    <?php }
                    } ?>
                </tbody>
            </table>
            <?php if (false !== $status && 'valid' == $status) {
                //do nothing
            } else {
                sp_submit_button(esc_html__('Save changes', 'wp-seopress-pro'));
            } ?>
        </div>
    </div>
</form>
<?php
    function seopress_sanitize_license($new)
    {
        $old = get_option('seopress_pro_license_key');
        if ($old && $old != $new) {
            delete_option('seopress_pro_license_status'); // new license has been entered, so must reactivate
        }
        if ('********************************' == $new) {
            return $old;
        } else {
            return $new;
        }
    }
