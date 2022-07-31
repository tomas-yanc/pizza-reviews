<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\User */

$this->title = 'Обновить пароль:';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update-pass';
?>
<div class="user-update">

    <h2><?= Html::encode($this->title) ?></h2><br>

    <?= $this->render('_form-pass', [
        'model' => $model,
    ]) ?>

</div>
