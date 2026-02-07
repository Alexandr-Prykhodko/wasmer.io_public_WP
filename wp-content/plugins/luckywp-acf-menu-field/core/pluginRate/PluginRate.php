<?php

namespace luckywp\acfMenuField\core\pluginRate;

use luckywp\acfMenuField\core\base\BaseObject;
use luckywp\acfMenuField\core\Core;

class PluginRate extends BaseObject
{

    public $link;

    public function init()
    {
        add_action('wp_ajax_' . Core::$plugin->prefix . 'plugin_rate', [$this, 'rate']);
        add_action('wp_ajax_' . Core::$plugin->prefix . 'plugin_rate_show_later', [$this, 'showLater']);
        add_action('wp_ajax_' . Core::$plugin->prefix . 'plugin_rate_hide', [$this, 'hide']);

        add_action('init', function () {
            if ($this->isShow()) {
                add_action('admin_notices', [$this, 'notice']);
            }
        });

        add_action('admin_enqueue_scripts', [$this, 'assets']);
    }

    private $_isShow;

    /**
     * @return bool
     */
    public function isShow()
    {
        if ($this->_isShow === null) {
            if (current_user_can('manage_options')) {
                $time = Core::$plugin->options->get('rate_time');
                if ($time === false) {
                    Core::$plugin->options->set('rate_time', time() + DAY_IN_SECONDS);
                    $this->_isShow = false;
                } else {
                    $this->_isShow = time() > $time;
                }
            } else {
                $this->_isShow = false;
            }
        }
        return $this->_isShow;
    }

    public function rate()
    {
        Core::$plugin->options->set('rate_time', time() + YEAR_IN_SECONDS);
    }

    public function hide()
    {
        Core::$plugin->options->set('rate_time', time() + YEAR_IN_SECONDS * 5);
    }

    public function showLater()
    {
        Core::$plugin->options->set('rate_time', time() + WEEK_IN_SECONDS);
    }

    public function notice()
    {
        $link = $this->link;
        $prefix = Core::$plugin->prefix;
        $trimPrefix = Core::$plugin->trimPrefix;
        include __DIR__ . '/notice.php';
    }

    /**
     * Подключение ресурсов
     */
    public function assets()
    {
        if ($this->isShow()) {
            wp_enqueue_style(Core::$plugin->prefix . 'pluginRate', Core::$plugin->url . '/core/pluginRate/main.min.css', [], Core::$plugin->version);
            wp_enqueue_script(Core::$plugin->prefix . 'pluginRate', Core::$plugin->url . '/core/pluginRate/main.min.js', ['jquery'], Core::$plugin->version);
            wp_localize_script(Core::$plugin->prefix . 'pluginRate', Core::$plugin->trimPrefix . 'PluginRate', [
                'ajaxUrl' => admin_url('admin-ajax.php'),
            ]);
        }
    }
}
