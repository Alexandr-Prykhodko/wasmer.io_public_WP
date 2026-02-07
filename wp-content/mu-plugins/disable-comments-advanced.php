<?php
/**
 * Plugin Name: Disable Comments & Discussions
 * Description: Полностью отключает комментарии, обсуждения и связанную функциональность
 * Version: 1.0
 * 
 * Что отключает:
 * - Комментарии на фронтенде и в админ-панели
 * - Обсуждения (Pingbacks, Trackbacks)
 * - REST API для комментариев
 * - Email уведомления о комментариях
 * - Меню комментариев в админ-панели
 * - Metabox комментариев на страницах редактирования
 */

// ===== КОНФИГУРАЦИЯ =====

// Какие типы постов затронуть (пусто = все)
$disabled_comment_post_types = array();
// $disabled_comment_post_types = array( 'post', 'page' ); // Пример

// ===== ФРОНТЕНД - ОТКЛЮЧЕНИЕ КОММЕНТАРИЕВ =====

/**
 * Удаляем поддержку комментариев из типов постов
 */
add_action( 'wp_loaded', function() {
    global $disabled_comment_post_types;
    
    if ( empty( $disabled_comment_post_types ) ) {
        $post_types = get_post_types( array( 'public' => true ), 'names' );
    } else {
        $post_types = $disabled_comment_post_types;
    }
    
    foreach ( $post_types as $post_type ) {
        if ( post_type_exists( $post_type ) ) {
            remove_post_type_support( $post_type, 'comments' );
            remove_post_type_support( $post_type, 'trackbacks' );
        }
    }
} );

/**
 * Отключаем открытие комментариев и пингбеков
 */
add_filter( 'comments_open', '__return_false' );
add_filter( 'pings_open', '__return_false' );

/**
 * Скрываем форму и список комментариев на фронтенде
 */
add_action( 'wp_head', function() {
    ?>
    <style>
        /* Скрываем блоки комментариев */
        #comments,
        #respond,
        .comments-area,
        .comment-form,
        .commentlist,
        .comments,
        .comment-respond,
        .post-comments {
            display: none !important;
        }
        
        /* Убедимся что они невидимы */
        .discussion-closed,
        .no-comments {
            display: none !important;
        }
    </style>
    <?php
} );

/**
 * Возвращаем пустой массив комментариев
 */
add_filter( 'comments_array', function( $comments, $post_id ) {
    return array();
}, 10, 2 );

/**
 * Отключаем пингбеки
 */
add_filter( 'pre_ping', '__return_false' );

// ===== АДМИН-ПАНЕЛЬ - МЕНЮ И ИНТЕРФЕЙС =====

/**
 * Удаляем меню "Комментарии" из админ-панели
 */
add_action( 'admin_menu', function() {
    remove_menu_page( 'edit-comments.php' );
} );

/**
 * Удаляем уведомление о комментариях из админ-бара
 */
add_action( 'admin_bar_menu', function( $wp_admin_bar ) {
    $wp_admin_bar->remove_node( 'comments' );
}, 999 );

/**
 * Удаляем метабокс комментариев из страниц редактирования постов
 */
add_action( 'add_meta_boxes', function() {
    $screens = array( 'post', 'page' );
    
    foreach ( $screens as $screen ) {
        remove_meta_box( 'commentsdiv', $screen, 'normal' );
        remove_meta_box( 'commentstatusdiv', $screen, 'normal' );
        remove_meta_box( 'trackbacksdiv', $screen, 'normal' );
    }
} );

/**
 * Скрываем опции комментариев в админ-интерфейсе
 */
add_action( 'admin_head', function() {
    ?>
    <style>
        /* Скрываем меню комментариев */
        #menu-comments {
            display: none !important;
        }
        
        /* Скрываем счетчик комментариев */
        .awaiting-mod,
        .comment-count {
            display: none !important;
        }
        
        /* Скрываем опции обсуждений в настройках */
        .discussion-settings {
            display: none !important;
        }
        
        /* Скрываем related comments */
        .postbox #comments,
        .discussion-settings,
        .screen-options {
            display: none !important;
        }
    </style>
    <?php
} );

