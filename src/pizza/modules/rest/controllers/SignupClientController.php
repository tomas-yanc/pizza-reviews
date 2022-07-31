<?php

namespace app\modules\rest\controllers;

use yii;
use yii\base\Model;
use yii\rest\Controller;
use yii\web\ServerErrorHttpException;

class SignupClientController extends Controller
{
    public $modelClass = 'app\modules\rest\models\Client';

    public function actions()
    {
        $actions = parent::actions();
        return $actions;
    }

    public function actionCreate() 
    {
        if(Yii::$app->request->isPost) {
            $model = new $this->modelClass();

            $model->load(Yii::$app->getRequest()->getBodyParams(), '');
    
            if ($model->save()) {
                $response = Yii::$app->getResponse();
                $response->setStatusCode(201);
            } elseif (!$model->hasErrors()) {
                throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
            }
            return $model;
        } 
    }
}