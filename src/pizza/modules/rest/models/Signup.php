<?php

namespace app\modules\rest\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\behaviors\TimestampBehavior;

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
 */
class Signup extends ActiveRecord implements IdentityInterface
{
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
            }
            return true;
        }
        return false;
    }

    public function fields()
    {
        $fields = parent::fields();

        unset(
            $fields['password'],
            $fields['password_old'],
            $fields['auth_key'],
            $fields['avatar_initial'],
            $fields['created_at'],
            $fields['updated_at'],
        );
        return $fields;
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
            [['username'], 'required'],
            [['password'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['username', 'password', 'password_old', 'auth_key', 'avatar', 'avatar_initial'], 'string', 'max' => 255],
            [['first_name', 'surname', 'patronymic', 'city'], 'string', 'max' => 32],
            [['phone_number'], 'string', 'max' => 16],
            [['username'], 'unique'],
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

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
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

    public static function getUser($username) {
        $checkUsername = self::findOne(['username' => $username]);
        return $checkUsername;
    }

    public static function getPasswordsHash() {
        $checkPasswordsHash = self::find()->select('password')->all();
        return $checkPasswordsHash;
    }
}
