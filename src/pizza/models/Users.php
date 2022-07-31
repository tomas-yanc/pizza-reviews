<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string|null $password_old
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
class Users extends \yii\db\ActiveRecord
{
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
            [['username', 'password'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['username', 'password', 'password_old', 'auth_key', 'avatar', 'avatar_initial'], 'string', 'max' => 255],
            [['first_name', 'surname', 'patronymic', 'city'], 'string', 'max' => 32],
            [['phone_number'], 'string', 'max' => 16],
            [['username'], 'unique', 'targetClass' => User::className(),  'message' => 'Логин уже занят.'],
            [['password'], 'unique'],
            [['auth_key'], 'unique'],
            [['phone_number'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'password_old' => 'Password Old',
            'auth_key' => 'Auth Key',
            'first_name' => 'First Name',
            'surname' => 'Surname',
            'patronymic' => 'Patronymic',
            'date_birth' => 'Date Birth',
            'city' => 'City',
            'phone_number' => 'Phone Number',
            'avatar' => 'Avatar',
            'avatar_initial' => 'Avatar Initial',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Reviews]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReviews()
    {
        return $this->hasMany(Reviews::className(), ['user_id' => 'id']);
    }

    public static function Users()
    {
        $Users = Self::find()->all();
        
        return $Users;
    }
}
