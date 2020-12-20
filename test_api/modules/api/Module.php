<?php

namespace app\modules\api;

use yii\base\BootstrapInterface;

/**
 * Модуль API
 */
class Module extends \yii\base\Module implements BootstrapInterface
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\api\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        $this->modules = [
            'v1' => [
                'class' => 'app\modules\api\modules\v1\Module',
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function bootstrap($app)
    {
        $app->getUrlManager()->addRules([
            'GET   api/<version:\w+>/<controller:\w+>' => 'api/<version>/<controller>/index',
        ]);
    }
}
