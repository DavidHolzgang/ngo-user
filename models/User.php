<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $authkey
 * @property string $first_name
 * @property string $last_name
 * @property string $access-token
 * @property string $last_login_time
 * @property string $token_expiration
 */

class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'authkey'], 'required'],
            [['last_login_time', 'token_expiration'], 'safe'],
            [['username'], 'string', 'max' => 24],
            [['password'], 'string', 'max' => 126],
            [['authkey', 'access-token'], 'string', 'max' => 255],
            [['first_name'], 'string', 'max' => 64],
            [['last_name'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'authkey' => 'Auth Key',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'access-token' => 'Access Token',
            'last_login_time' => 'Last Login At',
            'token_expiration' => 'Token Expires At',
        ];
    }

    
    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public function fields()
    {
      $fields = parent::fields();
      
      if (Yii::$app->user->getId() !== $this->getId()) {
        unset($fields['password'], $fields['authkey'], $fields['accessToken']);
      }
      
      return $fields;
    }

    
    /**
     * setter for the password field
     * 
     * @param string $password  new password to be set
     */
    public function setPassword($password)
    {
      $this->password = Yii::$app->security->generatePasswordHash($password);
    }


    // implement IdentityInterface abstract methods
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
      return self::findOne($id);
    }

    
    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
      return self::findOne(['accessToken' => $token]);
    }

    
    /**
     * @inheritdoc
     */
    public function getId()
    {
      return $this->id;
    }

    
    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
      return $this->authkey;
    }

    
    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
      return ($this->authkey === $authKey);
    }

    
    // implement LoginForm utility methods
    /**
     * Find user by username
     * 
     * @param string $username
     * @return static | null
     */
    public static function findByUsername($username)
    {
      return self::findOne(['username' => $username]);
    }

    
    /**
     * Validate given password for a user
     * 
     * @param string $password  password to be validates
     * @return boolean true if password is valid for current user | false otherwise
     */
    public function validatePassword($password)
    {
      return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }
    
}
