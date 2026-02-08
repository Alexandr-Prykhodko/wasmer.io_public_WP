<!-- <?php
/**
 * Plugin Name: Disable Default Editor with ACF Support
 * Description: Отключает стандартный редактор и оставляет только ACF поля с редактором
 * Version: 1.0
 * 
 * Использование:
 * Просто загрузите файл в папку wp-content/mu-plugins/
 */

// ===== КОНФИГУРАЦИЯ =====
// Отключить для этих типов постов (пусто = для всех)
$disabled_post_types = array();
// $disabled_post_types = array( 'page', 'post' ); // Пример для конкретных типов

// ===== ОСНОВНОЙ КОД =====

/**
 * Отключаем Block Editor (Gutenberg)
 */
add_filter( 'use_block_editor_for_post_type', function( $use_block_editor, $post_type ) {
    global $disabled_post_types;
    
    if ( empty( $disabled_post_types ) || in_array( $post_type, $disabled_post_types ) ) {
        return false;
    }
    
    return $use_block_editor;
}, 10, 2 );

add_filter( 'use_block_editor_for_post', '__return_false', 10 );

/**
 * Отключаем Classic Editor плагин если установлен
 */
add_filter( 'classic_editor_enabled_editors_for_post_type', function( $editors, $post_type ) {
    global $disabled_post_types;
    
    if ( empty( $disabled_post_types ) || in_array( $post_type, $disabled_post_types ) ) {
        return array( 'classic' => false, 'block' => false );
    }
    
    return $editors;
}, 10, 2 );

/**
 * Удаляем поддержку редактора для типов постов (альтернативный метод)
 */
add_action( 'init', function() {
    global $disabled_post_types;
    
    if ( empty( $disabled_post_types ) ) {
        $post_types = get_post_types( array( 'public' => true ), 'names' );
    } else {
        $post_types = $disabled_post_types;
    }
    
    foreach ( $post_types as $post_type ) {
        if ( post_type_exists( $post_type ) ) {
            remove_post_type_support( $post_type, 'editor' );
        }
    }
}, 9 );
 -->
