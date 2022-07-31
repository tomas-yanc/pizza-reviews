<?php

namespace app\modules\admin\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $imageFile;
    public $myuniqid;

    public function rules()
    {
        return [
            [['imageFile'], 'image', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg'],
        ];
    }
    
    public function upload()
    {
        $this->myuniqid = uniqid();
        $_GET['myuniqid'] = $this->myuniqid;
        
        if ($this->validate()) {
            $this->imageFile->saveAs('uploads/avatars/' . $this->myuniqid . '.' . $this->imageFile->extension);
            return true;
        } else {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'imageFile' => 'Аватар',
        ];
    }
}