<?php
/**
 * Plugin Name: Custom Brand Logo
 * Description: Replaces WordPress logo with custom brand logo. Adds settings page to admin panel.
 * Version: 1.0
 */

// ===== SETTINGS =====

define( 'CUSTOM_LOGO_OPTION', 'custom_brand_logo_id' );

// ===== REGISTER MENU AND PAGE =====

/**
 * Add settings page under Settings menu
 */
add_action( 'admin_menu', function() {
    add_submenu_page(
        'options-general.php',      // Parent menu
        'Theme Settings',           // Page title
        'Admin Settings',           // Menu title
        'manage_options',           // Capability
        'theme-settings',           // Menu slug
        'custom_logo_settings_page' // Function
    );
} );

/**
 * Settings page callback
 */
function custom_logo_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'You do not have permission to access this page' );
    }
    
    // Save logo
    if ( isset( $_POST['custom_logo_nonce'] ) && wp_verify_nonce( $_POST['custom_logo_nonce'], 'custom_logo_action' ) ) {
        
        // Handle logo upload
        if ( ! empty( $_FILES['custom_logo_file']['name'] ) ) {
            $attachment_id = custom_logo_upload_handler();
            
            if ( $attachment_id ) {
                update_option( CUSTOM_LOGO_OPTION, $attachment_id );
                echo '<div class="notice notice-success is-dismissible"><p>Logo uploaded successfully!</p></div>';
            } else {
                echo '<div class="notice notice-error is-dismissible"><p>Error uploading logo</p></div>';
            }
        }
        
        // Remove logo
        if ( isset( $_POST['remove_logo'] ) ) {
            delete_option( CUSTOM_LOGO_OPTION );
            echo '<div class="notice notice-success is-dismissible"><p>Logo removed!</p></div>';
        }
    }
    
    $logo_id = get_option( CUSTOM_LOGO_OPTION );
    $logo_url = $logo_id ? wp_get_attachment_url( $logo_id ) : '';
    ?>
    
    <div class="wrap">
        <h1>Theme & Logo Settings</h1>
        
        <div style="max-width: 800px; margin-top: 30px;">
            
            <form method="post" enctype="multipart/form-data">
                <?php wp_nonce_field( 'custom_logo_action', 'custom_logo_nonce' ); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="custom_logo_file">Upload Logo</label>
                        </th>
                        <td>
                            <input 
                                type="file" 
                                id="custom_logo_file" 
                                name="custom_logo_file" 
                                accept="image/png,image/jpeg,image/gif,image/svg+xml"
                                style="max-width: 100%;"
                            >
                            <p class="description">
                                Supported formats: PNG, JPG, GIF, SVG<br>
                                Recommended size: 32x32px for icon
                            </p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button( 'Upload Logo', 'primary' ); ?>
            </form>
            
            <?php if ( $logo_url ) : ?>
                <div style="margin-top: 40px; padding: 20px; background: #f5f5f5; border-radius: 5px;">
                    <h2>Current Logo</h2>
                    <div style="margin: 20px 0;">
                        <img src="<?php echo esc_url( $logo_url ); ?>" 
                             style="max-width: 100px; height: auto; border: 1px solid #ddd; padding: 5px;"
                             alt="Logo">
                    </div>
                    
                    <form method="post">
                        <?php wp_nonce_field( 'custom_logo_action', 'custom_logo_nonce' ); ?>
                        <input type="hidden" name="remove_logo" value="1">
                        <?php submit_button( 'Remove Logo', 'delete' ); ?>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php
}

// ===== UPLOAD FUNCTIONS =====

/**
 * Handle logo file upload
 */
function custom_logo_upload_handler() {
    if ( empty( $_FILES['custom_logo_file'] ) ) {
        return false;
    }
    
    // Include WordPress media functions
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    require_once( ABSPATH . 'wp-admin/includes/media.php' );
    
    // Handle upload
    $attachment_id = media_handle_upload( 'custom_logo_file', 0 );
    
    if ( is_wp_error( $attachment_id ) ) {
        return false;
    }
    
    return $attachment_id;
}

// ===== REPLACE LOGO IN ADMIN PANEL =====

/**
 * Replace WordPress logo with custom logo in admin bar - remove default links
 */
add_action( 'admin_bar_menu', function( $wp_admin_bar ) {
    
    $logo_id = get_option( CUSTOM_LOGO_OPTION );
    
    // Remove all default WordPress logo
    $wp_admin_bar->remove_node( 'wp-logo' );
    
    // If custom logo is set, add it as FIRST item
    if ( $logo_id ) {
        $logo_url = wp_get_attachment_url( $logo_id );
        
        if ( $logo_url ) {
            // Add custom logo with unique ID to avoid conflicts
            $wp_admin_bar->add_node( array(
                'id'    => 'wp-logo-custom',
                'title' => '<img src="' . esc_url( $logo_url ) . '" alt="Logo" style="height: 28px; width: auto;">',
                'href'  => admin_url( 'options-general.php?page=theme-settings' ),
            ) );
        }
    }
    
}, 0 );

// ===== CSS FOR LOGO =====

/**
 * Add CSS for logo display - position as first item
 */
add_action( 'wp_head', function() {
    ?>
    <style>
        /* Position custom logo as FIRST item with extreme negative order */
        #wp-admin-bar-wp-logo-custom {
            order: -9999999 !important;
        }
        
        #wp-admin-bar-wp-logo-custom > a {
            padding: 0 6px !important;
            display: flex;
            align-items: center;
            height: 32px;
        }
        
        #wp-admin-bar-wp-logo-custom img {
            height: 28px !important;
            width: auto !important;
            display: block;
        }
        
        #wp-admin-bar-wp-logo-custom:hover > a {
            background-color: rgba(255, 255, 255, 0.1);
        }
    </style>
    <?php
}, 999 );

// ===== CLEANUP ON LOGO DELETE =====

/**
 * Clean option if logo is deleted from media library
 */
add_action( 'delete_attachment', function( $attachment_id ) {
    $logo_id = get_option( CUSTOM_LOGO_OPTION );
    
    if ( $logo_id == $attachment_id ) {
        delete_option( CUSTOM_LOGO_OPTION );
    }
} );