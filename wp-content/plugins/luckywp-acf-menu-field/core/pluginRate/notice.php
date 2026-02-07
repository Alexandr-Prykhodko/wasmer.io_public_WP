<?php
/**
 * @var $link string
 * @var $prefix string
 * @var $trimPrefix string
 */
?>
<div class="notice notice-info <?= $trimPrefix ?>PluginRate">
    <p>
        <?= esc_html__('Hello!', 'luckywp-acf-menu-field') ?>
        <br>
        <?= sprintf(
        /* translators: %s: LuckyWP ACF Menu Field */
            esc_html__('We are very pleased that you by now have been using the %s plugin a few days.', 'luckywp-acf-menu-field'),
            '<b>LuckyWP ACF Menu Field</b>'
        ) ?>
        <br>
        <?= esc_html__('Please rate plugin. It will help us a lot.', 'luckywp-acf-menu-field') ?>
    </p>
    <p>
        <a href="<?= $link ?>" data-action="<?= $prefix ?>plugin_rate" target="_blank" class="button button-primary"><?= esc_html__('Rate the plugin', 'luckywp-acf-menu-field') ?></a>
        <span data-action="<?= $prefix ?>plugin_rate_show_later" class="button button-link"><?= esc_html__('Remind later', 'luckywp-acf-menu-field') ?></span>
        <span data-action="<?= $prefix ?>plugin_rate_hide" class="button button-link"><?= esc_html__('Don\'t show again', 'luckywp-acf-menu-field') ?></span>
    </p>
    <p>
        <b><?= esc_html__('Thank you very much!', 'luckywp-acf-menu-field') ?></b>
    </p>
    <div class="<?= $trimPrefix ?>PluginRate_preloader">
        <div class="<?= $trimPrefix ?>PluginRate_preloader_i"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
    </div>
</div>