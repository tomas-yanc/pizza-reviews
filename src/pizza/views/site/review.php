<?php

use app\models\Reviews;

/** @var yii\web\View $this */

$this->title = 'Reviews';
?>
<div class="site-review">

    <div>
        <h1>Отзывы о пицце</h1>
    </div>

    <div class="body-content">
        <div class="row">
            <div class="col-lg-10">
                <?php
                if(!empty($users)) :
                    foreach($users as $user) :
                        if($user->username !== 'admin') {
                            echo '<hr><h2>' . $user->first_name . ' ' . $user->surname . '</h2><br>';
                            echo '<img width = 150 src = "/uploads/avatars/' . $user->avatar . '" alt = "Аватар">';
                            echo '<p>' . $user->username . '</p>';
                        $reviews = $user->reviews;

                        if(!empty($reviews)) :
                            foreach($reviews as $review) :
                                echo '<h4>' . $review->title . '</h4>';
                                echo '<p>' . $review->body . '</p>';
                                echo '<i>Дата создания: ' . date('Y-m-d', $review->created_at) . '</i>';
                                echo $review->status == Reviews::UNREAD ? '<p><i>Отзыв не прочитан</i></p>' : '<p><i>Отзыв прочитан</i></p>';
                                echo '<br>';
                            endforeach;
                        endif;
                        }
                    endforeach;
                endif;
                ?>
            </div>
        </div>
    </div>
</div>
