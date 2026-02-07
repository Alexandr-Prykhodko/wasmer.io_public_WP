<?php
/**
 * Plugin Name: Custom User Avatar
 * Description: Upload custom avatar image for users
 * Version: 1.3
 */

// Добавляем enctype к форме профиля
add_action( 'user_edit_form_tag', 'add_enctype_to_profile_form' );
function add_enctype_to_profile_form() {
    echo ' enctype="multipart/form-data"';
}

// Удаляем стандартные секции профиля
add_filter( 'user_profile_picture_description', '__return_empty_string', 99 );

// Скрываем выбор цветовой схемы администратора
remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );

add_action( 'admin_head-profile.php', 'hide_profile_sections' );
add_action( 'admin_head-user-edit.php', 'hide_profile_sections' );

function hide_profile_sections() {
    ?>
    <style>
        /* Скрываем стандартный блок Profile Picture от Gravatar */
        .user-profile-picture,
        h2:has(+ .user-profile-picture),
        h3:has(+ .user-profile-picture) {
            display: none !important;
        }
        
        /* Скрываем Administration Color Scheme */
        .user-admin-color-wrap,
        h2:has(+ .user-admin-color-wrap),
        h3:has(+ .user-admin-color-wrap) {
            display: none !important;
        }
        
        /* Скрываем Keyboard Shortcuts */
        .user-comment-shortcuts-wrap,
        tr.user-comment-shortcuts-wrap {
            display: none !important;
        }
        
        /* Альтернативный способ - по тексту */
        tr.user-profile-picture-wrap,
        tr.user-admin-color-wrap {
            display: none !important;
        }
    </style>
    <?php
}

add_action( 'show_user_profile', 'show_custom_avatar_field' );
add_action( 'edit_user_profile', 'show_custom_avatar_field' );

function show_custom_avatar_field( $user ) {
    $avatar_url = get_user_meta( $user->ID, 'user_avatar_url', true );
    ?>
    <h3>Profile Picture</h3>
    <table class="form-table">
        <tr>
            <th><label for="user_avatar">Your Avatar</label></th>
            <td>
                <div id="avatar-preview" style="margin-bottom: 15px;">
                    <?php if ( $avatar_url ) : ?>
                        <img id="preview-img" src="<?php echo esc_url( $avatar_url ); ?>" 
                             style="width: 150px; height: 150px; border-radius: 10px; object-fit: cover; border: 2px solid #ddd;">
                    <?php else : ?>
                        <div id="preview-empty" style="width: 150px; height: 150px; background: #f0f0f0; border-radius: 10px; border: 2px dashed #ddd; display: flex; align-items: center; justify-content: center; color: #999;">
                            No Avatar
                        </div>
                    <?php endif; ?>
                </div>
                <input type="file" name="user_avatar" id="user_avatar" accept="image/*">
                <p class="description">Upload a square image (400x400px recommended)</p>
            </td>
        </tr>
    </table>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('user_avatar');
            if (!fileInput) return;
            
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;
                
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById('avatar-preview');
                    const previewImg = document.getElementById('preview-img');
                    const previewEmpty = document.getElementById('preview-empty');
                    
                    if (previewEmpty) {
                        previewEmpty.remove();
                    }
                    
                    if (!previewImg) {
                        const img = document.createElement('img');
                        img.id = 'preview-img';
                        img.src = event.target.result;
                        img.style.cssText = 'width: 150px; height: 150px; border-radius: 10px; object-fit: cover; border: 2px solid #667eea;';
                        preview.appendChild(img);
                    } else {
                        previewImg.src = event.target.result;
                    }
                };
                reader.readAsDataURL(file);
            });
        });
    </script>
    <?php
}

add_action( 'personal_options_update', 'save_custom_avatar' );
add_action( 'edit_user_profile_update', 'save_custom_avatar' );

function save_custom_avatar( $user_id ) {
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }
    
    // Проверка nonce для безопасности
    if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-user_' . $user_id ) ) {
        return false;
    }
    
    // Проверяем наличие файла
    if ( ! isset( $_FILES['user_avatar'] ) || empty( $_FILES['user_avatar']['name'] ) ) {
        return true;
    }
    
    if ( $_FILES['user_avatar']['error'] !== UPLOAD_ERR_OK ) {
        add_action( 'user_profile_update_errors', function( $errors ) {
            $errors->add( 'avatar_upload_error', 'Error uploading avatar. Please try again.' );
        });
        return false;
    }
    
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    
    $file = $_FILES['user_avatar'];
    
    // Проверка типа файла
    $allowed_types = array( 'image/jpeg', 'image/png', 'image/gif', 'image/webp' );
    $file_type = wp_check_filetype( $file['name'] );
    
    if ( ! in_array( $file['type'], $allowed_types ) ) {
        add_action( 'user_profile_update_errors', function( $errors ) {
            $errors->add( 'avatar_type_error', 'Only JPG, PNG, GIF, and WebP images are allowed.' );
        });
        return false;
    }
    
    // Удаляем старый аватар
    $old_avatar = get_user_meta( $user_id, 'user_avatar_url', true );
    if ( $old_avatar ) {
        $upload_dir = wp_upload_dir();
        $file_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $old_avatar );
        if ( file_exists( $file_path ) ) {
            @unlink( $file_path );
        }
    }
    
    // Загружаем новый файл
    $upload_overrides = array( 
        'test_form' => false,
        'unique_filename_callback' => function( $dir, $name, $ext ) use ( $user_id ) {
            return 'avatar-' . $user_id . '-' . time() . $ext;
        }
    );
    
    $uploaded_file = wp_handle_upload( $file, $upload_overrides );
    
    if ( isset( $uploaded_file['error'] ) ) {
        add_action( 'user_profile_update_errors', function( $errors ) use ( $uploaded_file ) {
            $errors->add( 'avatar_upload_error', $uploaded_file['error'] );
        });
        return false;
    }
    
    // Сохраняем URL аватара
    update_user_meta( $user_id, 'user_avatar_url', $uploaded_file['url'] );
    
    return true;
}

add_filter( 'pre_get_avatar_data', 'filter_user_avatar', 10, 2 );

function filter_user_avatar( $args, $id_or_email ) {
    $user = false;
    
    if ( is_numeric( $id_or_email ) ) {
        $user = get_user_by( 'id', (int) $id_or_email );
    } elseif ( is_string( $id_or_email ) ) {
        $user = get_user_by( 'email', $id_or_email );
    } elseif ( is_object( $id_or_email ) ) {
        if ( property_exists( $id_or_email, 'user_id' ) ) {
            $user = get_user_by( 'id', (int) $id_or_email->user_id );
        } elseif ( property_exists( $id_or_email, 'ID' ) ) {
            $user = $id_or_email;
        }
    }
    
    if ( ! $user ) {
        return $args;
    }
    
    $avatar_url = get_user_meta( $user->ID, 'user_avatar_url', true );
    
    if ( $avatar_url ) {
        $args['url'] = $avatar_url;
        $args['found_avatar'] = true;
    }
    
    return $args;
}

add_action( 'delete_user', function( $user_id ) {
    $avatar_url = get_user_meta( $user_id, 'user_avatar_url', true );
    if ( $avatar_url ) {
        $upload_dir = wp_upload_dir();
        $file_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $avatar_url );
        if ( file_exists( $file_path ) ) {
            @unlink( $file_path );
        }
    }
    delete_user_meta( $user_id, 'user_avatar_url' );
});