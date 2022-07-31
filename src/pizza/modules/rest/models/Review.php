<?php

namespace app\modules\rest\models;

use Yii;
use yii\helpers\Url;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;
use app\modules\rest\models\Auth;

/**
 * This is the model class for table "review".
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $title
 * @property string|null $body
 * @property string $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $user
 */
class Review extends \yii\db\ActiveRecord implements IdentityInterface 
{
    public const EXPIRE_ACCESS_TOKEN = 3600*24*7;

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
            $fields['user_id'],
            $fields['updated_at'],
        );
        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'review';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'title'], 'required'],
            [['user_id', 'created_at', 'updated_at'], 'integer'],
            [['body'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 8],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'title' => 'Title',
            'body' => 'Body',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        $authObject = Auth::findOne(['access_token' => $token]);
        if(($authObject->tokens_create + self::EXPIRE_ACCESS_TOKEN) > time()) {

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
}
