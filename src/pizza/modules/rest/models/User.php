<?php

namespace app\modules\rest\models;

use Yii;
use yii\helpers\Url;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;
use app\modules\rest\models\Auth;

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
class User extends \yii\db\ActiveRecord implements IdentityInterface 
{
    public const EXPIRE_ACCESS_TOKEN = 3600*24*7;
    public static $auth;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        unset(
            $fields['id'],
            // $fields['username'],
            $fields['password'],
            $fields['password_old'],
            $fields['auth_key'],
            $fields['avatar'],
            $fields['avatar_initial'],
            $fields['created_at'],
            $fields['updated_at'],
            $fields['surname'],
            $fields['first_name'],
            $fields['patronymic'],
        );

        $fields['name'] = function () {
            return $this->surname . ' ' . $this->first_name . ' ' . $this->patronymic;
        };

        return $fields;
    }

    public function extraFields()
    {
        return ['created_at', 'reviews'];
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
            [['username', 'password'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['username', 'password', 'password_old', 'auth_key', 'avatar', 'avatar_initial'], 'string', 'max' => 255],
            [['first_name', 'surname', 'patronymic', 'city'], 'string', 'max' => 32],
            [['phone_number'], 'string', 'max' => 16],
            [['username', 'password', 'auth_key', 'phone_number'], 'unique'],
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
        return $this->hasMany(Review::className(), ['user_id' => 'id']);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        $authObject = Auth::findOne(['access_token' => $token]);
        if(($authObject->tokens_create + self::EXPIRE_ACCESS_TOKEN) > time()) {

            self::$auth = $authObject;
            return $authObject;
        }
    }

    public static function findIdentity($id) 
    {

    }

    public function getId() 
    {

    }

    public function getAuthKey() 
    {

    }

    public function validateAuthKey($authKey) 
    {

    }

    public static function getUser($userId) {
        $user = self::findOne($userId);
        return $user;
    }

    public static function myReviews()
    {
        $query = self::find()->with(['reviews']);
        return $query;
    }
}
