<?php
// functions.php

if ( function_exists('acf_add_options_page') ) {

    // 1. Главная страница "Global Options"
    $parent = acf_add_options_page(array(
        'page_title'    => 'Global Options',
        'menu_title'    => 'Global Options',
        'menu_slug'     => 'global-options',
        'capability'    => 'edit_posts',
        'redirect'      => false, // false = у этой страницы есть свои поля (не только подстраницы)
        'icon_url'      => 'dashicons-admin-world', // Иконка глобуса
        'position'      => 2, // Сразу после Dashboard
        'show_in_rest'  => true, // Важно для Headless!
    ));
    
    // Если полей ОЧЕНЬ много, иногда делают подстраницы, 
    // но в вашем случае лучше использовать Вкладки (Tabs) внутри одной страницы,
    // чтобы отдавать всё одним JSON-объектом.
}


// Указываем путь для сохранения JSON
add_filter('acf/settings/save_json', 'my_acf_json_save_point');
function my_acf_json_save_point( $path ) {
    // Сохраняем в папку acf-json внутри папки этого mu-плагина
    $path = dirname(__FILE__) . '/acf-json';
    
    // Создадим папку, если её нет (работает при наличии прав)
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }
    return $path;
}

// Указываем путь для загрузки JSON (чтобы ACF видел их)
add_filter('acf/settings/load_json', 'my_acf_json_load_point');
function my_acf_json_load_point( $paths ) {
    $paths[] = dirname(__FILE__) . '/acf-json';
    return $paths;
}