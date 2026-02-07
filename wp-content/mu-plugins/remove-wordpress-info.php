<?php
/**
 * Plugin Name: Remove WordPress Logo Menu & Footer
 * Description: Удаляет WordPress logo меню и footer из админ-панели
 * Version: 1.0
 */

/**
 * Удаляем WordPress logo меню из админ-панели
 */
add_action( 'admin_bar_menu', function( $wp_admin_bar ) {
    // Удаляем wp-logo node которое содержит это меню
    $wp_admin_bar->remove_node( 'wp-logo' );
}, 999 );

/**
 * Удаляем footer из админ-панели
 */
add_filter( 'admin_footer_text', '__return_empty_string' );
add_filter( 'update_footer', '__return_empty_string' );

/**
 * Скрываем footer через CSS на случай если фильтры не сработают
 */
add_action( 'admin_head', function() {
    ?>
    <style>
        #wpfooter {
            display: none !important;
        }
    </style>
    <?php
} );