<?php

namespace app\modules\rest\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user_client".
 *
 * @property int $user_id
 * @property int $client_id
 * @property string|null $auth_code
 * @property string|null $secret_key
 * @property string|null $access_token
 * @property string|null $refresh_token
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class Auth extends ActiveRecord implements IdentityInterface
{
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
        return 'auth';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_id', 'user_id'], 'required'],
            [['user_id', 'tokens_create', 'created_at', 'updated_at'], 'integer'],
            [['client_id', 'auth_code', 'secret_key', 'access_token', 'refresh_token'], 'string', 'max' => 255],
            [['user_id', 'client_id'], 'unique', 'targetAttribute' => ['user_id', 'client_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'client_id' => 'Client ID',
            'auth_code' => 'Auth Code',
            'secret_key' => 'Secret Key',
            'access_token' => 'Access Token',
            'refresh_token' => 'Refresh Token',
            'tokens_create' => 'Tokens Create',
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

    public static function getAuth($clientId, $userId)
    {
        $auth = self::findOne([
            'client_id' => $clientId,
            'user_id' => $userId,
        ]);

        return $auth;
    }
}
