<?php

namespace app\modules\rest\controllers;

use Yii;
use yii\base\Model;
use yii\rest\Controller;
use yii\filters\auth\HttpBasicAuth;

class LoginController extends Controller
{
    public $modelClass = 'app\modules\rest\models\Signup';
    public $user;

    public const INVALID_REQUEST = 422;
    public const INTERVAL_SERVER_ERROR = 500;

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
        $response = Yii::$app->getResponse();

        $model = $this->modelClass::getUser($username);

        if($model == null) {
            $response->data = [
                'status' => $response->statusCode = self::INVALID_REQUEST, 
            ];
            return $response->data;
        }

        if($model->password == null) {
            $response->data = [
                'status' => $response->statusCode = self::INVALID_REQUEST, 
            ];
            return $response->data;
        }

        if(!Yii::$app->getSecurity()->validatePassword($password, $model->password)) {
            $response->data = [
                'status' => $response->statusCode = self::INVALID_REQUEST, 
            ];
            return $response->data;
        }

        $this->user = $model;
        return $model;
    }

    public function actions()
    {
        $actions = parent::actions();
        return $actions;
    }

    public function actionIndex()
    {   
        if($this->user == null) {
            $response = Yii::$app->getResponse();
            $response->data = [
                'status' => $response->statusCode = self::INTERVAL_SERVER_ERROR, 
            ];
            return $response->data;
        }

        return $this->user;
    }
}