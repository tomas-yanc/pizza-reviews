<?php

namespace app\modules\rest\controllers;

use Yii;
use yii\base\Model;
use yii\rest\Controller;
use yii\web\ServerErrorHttpException;
use yii\filters\auth\HttpBasicAuth;

class ClientController extends Controller
{
    public $modelClass = 'app\modules\rest\models\Client';
    public $client;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBasicAuth::class,
            'auth' => [$this, 'auth'],
        ];
        return $behaviors;
    }

    public function auth($username, $password)
    {
        $model = $this->modelClass::checkClient($username, $password);

        if(!empty($model)) {

            $this->client = $model;
            return $model;
        }
    }

    public function actions()
    {
        $actions = parent::actions();
        return $actions;
    }

    public function actionIndex()
    {
        if(!empty($this->client)) {

            return $this->client;
        }
    }

    public function actionUpdate()
    {
        if(!empty($this->client)) {
            $model = $this->client;

            $model->load(Yii::$app->getRequest()->getBodyParams(), '');
    
            if ($model->save() === false && !$model->hasErrors()) {
                throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
            }
    
            return $model;
        }
    }

    public function actionDelete()
    {
        if(!empty($this->client)) {
            $model = $this->client;

            $model->delete();
            return 'The client has been deleted.';
        }
    }
}