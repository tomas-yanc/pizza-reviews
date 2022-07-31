<?php

namespace app\models;

use Yii;
use yii\base\Model;
 
class SignupForm extends Model 
{
    public $username;
    public $password;
    
    public function rules() 
    {
        return [
            [['username'], 'required', 'message' => 'Необходимо заполнить «Username».'],
            [['password'], 'required', 'message' => 'Необходимо заполнить «Password».'],
            ['username', 'unique', 'targetClass' => User::className(),  'message' => 'Логин уже занят.'],
            [['username'], 'trim'],
        ];
    }
    
    public function attributeLabels() 
    {
        return [
            'username' => 'Username',
            'password' => 'Password',
        ];
    }
}