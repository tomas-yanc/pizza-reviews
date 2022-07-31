<?php

namespace app\modules\rest\controllers;

use Yii;
use yii\base\Model;
use yii\rest\Controller;
use yii\web\ServerErrorHttpException;

class SignupController extends Controller
{
    public $modelClass = 'app\modules\rest\models\Signup';

    public const INVALID_REQUEST = 422;

    public function actions()
    {
        $actions = parent::actions();
        return $actions;
    }

    public function actionCreate()
    {
        $response = Yii::$app->getResponse();
        
        if(!Yii::$app->request->isPost) {
            return $this::setGenericRequestErrors();
        }

        if(empty(Yii::$app->request->post())) {

            $response->statusCode = self::INVALID_REQUEST;
            $response->data = [
                'field' => ['password', 'username'],
                'message' => 'Need to fill in the data',
            ];
            return $response->data;
        }

        $username = Yii::$app->request->post('username');

        $checkUsername = $this->modelClass::getUser($username);

        if($checkUsername !== null) {
            $response->statusCode = self::INVALID_REQUEST;
            $response->data = [
                'field' => 'username',
                'message' => 'Username already taken.',
            ];
            return $response->data;
        }

        if(empty(Yii::$app->request->post('password'))) {

            $response->statusCode = self::INVALID_REQUEST;
            $response->data = [
                'field' => 'password',
                'message' => 'Need to fill in the «Password».',
            ];
            return $response->data;
        }

        $checkPasswordsHash = $this->modelClass::getPasswordsHash();

        foreach($checkPasswordsHash as $checkPassword) {

            if(Yii::$app->getSecurity()->validatePassword(Yii::$app->request->post('password'), $checkPassword['password'])) {
                $response->statusCode = self::INVALID_REQUEST;
                $response->data = [
                    'field' => 'password',
                    'message' => 'Password already taken.',
                ];
                return $response->data;
            }
        }

        $model = new $this->modelClass();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if (empty($model->password)) {
            $response->data = [
                'status' => $response->statusCode = self::INVALID_REQUEST, 
            ];
            return $response->data;
        }

        $model->password = Yii::$app->getSecurity()->generatePasswordHash($model->password);

        if ($model->save()) {
            $response->setStatusCode(201);
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }
}