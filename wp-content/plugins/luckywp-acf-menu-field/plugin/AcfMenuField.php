<?php

namespace luckywp\acfMenuField\plugin;

use acf_field;
use luckywp\acfMenuField\core\helpers\ArrayHelper;
use luckywp\acfMenuField\core\helpers\Html;
use WP_Term;

class AcfMenuField extends acf_field
{

    public function __construct()
    {
        $this->name = 'menu';
        $this->label = esc_html__('Menu', 'luckywp-acf-menu-field');
        $this->category = 'relational';
        $this->defaults = [
            'return_format' => 'id',
            'allow_null' => 0,
        ];
        parent::__construct();
    }

    /**
     * @param array $field
     */
    public function render_field_settings($field)
    {
        acf_render_field_setting($field, [
            'label' => Plugin::__acf('Return Value'),
            'instructions' => Plugin::__acf('Specify the returned value on front end'),
            'type' => 'radio',
            'name' => 'return_format',
            'layout' => 'horizontal',
            'choices' => $this->getReturnFormats(),
        ]);
        acf_render_field_setting($field, [
            'label' => Plugin::__acf('Allow Null?'),
            'instructions' => '',
            'name' => 'allow_null',
            'type' => 'true_false',
            'ui' => 1,
        ]);
    }

    /**
     * @param array $field
     */
    public function render_field($field)
    {
        $menus = wp_get_nav_menus();
        if (!$menus) {
            echo '<p><i>' . esc_html__('Navigation menus not exists.', 'luckywp-acf-menu-field') . '</i></p>';
            return;
        }

        $items = [];
        if (ArrayHelper::getValue($field, 'allow_null', false)) {
            $items[''] = '— ' . _x('Select', 'verb', 'acf') . ' —';
        }
        foreach (wp_get_nav_menus() as $menu) {
            $items[$menu->term_id] = $menu->name;
        }

        echo Html::dropDownList($field['name'], $field['value'], $items, [
            'id' => $field['id'],
            'class' => ArrayHelper::getValue($field, 'class'),
        ]);
    }

    /**
     * @param mixed $value
     * @param int $postId
     * @param array $field
     * @return int|string|WP_Term|null|false
     */
    public function format_value($value, $postId, $field)
    {
        if (empty($value)) {
            return null;
        }
        $menuId = (int)$value;

        $format = ArrayHelper::getValue($field, 'return_format');
        if (!array_key_exists($format, $this->getReturnFormats())) {
            $format = 'id';
        }

        switch ($format) {
            case 'object':
                return wp_get_nav_menu_object($menuId);

            case 'html':
                $menu = wp_get_nav_menu_object($menuId);
                if (!$menu) {
                    return false;
                }

                $args = apply_filters('lwpamf_wp_nav_menu_args', [
                    'fallback_cb' => false,
                ], $menu, $field, $postId);
                $args['menu'] = $menu;
                $args['echo'] = false;
                return wp_nav_menu($args);

            default:
            case 'id':
                return $menuId;
        }
    }

    /**
     * @return array
     */
    protected function getReturnFormats()
    {
        return [
            'id' => esc_html__('Menu ID', 'luckywp-acf-menu-field'),
            'object' => esc_html__('Menu Object', 'luckywp-acf-menu-field'),
            'html' => esc_html__('Menu HTML', 'luckywp-acf-menu-field'),
        ];
    }
}
