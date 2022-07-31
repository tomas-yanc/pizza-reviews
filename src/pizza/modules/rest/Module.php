<?php

namespace app\modules\rest;

/**
 * rest module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\rest\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        \Yii::$app->user->enableSession = false;
        // \Yii::$app->user->identityClass = 'app\modules\rest\models\Client';
        \Yii::$app->user->identityClass = 'app\modules\rest\models\User';

        // custom initialization code goes here
    }
}
