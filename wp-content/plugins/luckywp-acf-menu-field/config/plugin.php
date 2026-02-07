<?php
return [
    'textDomain' => 'luckywp-acf-menu-field',
    'bootstrap' => [
        'admin',
        'pluginRate',
    ],
    'bootstrapAfterSetupTheme' => [
    ],
    'components' => [
        'admin' => \luckywp\acfMenuField\admin\Admin::class,
        'options' => \luckywp\acfMenuField\core\wp\Options::class,
        'pluginRate' => [
            'class' => \luckywp\acfMenuField\core\pluginRate\PluginRate::class,
            'link' => 'https://wordpress.org/support/plugin/luckywp-acf-menu-field/reviews/?rate=5#new-post',
        ],
    ]
];
