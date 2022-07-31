<?php

namespace app\modules\rest\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "client".
 *
 * @property int $id
 * @property string|null $client_name
 * @property string|null $client_id
 * @property string|null $client_secret
 * @property string|null $redirect_uri
 *
 */
class Client extends ActiveRecord implements IdentityInterface
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
                $this->client_id = Yii::$app->getSecurity()->generateRandomString();
                $this->client_secret = Yii::$app->getSecurity()->generateRandomString();
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
        return 'client';
    }

    public function fields()
    {
        $fields = parent::fields();

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_name', 'redirect_uri'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['client_name', 'client_id', 'client_secret', 'redirect_uri'], 'string', 'max' => 255],
            [['client_name', 'redirect_uri'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_name' => 'Client Name',
            'client_id' => 'Client ID',
            'client_secret' => 'Client Secret',
            'redirect_uri' => 'Redirect Uri',
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

    public static function getClient($clientId)
    {
        $client = self::findOne(['client_id' => $clientId]);

        return $client;
    }

    // Для проверки аутентификации клиента по Basic-Auth
    public static function checkClient($username, $password)
    {
        $client = self::findOne(['client_id' => $username, 'client_secret' => $password]);

        return $client;
    }
}
