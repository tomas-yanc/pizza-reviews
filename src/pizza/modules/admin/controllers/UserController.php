<?php

namespace app\modules\admin\controllers;

use Yii;
use app\modules\admin\models\User;
use app\modules\admin\models\search\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\modules\admin\models\UploadForm;
use yii\web\UploadedFile;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
                'access' => [
                    'class' => AccessControl::class,
                    'only' => ['index', 'view', 'create', 'update', 'delete'],
                    'rules' => [
                        [
                            'allow' => false,
                            'actions' => ['index', 'view', 'create', 'update', 'delete'],
                            'roles' => ['?'],
                        ],
                        [
                            'allow' => true,
                            'actions' => ['index', 'view', 'create', 'update', 'delete'],
                            'roles' => ['@'],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all User models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id); 
        if($model->id !== Yii::$app->user->id) {
            return $this->goHome();
        }
        
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelUp = new UploadForm();

        if($model->id !== Yii::$app->user->id) {
            return $this->goHome();
        }

        if ($this->request->isPost && $model->load($this->request->post()) && $model->validate()) {

            $modelUp->imageFile = UploadedFile::getInstance($modelUp, 'imageFile');
            if ($modelUp->validate() && $modelUp->imageFile) {
                $modelUp->upload();

                if ($model->avatar) {
                    unlink(Yii::getAlias('@app') . "/web/uploads/avatars/$model->avatar");
                }
                if ($_GET['myuniqid']) {
                    $model->avatar = $_GET['myuniqid'] . '.' . $modelUp->imageFile->extension;
                }
                $model->avatar_initial = $modelUp->imageFile->baseName . '.' . $modelUp->imageFile->extension;
            }
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'modelUp' => $modelUp,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        if($model->id !== Yii::$app->user->id) {
            return $this->goHome();
        }
        
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionUpdatePass($id)
    {
        $model = $this->findModel($id);
        $pass_now = $model->password;

        if($model->id !== Yii::$app->user->id) {
            return $this->goHome();
        }

        if ($this->request->isPost && $model->load($this->request->post()) && $model->validate()) {

                if (Yii::$app->getSecurity()->validatePassword($model->password_old, $pass_now)) {

                    $model->password = Yii::$app->getSecurity()->generatePasswordHash($model->password);
                    $model->password_old = Yii::$app->getSecurity()->generatePasswordHash($model->password_old);
                    $model->save();
                    return $this->redirect(['view', 'id' => $model->id]);
                }

            throw new NotFoundHttpException('Введен не верный старый пароль');
        }
        return $this->render('update-pass', [
            'model' => $model,
        ]);
    }

    public function actionDeleteAvatar($id)
    {
        $model = $this->findModel($id);

        if ($model->avatar) {
            unlink(Yii::getAlias('@app') . "/web/uploads/avatars/$model->avatar");
        }

        $model->avatar = '';
        $model->avatar_initial = '';
        $model->save();
        return $this->redirect(['index', 'id' => $model->id]);
    }
}
