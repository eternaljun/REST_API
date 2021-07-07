<?php

namespace app\models;

use Yii;
use \yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $first_name
 * @property string $surname
 * @property string $phone
 * @property string $email
 * @property string $password
 * @property string $register_date
 * @property string $token
 */
class Users extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * {@inheritdoc}
     */
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
            [['first_name', 'surname', 'email'], 'string', 'max' => 80],
            [['phone'], 'string', 'max' => 11],
            [['email'], 'unique'],
            [['email'], 'email'],
            [['password'], 'string', 'max' => 255],
            [['register_date'], 'string', 'max' => 50],
            [['token'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'surname' => 'Surname',
            'phone' => 'Phone',
            'email' => 'Email',
            'password' => 'Password',
            'register_date' => 'Register Date',
            'token' => 'Token',
        ];
    }

    public static function findIdentity($id)
    {

    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return Users::findOne(['token' => $token]);
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
