<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;


class Login extends ActiveRecord
{
   public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            [['email'], 'string', 'max' => 80],
            [['email'], 'email'],
            [['password'], 'string', 'max' => 255],
        ];
    }
}
