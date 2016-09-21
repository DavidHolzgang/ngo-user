<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind login processing.
 * NOTE that this is not an actual form, but simply an abstraction for processing
 *
 * @property User|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = false;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        Yii::trace('entering login -- parameters are: ' . print_r($this, true), 'models/LoginForm/login');
      
        if ($this->validate()) {
            if (!Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0)) {
              return false;
            } else {
              /* TODO
               * set last login
               * create new access token
               * store new access token
               */
              $this->setLoginData();
              return true; // return success
            }
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }

    /**
     * Returns access token for [[username]]
     *
     * @return User access token | null
     */
    public function getAccessToken()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user->access_token;
    }

    /**
     * Returns access token for [[username]]
     *
     * @return User access token | null
     */
    public function setLoginData()
    {
      
      Yii::trace('entering set tracking data ', 'models/LoginForm/setLoginData');
      $format = 'Y-m-d h:m:s';
      $expDate = time() + (24 * 60 * 60);
      $updateData = [
          'last_login_time' => date($format),
          'token_expiration' => date($format, $expDate)
      ];
      Yii::trace('tracking data is: ' . print_r($updateData, true), 'models/LoginForm/setLoginData');
      // post to database
      $user = $this->getUser();
      $count = $user->updateAttributes($updateData);
      Yii::trace('reccords updated were: ' . print_r($count, true), 'models/LoginForm/setLoginData');
    
      return true;
    }
}
