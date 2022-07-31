<?php

namespace app\modules\rest\controllers;

use Yii;
use yii\rest\Controller;
use yii\filters\auth\HttpBearerAuth;
use app\modules\rest\models\User;

class ReviewController extends Controller
{
    public $modelClass = 'app\modules\rest\models\Review';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        return $actions;
    }

    public function actionIndex()
    {
        $user = $this->findUser();
        $reviews = $user->reviews;

        if (isset($reviews)) {
            return $reviews;
        }
    }

    public function actionCreate() 
    {
        if(Yii::$app->request->isPost) {
            $model = new $this->modelClass();

            $model->load(Yii::$app->getRequest()->getBodyParams(), '');
            $model->user_id = $this->findUser()->id;
    
            if ($model->save()) {
                $response = Yii::$app->getResponse();
                $response->setStatusCode(201);
            } elseif (!$model->hasErrors()) {
                throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
            }
            return $model;
        } 
    }

    public function actionUpdate($id)
    {
        if(!empty(Yii::$app->request->post())) {

            $model = $this->findModel($id);
            $model->load(Yii::$app->getRequest()->getBodyParams(), '');

            if ($model->save() === false && !$model->hasErrors()) {
                throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
            }
            if (isset($model)) {
                return $model;
            }
        } else {
            return User::setGenericRequestErrors();
        }
    }

    public function findModel($id)
    {
        $model = $this->modelClass::findOne($id);

        if(!empty($model)) {
            return $model;
        }
    }

    public function findUser()
    {
        $auth = User::$auth;
        if($auth !== null) {

            $user = User::findOne($auth->user_id);

            if (isset($user)) {
                return $user;
            }
        }
    }
    
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if(isset($model)) {

            $model->delete();
            return 'The review has been deleted.';
        }
    }
}