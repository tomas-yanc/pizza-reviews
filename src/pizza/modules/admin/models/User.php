<?php

namespace app\modules\admin\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $password_old
 * @property string|null $auth_key
 * @property string|null $first_name
 * @property string|null $surname
 * @property string|null $patronymic
 * @property string|null $date_birth
 * @property string|null $city
 * @property string|null $phone_number
 * @property string|null $avatar
 * @property string|null $avatar_initial
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Review[] $reviews
 */
class User extends \yii\db\ActiveRecord
{
    public $pass_now;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->auth_key = Yii::$app->getSecurity()->generateRandomString();
                $this->access_token = Yii::$app->getSecurity()->generateRandomString();
            }
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'password', 'phone_number'], 'required'],
            [['date_birth'], 'date', 'format' => 'php:Y-m-d', 'message' => 'Введите верный формат даты'],
            [['created_at', 'updated_at'], 'integer'],
            [['username', 'password', 'password_old', 'auth_key', 'avatar', 'avatar_initial'], 'string', 'max' => 255],
            [['first_name', 'surname', 'patronymic', 'city'], 'string', 'max' => 32],
            [['phone_number'], 'string', 'max' => 11],
            [['username'], 'unique'],
            [['password'], 'unique'],
            [['auth_key'], 'unique'],
            [['phone_number'], 'unique', 'targetClass' => User::className(),  'message' => 'Номер уже занят.'],
            [['password'], 'string', 'min' => 4],
            [['phone_number'], 'match', 'pattern' => '/[0-9]/'],
            [['username'], 'trim'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Логин',
            'password' => 'Новый пароль',
            'password_old' => 'Старый пароль',
            'auth_key' => 'Ключ авторизации',
            'first_name' => 'Имя',
            'surname' => 'Фамилия',
            'patronymic' => 'Отчество',
            'date_birth' => 'День рождения',
            'city' => 'Город',
            'phone_number' => 'Номер телефона',
            'avatar' => 'Avatar',
            'avatar_initial' => 'Аватар',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    }

    /**
     * Gets query for [[Reviews]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReviews()
    {
        return $this->hasMany(Review::className(), ['user_id' => 'id']);
    }
}
