<?php declare(strict_types=1);

namespace App;

use Swoft\SwoftApplication;
use function date_default_timezone_set;

/**
 *
 * 昨夜雨疏风骤，浓睡不消残酒。
 * 试问卷帘人，却道海棠依旧。
 * 知否?知否? 应是绿肥红瘦。
 *
 * Class Application
 *
 * @since 2.0
 */
class Application extends SwoftApplication
{
    protected function beforeInit(): void
    {
        parent::beforeInit();

        date_default_timezone_set('Asia/Shanghai');
    }
}
