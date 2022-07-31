<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Review */
/* @var $form yii\widgets\ActiveForm */
?>

<?php
    $username = '';
    if(isset(Yii::$app->user->identity['username'])) {
        $username = Yii::$app->user->identity['username'];
    }
?>

<div class="review-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php // $form->field($model, 'user_id')->textInput(['value' => Yii::$app->user->id]) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'body')->textarea(['rows' => 6]) ?>

    <?php
    if($username == 'admin') {
        echo $form->field($model, 'status')->textInput(['maxlength' => true]);
    }
    ?>

    <?php // $form->field($model, 'created_at')->textInput() ?>

    <?php // $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
