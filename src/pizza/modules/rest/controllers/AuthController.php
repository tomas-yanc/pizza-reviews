<?php

namespace app\modules\rest\controllers;

use yii;
use yii\base\Model;
use yii\rest\Controller;
use yii\web\ServerErrorHttpException;
use yii\filters\auth\HttpBasicAuth;
use app\modules\rest\models\Signup;
use app\modules\rest\models\Client;
use app\modules\rest\services\CreateAuthData;
use app\modules\rest\interfaces\CreateAuthDataInterface;

use yii\di\Container;

class AuthController extends Controller
{
    public const INVALID_REQUEST = 422;
    public const INTERVAL_SERVER_ERROR = 500;
    public const EXPIRE_ACCESS_TOKEN = 3600*24*7;
    public const EXPIRE_REFRESH_TOKEN = 3600*24*30*12;
    
    public $modelClass = 'app\modules\rest\models\Auth';
    public $findModel;
    public $user;

    public $createAuthDataInterface;

    public function __construct($id, $module, $config = [], CreateAuthDataInterface $createAuthDataInterface)
    {
        $this->createAuthDataInterface = $createAuthDataInterface;
        parent::__construct($id, $module, $config);
    }

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
        $model = Signup::findOne(['username' => $username]);

        if(!empty($model)) {
            if(Yii::$app->getSecurity()->validatePassword($password, $model->password)) {

                $this->user = $model;
                return $model;
            }
        }
    }

    public function actions()
    {
        $actions = parent::actions();
        return $actions;
    }

    public function getAuth($clientId)
    {
        $userId = $this->user->id;

        $auth = $this->modelClass::getAuth($clientId, $userId);
        return $auth;
    }

    public function getClient($clientId)
    {
        $client = Client::getClient($clientId);
        return $client;
    }

    public function getAccessToken($userId, $clientSecret, $secretKey) {
        $accessToken = CreateAuthData::createJwt(
            $userId, 
            $clientSecret, 
            $secretKey
        );

        return $accessToken;
    }

    public function setGenericRequestErrors()
    {
        $response = Yii::$app->getResponse();
        $response->data = [
            'status' => $response->statusCode = self::INVALID_REQUEST, 
        ];
        return $response->data;
    }

    public function setGenericServerErrors()
    {
        $response = Yii::$app->getResponse();
        $response->data = [
            'status' => $response->statusCode = self::INTERVAL_SERVER_ERROR, 
        ];
        return $response->data;
    }

    public function actionCreateAuthCode()
    {
        if(empty(Yii::$app->request->post())) {
            return $this->setGenericRequestErrors();
        }

        if(Yii::$app->request->post('client_id') == null) { 
            return $this->setGenericRequestErrors();
        }

        $clientId = Yii::$app->request->post('client_id');
        $userId = $this->user->id;

        $client = $this->getClient($clientId);

        if($client == null) { 
            return $this->setGenericRequestErrors();
        }

        $auth = $this->getAuth($clientId);

        $auth !== null ? $model = $auth : $model = new $this->modelClass();

        if(Yii::$app->request->post('response_type') !== 'code') {
            return $this->setGenericRequestErrors();
        }

        if(Yii::$app->request->post('state') == null) { 
            return $this->setGenericRequestErrors();
        }

        $state = Yii::$app->request->post('state');

        $model->client_id = Yii::$app->request->post('client_id');
        $model->auth_code = Yii::$app->getSecurity()->generateRandomString();
        $model->user_id = $this->user->id;

        if ($model->save()) {
            Yii::$app->response->setStatusCode(201);
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        if(Yii::$app->request->post('redirect_uri') !== $client->redirect_uri) {
            return $this->setGenericRequestErrors();
        }

        $response = Yii::$app->response;
        $response->data = [
            'state' => $state, 
            'auth-code' => $model->auth_code,
        ];

        return $response->data;
    }

    public function actionCreateTokens()
    {
        $response = Yii::$app->response;

        if(empty(Yii::$app->request->post())) {
            return $this->setGenericRequestErrors();
        }

        if(Yii::$app->request->post('client_id') == null) {
            return $this->setGenericRequestErrors();
        }

        $clientId = Yii::$app->request->post('client_id');

        $auth = $this->getAuth($clientId);

        if($auth->access_token !== null) {
            if(!empty($auth->access_token)) {

                $response->data = [
                    'access_token' => $auth->access_token,
                    'refresh_token' => $auth->refresh_token,
                ];
        
                return $response->data; 
            }
        }

        $client = $this->getClient($clientId);

        if($client == null) {
            return $this->setGenericRequestErrors();
        }

        if($client->client_secret !== Yii::$app->request->post('client_secret')) {
            return $this->setGenericRequestErrors();
        }

        if(Yii::$app->request->post('grant_type') !== 'authorization_code') {
            return $this->setGenericRequestErrors();
        }

        if($auth->auth_code !== Yii::$app->request->post('code')) {
            return $this->setGenericRequestErrors();
        }

        // Создание JWT
        $userId = $auth->user_id;
        $clientSecret = $client->client_secret;
        $secretKey = Yii::$app->getSecurity()->generateRandomString();

        $accessToken = $this->getAccessToken($userId, $clientSecret, $secretKey);

        if($accessToken == null) {
            return $this->setGenericServerErrors();
        }

        $auth->access_token = $accessToken;
        $auth->refresh_token = Yii::$app->getSecurity()->generateRandomString();
        $auth->secret_key = $secretKey;
        $auth->tokens_create = time();

        $auth->save();

        if($client->redirect_uri !== Yii::$app->request->post('redirect_uri')) {
            return $this->setGenericRequestErrors();
        }

        $response->data = [
            'access_token' => $auth->access_token,
            'refresh_token' => $auth->refresh_token,
            'access_token_expire' => '3600*24*7',
            'refresh_token_expire' => '3600*24*30*12',
        ];

        return $response->data;
    }

    public function actionUpdateTokens() 
    {
        if(empty(Yii::$app->request->post())) {
            return $this->setGenericRequestErrors();
        }

        if(Yii::$app->request->post('client_id') == null) {
            return $this->setGenericRequestErrors();
        }

        $clientId = Yii::$app->request->post('client_id');
        $client = $this->getClient($clientId);

        if($client == null) {
            return $this->setGenericRequestErrors();
        }
        
        if($client->client_secret !== Yii::$app->request->post('client_secret')) {
            return $this->setGenericRequestErrors();
        }

        $auth = $this->getAuth($clientId);

        if($auth == null) {
            return $this->setGenericRequestErrors();
        }
        
        if($auth->refresh_token !== Yii::$app->request->post('refresh_token')) {
            return $this->setGenericRequestErrors();
        }

        if(($auth->tokens_create + self::EXPIRE_ACCESS_TOKEN) > time()) {
            return 'The access token is still valid.';
        }

        if(($auth->tokens_create + self::EXPIRE_REFRESH_TOKEN) < time()) {
            return 'The refresh token is no longer valid.';
        }

        if(Yii::$app->request->post('grant_type') !== 'refresh_token') {
            return $this->setGenericRequestErrors();
        }

        // Создание JWT
        $userId = $auth->user_id;
        $clientSecret = $client->client_secret;
        $secretKey = Yii::$app->getSecurity()->generateRandomString();

        $accessToken = $this->getAccessToken($userId, $clientSecret, $secretKey);

        if($accessToken == null) {
            return $this->setGenericServerErrors();
        }

        if($accessToken == null) {
            return $this->setGenericServerErrors();
        }
        
        $auth->access_token = $accessToken;
        $auth->refresh_token = Yii::$app->getSecurity()->generateRandomString();
        $auth->secret_key = $secretKey;
        $auth->tokens_create = time();

        $auth->save();

        if($client->redirect_uri !== Yii::$app->request->post('redirect_uri')) {
            return $this->setGenericRequestErrors();
        }

        $response = Yii::$app->response;
        $response->data = [
            'access_token' => $auth->access_token,
            'refresh_token' => $auth->refresh_token,
            'access_token_expire' => '3600*24*7',
            'refresh_token_expire' => '3600*24*30*12',
        ];

        return $response->data;
    }
}