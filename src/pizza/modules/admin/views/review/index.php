<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\search\ReviewSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Reviews';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
    $username = '';
    $first_name = '';
    $surname = '';
    if(isset(Yii::$app->user->identity['username'])) {
        $username = Yii::$app->user->identity['username'];
        $first_name = Yii::$app->user->identity['first_name'];
        $surname = Yii::$app->user->identity['surname'];
    }
?>

<div class="review-index">

    <h3><?= $username !== 'admin' ?  'Отзывы пользователя: ' . $first_name . ' ' . $surname : 'Отзывы пользователей'?></h3>

    <p>
        <?= Html::a('Добавить отзыв', ['create'], ['class' => 'btn btn-outline-secondary']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            // 'user_id',
            'title',
            'body:ntext',
            'status',
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:Y-m-d H:i:s']
            ],
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

</div>
