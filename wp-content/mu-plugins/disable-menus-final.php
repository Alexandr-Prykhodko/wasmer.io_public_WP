<?php
/**
 * Plugin Name: Disable Admin Menu Items
 * Description: Отключает определенные пункты меню в админ-панели WordPress
 */

add_action('admin_menu', 'disable_admin_menu_items', 999);

function disable_admin_menu_items() {
    // Удаляем подменю в Settings
    remove_submenu_page('options-general.php', 'options-privacy.php'); // Privacy
    remove_submenu_page('options-general.php', 'options-discussion.php'); // Discussion
    
    // Удаляем подменю в Tools
    remove_submenu_page('tools.php', 'tools.php'); // Available Tools
    remove_submenu_page('tools.php', 'import.php'); // Import
    remove_submenu_page('tools.php', 'export.php'); // Export
    remove_submenu_page('tools.php', 'site-health.php'); // Site Health
    remove_submenu_page('tools.php', 'export-personal-data.php'); // Export Personal Data
    remove_submenu_page('tools.php', 'erase-personal-data.php'); // Erase Personal Data
    
    // Удаляем редакторы
    remove_submenu_page('themes.php', 'theme-editor.php'); // Theme File Editor
    remove_submenu_page('themes.php', 'plugin-editor.php'); // Plugin File Editor
    
    // Удаляем Site Editor (новый редактор)
    remove_submenu_page('themes.php', 'site-editor.php'); // Site Editor
    
    // Удаляем Customize из Appearance
    remove_submenu_page('themes.php', 'customize.php'); // Customize
}

// Блокируем прямой доступ к отключенным страницам
add_action('admin_init', 'block_disabled_pages_access');

function block_disabled_pages_access() {
    global $pagenow;
    
    $blocked_pages = array(
        'site-editor.php',
        'theme-editor.php',
        'plugin-editor.php',
        'site-health.php',
        'options-privacy.php',
        'options-discussion.php',
        'export.php',
        'import.php',
        'export-personal-data.php',
        'erase-personal-data.php',
        'customize.php' // Блокируем Customizer
    );
    
    if (in_array($pagenow, $blocked_pages)) {
        wp_die('Access to this page is disabled.', 'Access Denied', array('response' => 403));
    }
    
    // Дополнительная проверка для tools.php (Available Tools)
    if ($pagenow === 'tools.php' && !isset($_GET['page'])) {
        wp_die('Access to this page is disabled.', 'Access Denied', array('response' => 403));
    }
}

// Отключаем Block Editor (Gutenberg) и включаем классический редактор
add_filter('use_block_editor_for_post', '__return_false', 10);
add_filter('use_block_editor_for_post_type', '__return_false', 10);

// Отключаем FSE (Full Site Editing) и включаем классическое управление темой
add_filter('gutenberg_use_widgets_block_editor', '__return_false');
add_filter('use_widgets_block_editor', '__return_false');

// Включаем классическое управление меню
add_action('after_setup_theme', 'enable_classic_menus');

function enable_classic_menus() {
    // Регистрируем поддержку классических меню
    add_theme_support('menus');
    
    // Отключаем навигационные блоки (если тема их использует)
    remove_theme_support('block-template-parts');
}

// Добавляем классическую страницу Menus в Appearance
add_action('admin_menu', 'restore_classic_menus_page');

function restore_classic_menus_page() {
    // Если страница меню не существует, добавляем её
    if (!current_theme_supports('menus')) {
        add_theme_support('menus');
    }
}

// Полностью отключаем Customizer
add_action('customize_register', 'disable_customizer', 999);

function disable_customizer($wp_customize) {
    // Удаляем все панели, секции и контролы
    $wp_customize->remove_panel('nav_menus');
    $wp_customize->remove_panel('widgets');
    
    // Можно также полностью очистить customizer
    foreach ($wp_customize->panels() as $panel => $panel_obj) {
        $wp_customize->remove_panel($panel);
    }
    foreach ($wp_customize->sections() as $section => $section_obj) {
        $wp_customize->remove_section($section);
    }
}

// Удаляем ссылки на Customizer из админ-бара
add_action('admin_bar_menu', 'remove_customizer_from_admin_bar', 999);

function remove_customizer_from_admin_bar($wp_admin_bar) {
    $wp_admin_bar->remove_node('customize');
    $wp_admin_bar->remove_node('site-editor');
    $wp_admin_bar->remove_node('site-health');
}

// Кастомные CSS стили для админки
add_action('admin_head', 'custom_admin_styles');

function custom_admin_styles() {
    echo '<style>
        /* Меняем фон админки на белый */
        body.wp-admin,
        #wpcontent,
        #wpbody,
        #wpbody-content {
            background: #ffffff !important;
        }
        
        /* Фон для wrap контейнеров */
        .wrap {
            background: #ffffff !important;
        }
        
        /* Скрываем кнопку Customize на странице тем */
        .theme-actions .button.load-customize,
        .theme-browser .theme .theme-actions .button.load-customize {
            display: none !important;
        }
        
        /* Скрываем "Manage with Live Preview" на странице меню */
        .nav-menus-php .hide-if-no-customize,
        .nav-menus-php #customize-control,
        .nav-menus-php .customize-support {
            display: none !important;
        }
        
        /* Скрываем вкладку и ссылки на Customizer */
        #menu-appearance a[href*="customize.php"],
        a.hide-if-no-customize {
            display: none !important;
        }
    </style>';
}

// Отключаем скрипты Customizer на странице меню
add_action('admin_enqueue_scripts', 'disable_customizer_scripts');

function disable_customizer_scripts($hook) {
    if ($hook === 'nav-menus.php') {
        // Отключаем customize support на странице меню
        add_filter('customize_nav_menu_available_item_types', '__return_empty_array');
    }
}

// Удаляем customize support из темы
add_action('after_setup_theme', 'remove_customize_support', 100);

function remove_customize_support() {
    remove_theme_support('customize-selective-refresh-widgets');
}

// Отключаем возможность редактирования файлов через константу
if (!defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', true);
}

// Отключаем тему FSE (Full Site Editing)
add_filter('theme_file_path', 'disable_fse_theme', 10, 2);

function disable_fse_theme($path, $file) {
    // Если тема пытается загрузить templates или parts для FSE, блокируем
    if (strpos($file, 'templates/') === 0 || strpos($file, 'parts/') === 0) {
        return false;
    }
    return $path;
}