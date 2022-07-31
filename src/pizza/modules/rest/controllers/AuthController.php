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

class AuthController extends Controller
{
    public const INVALID_REQUEST = 422;
    public const INTERVAL_SERVER_ERROR = 500;
    public const EXPIRE_ACCESS_TOKEN = 3600*24*7;
    public const EXPIRE_REFRESH_TOKEN = 3600*24*30*12;
    
    public $modelClass = 'app\modules\rest\models\Auth';
    public $findModel;
    public $user;

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




    // public function actionCreateAuthCode()
    // {
    //     if(Yii::$app->request->isPost) {

    //         $auth = $this->modelClass::findOne([
    //             'client_id' => Yii::$app->request->post('client_id'),
    //             'user_id' => $this->user->id,
    //         ]);

    //         if($auth !== null) {
    //             $model = $auth;
    //         } else {
    //             $model = new $this->modelClass();
    //         }

    //         if(Yii::$app->request->post('response_type') == 'code') {
    //             $this->authCode = Yii::$app->getSecurity()->generateRandomString();
    //             $this->state = Yii::$app->request->post('state');

    //             $model->user_id = $this->user->id;
    //             $model->client_id = Yii::$app->request->post('client_id');
    //             $model->auth_code = $this->authCode;
    //         } else {
    //             return $this->modelClass::setGenericRequestErrors();
    //         }

    //         if ($model->save()) {
    //             Yii::$app->response->setStatusCode(201);
    //         } elseif (!$model->hasErrors()) {
    //             throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
    //         }

    //         if($model->client_id !== null) { 

    //             if($this->state !== null) { 
    //                 $client = Client::findOne(['client_id' => Yii::$app->request->post('client_id')]); 

    //                 if(Yii::$app->request->post('redirect_uri') == $client->redirect_uri) {
    //                     $response = Yii::$app->response;
    //                     $response->data = [
    //                         'state' => $this->state, 
    //                         'data' => $model,
    //                     ];
            
    //                     return $response->data;
        
    //                 } else {
    //                     return $this->modelClass::setGenericRequestErrors();
    //                 }
    //             } else {
    //                 return $this->modelClass::setGenericRequestErrors();
    //             }
    //         } else {
    //             return $this->modelClass::setGenericRequestErrors();
    //         }
    //     } else {
    //         return $this->modelClass::setGenericRequestErrors();
    //     }
    // }

    // public function actionCreateTokens()
    // {
    //     if(Yii::$app->request->isPost) {

    //         if(Yii::$app->request->post('client_id') !== null) {
    //             $client = Client::findOne(['client_id' => Yii::$app->request->post('client_id')]);

    //             $auth = $this->modelClass::findOne([
    //                 'client_id' => Yii::$app->request->post('client_id'), 
    //                 'user_id' => $this->user->id
    //             ]);
    //         } else {
    //             return $this->modelClass::setGenericRequestErrors();
    //         }

    //         if($client !== null) {

    //             if($client->client_secret == Yii::$app->request->post('client_secret')) {

    //                 if(Yii::$app->request->post('grant_type') == 'authorization_code') {

    //                     if($auth->auth_code == Yii::$app->request->post('code')) {
    //                         $auth->secret_key = Yii::$app->getSecurity()->generateRandomString();
        
    //                         // Создание JWT
    //                         $headerArr = ['alg' => 'HS256', 'typ' => 'JWT'];
    //                         $payloadArr = ['userId' => $auth->user_id, 'clientSecret' => $client->client_secret];
                    
    //                         if($payloadArr !== null) {
    //                             $header = implode($headerArr);
    //                             $payload = implode($payloadArr);
    //                         } else {
    //                             return $this->modelClass::setGenericServerErrors();
    //                         }
                    
    //                         $secret_key = $auth->secret_key;
    //                         $unsignedToken = base64_encode($header) . '.' . base64_encode($payload);
    //                         $signature = hash_hmac('sha256', $unsignedToken, $secret_key);
        
    //                         if($signature !== null) {
    //                             $auth->access_token = $signature;
    //                             $auth->refresh_token = Yii::$app->getSecurity()->generateRandomString();
    //                             $auth->tokens_create = time();

