<?php

namespace luckywp\acfMenuField\plugin;

use luckywp\acfMenuField\admin\Admin;
use luckywp\acfMenuField\core\base\BasePlugin;
use luckywp\acfMenuField\core\pluginRate\PluginRate;
use luckywp\acfMenuField\core\wp\Options;

/**
 * @property-read Admin $admin
 * @property-read Options $options
 * @property-read PluginRate $pluginRate
 */
class Plugin extends BasePlugin
{

    public function init()
    {
        add_action('acf/include_field_types', function () {
            include_once __DIR__ . '/AcfMenuField.php';
            new AcfMenuField();
        });
    }

    /**
     * @param string $text
     * @return string
     */
    public static function __acf($text)
    {
        return __($text, 'acf');
    }
}
