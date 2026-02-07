<?php

namespace luckywp\acfMenuField\admin;

use luckywp\acfMenuField\core\base\BaseObject;
use luckywp\acfMenuField\core\Core;
use WP_Plugin_Install_List_Table;

class Plugins extends BaseObject
{
    public function init()
    {
        // Ссылки в списке плагинов
        add_filter('plugin_action_links_' . Core::$plugin->basename, function ($links) {
            $links[] = '<a href="admin.php?page=lwpamf_plugins">' . esc_html__('LuckyWP Plugins', 'luckywp-acf-menu-field') . '</a>';
            return $links;
        });

        add_action('admin_menu', function () {
            add_submenu_page(
                Core::$plugin->prefix . '-hidden',
                esc_html__('LuckyWP Plugins', 'luckywp-acf-menu-field'),
                esc_html__('LuckyWP Plugins', 'luckywp-acf-menu-field'),
                'read',
                Core::$plugin->prefix . 'plugins',
                [$this, 'page']
            );
        });

        parent::init();
    }

    public function page()
    {
        require_once ABSPATH . 'wp-admin/includes/class-wp-plugin-install-list-table.php';

        add_filter('install_plugins_nonmenu_tabs', function ($tabs) {
            $tabs[] = 'luckywp';
            return $tabs;
        });
        add_filter('install_plugins_table_api_args_luckywp', function ($args) {
            global $paged;
            return [
                'page' => $paged,
                'per_page' => 12,
                'locale' => get_user_locale(),
                'author' => 'theluckywp',
            ];
        });

        $_REQUEST['tab'] = 'luckywp';
        $_POST['tab'] = 'luckywp';
        $table = new WP_Plugin_Install_List_Table();
        $table->prepare_items();

        wp_enqueue_script('plugin-install');
        add_thickbox();
        wp_enqueue_script('updates');

        ?>
        <div class="wrap">
            <h1><?= esc_html__('LuckyWP Plugins', 'luckywp-acf-menu-field') ?></h1>
            <div id="plugin-filter">
                <?php $table->display() ?>
            </div>
        </div>
        <?php
    }
}