    //                             $auth->save();

    //                         } else {
    //                             return $this->modelClass::setGenericServerErrors();
    //                         }
        
    //                         if($client->redirect_uri == Yii::$app->request->post('redirect_uri')) {
    //                             $response = Yii::$app->response;
    //                             $response->data = [
    //                                 'access_token' => $signature,
    //                                 'refresh_token' => $auth->refresh_token,
    //                                 'access_token_expire' => '3600*24*7',
    //                                 'refresh_token_expire' => '3600*24*30*12',
    //                             ];
            
    //                             return $response->data;
                                
    //                         } else {
    //                             return $this->modelClass::setGenericRequestErrors();
    //                         }
    //                     } else {
    //                         return $this->modelClass::setGenericRequestErrors();
    //                     }
    //                 } else {
    //                     return $this->modelClass::setGenericRequestErrors();
    //                 }
    //             } else {
    //                 return $this->modelClass::setGenericRequestErrors();
    //             }
    //         } else {
    //             return $this->modelClass::setGenericRequestErrors();
    //         }
    //     } else {
    //         return $this->modelClass::setGenericRequestErrors();
    //     }
    // }

    // public function actionUpdateTokens() 
    // {
    //     if(Yii::$app->request->isPost) {
    //         $client = Client::findOne(['client_id' => Yii::$app->request->post('client_id')]);

    //         if($client !== null && $client->client_secret == Yii::$app->request->post('client_secret')) {

    //             $auth = $this->modelClass::findOne([
    //                 'client_id' => Yii::$app->request->post('client_id'),
    //                 'user_id' => $this->user->id
    //             ]);

    //             if($auth !== null && $auth->refresh_token == Yii::$app->request->post('refresh_token')) {

    //                 if(($auth->tokens_create + $this->modelClass::EXPIRE_ACCESS_TOKEN) < time()) {

    //                     if(($auth->tokens_create + $this->modelClass::EXPIRE_REFRESH_TOKEN) > time()) {

    //                         if(Yii::$app->request->post('grant_type') == 'refresh_token') {
    //                             $auth->secret_key = Yii::$app->getSecurity()->generateRandomString();
    //                             $auth->tokens_create = time();
        
    //                             // Создание JWT
    //                             $headerArr = ['alg' => 'HS256', 'typ' => 'JWT'];
    //                             $payloadArr = ['userId' => $auth->user_id, 'clientSecret' => $client->client_secret];
                        
    //                             if($payloadArr !== null) {
    //                                 $header = implode($headerArr);
    //                                 $payload = implode($payloadArr);
    //                             } else {
    //                                 return $this->modelClass::setGenericServerErrors();
    //                             }
                        
    //                             $secret_key = $auth->secret_key;
    //                             $unsignedToken = base64_encode($header) . '.' . base64_encode($payload);
    //                             $signature = hash_hmac('sha256', $unsignedToken, $secret_key);
        
    //                             if($signature !== null) {
    //                                 $auth->access_token = $signature;
    //                                 $auth->refresh_token = Yii::$app->getSecurity()->generateRandomString();
    //                                 $auth->save();
    //                             } else {
    //                                 return $this->modelClass::setGenericServerErrors();
    //                             }
            
    //                             if($client->redirect_uri == Yii::$app->request->post('redirect_uri')) {
    //                                 $response = Yii::$app->response;
    //                                 $response->data = [
    //                                     'access_token' => $signature,
    //                                     'refresh_token' => $auth->refresh_token,
    //                                     'access_token_expire' => '3600*24*7',
    //                                     'refresh_token_expire' => '3600*24*30*12',
    //                                 ];
        
    //                                 return $response->data;
        
    //                             } else {
    //                                 return $this->modelClass::setGenericRequestErrors();
    //                             }
    //                         } else {
    //                             return $this->modelClass::setGenericRequestErrors();
    //                         }
    //                     } else {
    //                         return 'Refresh token is die.';
    //                     }
    //                 } else {
    //                     return 'Access Token still life.';
    //                 }
    //             } else {
    //                 return $this->modelClass::setGenericRequestErrors();
    //             }
    //         }
    //     }
    // }
}