<?php

namespace app\modules\rest\controllers;

use Yii;
use yii\base\Model;
use yii\helpers\Url;
use yii\rest\Controller;
use yii\web\ServerErrorHttpException;
use yii\db\ActiveRecordInterface;
use yii\web\NotFoundHttpException;
use app\modules\rest\models\UploadForm;
use yii\web\UploadedFile;
use yii\filters\auth\HttpBearerAuth;
use app\modules\rest\models\Auth;

class UserController extends Controller
{
    public $modelClass = 'app\modules\rest\models\User';
    public $pass;

    public const INVALID_REQUEST = 422;

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

    public static function setGenericRequestErrors()
    {
        $response = Yii::$app->getResponse();
        $response->data = [
            'status' => $response->statusCode = self::INVALID_REQUEST, 
        ];
        return $response->data;
    }

    public function actionIndex()
    {
        $model = $this->findModel();

        if (isset($model)) {
            return $model;
        }
    }

    public function actionUpdate()
    {
        if(empty(Yii::$app->request->post())) {
            return self::setGenericRequestErrors();
        }

        $model = $this->findModel();
        $this->pass = $model->password;

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        $model->password = $this->pass;

        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }
        if (isset($model)) {
            return $model;
        }
    }

    public function findModel()
    {
        $auth = $this->modelClass::$auth;

        if($auth !== null) {
            $userId = $auth->user_id;
            $user = $this->modelClass::getUser($userId);

            if(!empty($user)) {
                return $user;
            }
        }
    }
    
    public function actionDelete()
    {
        $model = $this->findModel();
        if(!empty($model)) {

            $model->delete();
            return 'The client has been deleted.';
        }
    }

    public function actionReviews()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $this->modelClass::myReviews(),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        return $dataProvider;
    }

    public function actionAvatar()
    {
        if (empty($_FILES['avatar']['name'])) {
            return 'You need to select a file.';
        }
            
        $model = $this->findModel();
        $modelUp = new UploadForm();

        if($model->avatar) {
            if(Yii::getAlias('@app') . "/web/uploads/avatars/" . $model->avatar) {
                unlink(Yii::getAlias('@app') . "/web/uploads/avatars/" . $model->avatar);
            }
        }

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        $modelUp->imageFile = UploadedFile::getInstanceByName('avatar');

        if ($modelUp->validate() && $modelUp->imageFile) {
            $modelUp->upload();
        }
        if ($_GET['myuniqid']) {
            $model->avatar = $_GET['myuniqid'] . '.' . $modelUp->imageFile->extension;
        }
        $model->avatar_initial = $modelUp->imageFile->baseName . '.' . $modelUp->imageFile->extension;

        $model->save();

        if($model->avatar) {
            return Yii::$app->response->sendFile(Yii::getAlias('@app') . "/web/uploads/avatars/" . $model->avatar);
        }
    }
}