<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'About user';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?php
        $identity = Yii::$app->user->identity;

        if(isset($identity['username'])) {
            echo '<pre>'; print_r($identity);
        }
        ?>
    </p>
</div>
