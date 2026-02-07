<?php
/**
 * Plugin Name: Disable Theme Frontend
 * Description: Отключает отображение контента через тему WordPress. Показывает заглушку или пустую страницу.
 * Version: 1.0
 */

// ===== КОНФИГУРАЦИЯ =====

/**
 * Выберите режим отображения:
 * 'empty' - полностью пустая страница
 * 'message' - сообщение что сайт находится в разработке
 * 'json' - JSON ответ с информацией о странице (полезно для разработки)
 */
$display_mode = 'json';

// ===== ОСНОВНОЙ КОД =====

/**
 * Отключаем отображение темы и показываем заглушку
 */
add_action( 'template_redirect', function() use ( $display_mode ) {
    // Проверяем админ-панель - её пропускаем
    if ( is_admin() ) {
        return;
    }
    
    // Если это запрос к REST API - пропускаем
    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
        return;
    }
    
    // Выбираем режим отображения
    switch ( $display_mode ) {
        case 'empty':
            // Полностью пустая страница
            exit;
            break;
            
        case 'message':
            // Сообщение о разработке
            header( 'Content-Type: text/html; charset=utf-8' );
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title>Сайт в разработке</title>
                <style>
                    * { margin: 0; padding: 0; }
                    body {
                        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        min-height: 100vh;
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    }
                    .container {
                        text-align: center;
                        background: white;
                        padding: 40px;
                        border-radius: 10px;
                        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                    }
                    h1 {
                        color: #333;
                        margin-bottom: 10px;
                    }
                    p {
                        color: #666;
                        font-size: 16px;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1>🚀 Сайт в разработке</h1>
                    <p>Контент будет доступен скоро</p>
                </div>
            </body>
            </html>
            <?php
            exit;
            break;
            
        case 'json':
            // JSON ответ с информацией о странице (для разработки API)
            header( 'Content-Type: application/json; charset=utf-8' );
            
            global $wp_query;
            
            $response = array(
                'status' => 'disabled',
                'message' => 'Theme frontend display is disabled',
                'page_info' => array(
                    'url' => $_SERVER['REQUEST_URI'] ?? '',
                    'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
                    'is_front_page' => is_front_page(),
                    'is_single' => is_single(),
                    'is_page' => is_page(),
                    'is_archive' => is_archive(),
                    'is_home' => is_home(),
                ),
                'post_data' => array(),
            );
            
            // Добавляем информацию о посте если есть
            if ( is_singular() && have_posts() ) {
                the_post();
                $response['post_data'] = array(
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'type' => get_post_type(),
                    'url' => get_the_permalink(),
                );
            }
            
            echo json_encode( $response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
            exit;
            break;
    }
}, 0 );

/**
 * Отключаем загрузку стилей и скриптов темы (опционально)
 */
add_action( 'wp_enqueue_scripts', function() {
    // Удаляем все стили и скрипты темы кроме критических
    global $wp_styles, $wp_scripts;
    
    if ( isset( $wp_styles ) ) {
        $wp_styles->queue = array();
    }
    
    if ( isset( $wp_scripts ) ) {
        $wp_scripts->queue = array();
    }
}, 999 );

/**
 * Убираем wp-head вывод
 */
remove_action( 'wp_head', 'wp_print_styles' );
remove_action( 'wp_head', 'wp_print_scripts' );
remove_action( 'wp_head', 'wp_enqueue_scripts' );