// ===== REST API - ОТКЛЮЧЕНИЕ =====

/**
 * Отключаем REST API для комментариев
 */
add_filter( 'rest_endpoints', function( $endpoints ) {
    if ( isset( $endpoints['/wp/v2/comments'] ) ) {
        unset( $endpoints['/wp/v2/comments'] );
    }
    if ( isset( $endpoints['/wp/v2/comments/(?P<id>[\d]+)'] ) ) {
        unset( $endpoints['/wp/v2/comments/(?P<id>[\d]+)'] );
    }
    return $endpoints;
} );

/**
 * Отключаем REST поддержку комментариев через register_rest_route
 */
add_action( 'rest_api_init', function() {
    // Удаляем маршруты комментариев
    if ( isset( $GLOBALS['wp_rest_server'] ) ) {
        $routes = $GLOBALS['wp_rest_server']->get_routes();
        
        foreach ( $routes as $route => $handlers ) {
            if ( strpos( $route, 'comments' ) !== false ) {
                unset( $routes[ $route ] );
            }
        }
    }
} );

// ===== EMAIL УВЕДОМЛЕНИЯ =====

/**
 * Email уведомления ОСТАВЛЕНЫ включенными
 * (удалены фильтры которые их отключали)
 */

// ===== СЧЕТ КОММЕНТАРИЕВ =====

/**
 * Возвращаем нулевой счет комментариев
 */
add_filter( 'wp_count_comments', function( $count ) {
    $count = new stdClass();
    $count->total_comments = 0;
    $count->all = 0;
    $count->moderated = 0;
    $count->approved = 0;
    $count->spam = 0;
    $count->trash = 0;
    
    return $count;
} );

// ===== БЕЗОПАСНОСТЬ - XMLRPC =====

/**
 * Отключаем pingback через XML-RPC
 */
add_action( 'xmlrpc_call', function( $method ) {
    if ( in_array( $method, array( 'pingback.ping', 'pingback.extensions.getPingbacks' ) ) ) {
        wp_die( 'Pingbacks are disabled', 'Pingbacks Disabled', array( 'response' => 403 ) );
    }
} );

/**
 * Удаляем pingback header
 */
add_filter( 'wp_headers', function( $headers ) {
    unset( $headers['X-Pingback'] );
    return $headers;
} );

// ===== БАЗА ДАННЫХ - МОНИТОРИНГ =====

/**
 * Предотвращаем создание новых комментариев
 */
add_filter( 'pre_comment_approved', function( $approved ) {
    return 'spam'; // Отправляем в спам
} );

add_filter( 'pre_comment_approved', function( $approved, $commentdata ) {
    // Лучше просто отклоняем комментарии
    return false;
}, 10, 2 );

// ===== ОПЦИОНАЛЬНАЯ ОЧИСТКА БАЗЫ ДАННЫХ =====

/**
 * Удаляем старые комментарии (раскомментируйте если нужно)
 */
// add_action( 'init', function() {
//     if ( ! get_option( 'comments_deleted_via_mu_plugin' ) ) {
//         global $wpdb;
//         
//         // Удаляем все комментарии
//         $wpdb->query( "DELETE FROM $wpdb->comments" );
//         
//         // Удаляем метаданные комментариев
//         $wpdb->query( "DELETE FROM $wpdb->commentmeta" );
//         
//         // Отмечаем что очистка сделана
//         update_option( 'comments_deleted_via_mu_plugin', '1' );
//     }
// } );

// ===== ОТКЛЮЧЕНИЕ В НАСТРОЙКАХ =====

/**
 * Скрываем опцию "Разрешить людям оставлять комментарии"
 */
add_action( 'admin_init', function() {
    // Для страницы обсуждений
    if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] === 'options-discussion' ) {
        add_filter( 'option_default_comment_status', '__return_false' );
    }
} );

// ===== ВСПОМОГАТЕЛЬНАЯ ФУНКЦИЯ =====

/**
 * Функция для очистки комментариев из темы
 */
if ( ! function_exists( 'is_comments_enabled' ) ) {
    function is_comments_enabled() {
        return false;
    }
}