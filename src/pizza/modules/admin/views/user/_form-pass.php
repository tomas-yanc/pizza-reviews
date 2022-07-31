<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap4\ActiveForm $form */
/** @var app\models\LoginForm $model */

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

$this->title = 'Update-pass';
?>
<div class="update_pass-form">

    <?php $form = ActiveForm::begin([
        'id' => 'update_pass-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'labelOptions' => ['class' => 'col-lg-2 col-form-label mr-lg-3'],
            'inputOptions' => ['class' => 'col-lg-3 form-control'],
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); ?>

        <?= $form->field($model, 'password_old')->passwordInput(['maxlength' => true, 'value' => '******' ]) ?>

        <?= $form->field($model, 'password')->passwordInput(['maxlength' => true, 'value' => '******']) ?>

        <?= $form->field($model, 'surname')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <div class="offset-lg-2 col-lg-11">
                <?= Html::submitButton('Обновить', ['class' => 'btn btn-primary', 'name' => 'update_pass-button']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>
</div>
