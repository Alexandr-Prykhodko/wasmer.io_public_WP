<?php
/*
Plugin Name: LuckyWP ACF Menu Field
Description: Add navigation menu field type to Advanced Custom Fields
Version: 1.0.3
Author: LuckyWP
Author URI: https://theluckywp.com/
Text Domain: luckywp-acf-menu-field
Domain Path: /languages

LuckyWP ACF Menu Field is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

LuckyWP ACF Menu Field is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with LuckyWP ACF Menu Field. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

require 'core/lwpamfAutoloader.php';
$lwpamfAutoloader = new lwpamfAutoloader();
$lwpamfAutoloader->register();
$lwpamfAutoloader->addNamespace('luckywp\acfMenuField', __DIR__);

$config = require(__DIR__ . '/config/plugin.php');
(new \luckywp\acfMenuField\plugin\Plugin($config))->run('1.0.3', __FILE__, 'lwpamf_');
