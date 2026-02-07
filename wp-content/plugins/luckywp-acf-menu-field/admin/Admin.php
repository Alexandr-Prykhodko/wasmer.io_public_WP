<?php

namespace luckywp\acfMenuField\admin;

use luckywp\acfMenuField\core\base\BaseObject;
use luckywp\acfMenuField\core\Core;

class Admin extends BaseObject
{

    public function init()
    {
        if (is_admin()) {
            Core::createObject(Plugins::class);
        }
    }
}
