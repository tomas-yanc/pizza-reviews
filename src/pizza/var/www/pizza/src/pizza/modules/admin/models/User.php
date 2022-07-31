<?php

namespace app\var\www\pizza\src\pizza\modules\admin\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string|null $access_token
 * @property string|null $auth_key
 * @property string|null $first_name
 * @property string|null $surname
 * @property string|null $patronymic
 * @property string|null $date_birth
 * @property string|null $city
 * @property string|null $phone_number
 * @property string|null $avatar
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Review[] $reviews
 */
class User extends \yii\db\ActiveRecord
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
            [['username', 'password', 'created_at', 'updated_at'], 'required'],
            [['date_birth'], 'safe'],
            [['created_at', 'updated_at'], 'integer'],
            [['username', 'password', 'access_token', 'auth_key', 'avatar'], 'string', 'max' => 255],
            [['first_name', 'surname', 'patronymic', 'city'], 'string', 'max' => 32],
            [['phone_number'], 'string', 'max' => 16],
            [['username'], 'unique'],
            [['password'], 'unique'],
            [['access_token'], 'unique'],
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
            'access_token' => 'Access Token',
            'auth_key' => 'Auth Key',
            'first_name' => 'First Name',
            'surname' => 'Surname',
            'patronymic' => 'Patronymic',
            'date_birth' => 'Date Birth',
            'city' => 'City',
            'phone_number' => 'Phone Number',
            'avatar' => 'Avatar',
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
        return $this->hasMany(Review::className(), ['user_id' => 'id']);
    }
}
