<?php

// /controllers/UserController.php

namespace app\controllers;

/*
 * The MIT License
 *
 * Copyright 2016 dholzgang.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

use Yii;
use app\models\User;
use app\models\LoginForm;
use yii\web\ForbiddenHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\ServerErrorHttpException;
use yii\filters\auth\HttpBasicAuth;
use yii\rest\ActiveController;

/**
 * REST User controller for the `v1` module
 */
class UserController extends ActiveController
{

  public $modelClass = 'app\models\User';

  /**
   * @inheritdoc
   */
  public function behaviors()
  {
    $behaviors = parent::behaviors();

    $behaviors['authenticator'] = [
        'class' => HttpBasicAuth::className(),
        'except' => ['index', 'login', 'create'],
    ];

    return $behaviors;
  }

  /**
   * Login action.
   *
   * @return string
   */
  public function actionLogin()
  {
    Yii::trace('entering login -- parameters are: ' . print_r(Yii::$app->request->post(), true), 'controllers/UserController/actionLogin');

    $model = new LoginForm();
    $inputArray = Yii::$app->request->post();
    $model->username = $inputArray['username'];
    $model->password = $inputArray['password'];
    
    if ($model->login()) {
      \Yii::$app->response->format = \yii\web\response::FORMAT_JSON;
      return [
          'message' => 'success',
          'token' => $model->getAccessToken(),
      ];
    }
    throw new \yii\web\UnauthorizedHttpException; // login fails here
  }

  /**
   * Logout action.
   *
   * @return string
   */
  public function actionLogout()
  {
    Yii::trace('entering logout ', __METHOD__);
    // for some reason, the access check isn't run for 'logout'??
    $this->checkAccess('logout');

    // set up to clean up user record
    $userId = Yii::$app->user->identity;

    if (!Yii::$app->user->logout()) {
      Yii::error('logout failed for user ' . print_r($userId, false), __METHOD__);
      throw new ServerErrorHttpException('logout failed');
    }
    $count = $userId->updateAttributes(['token_expiration' => null]);
    if ($count !== 1) {
      Yii::error('database update failed for user ' . print_r($userId, false), __METHOD__);
      throw new ServerErrorHttpException('database update failed');
    }
    return NULL; //logout succeeds -- kill authToken & return 200 response
  }

  /**
   * @inheritdoc
   * @codeCoverageIgnore
   */
  public function checkAccess($action, $model = null, $params = [])
  {
    Yii::trace('action is ' . print_r($action, true), __METHOD__);
    if (\Yii::$app->user->isGuest) {
      // a guest user can only view a user list, register or login
      switch ($action) {
        case 'create':
          break;
        case 'index':
          break;
        case 'login':
          break;
        default:
          throw new ForbiddenHttpException;      
      }
    } else {
      // action is OK, check for valid token
      $userId = Yii::$app->user->identity;
      $current_time = time();
      $expTime = strtotime($userId->token_expiration);
      if ($current_time > $expTime) {
        Yii::trace('token has expired', __METHOD__);
        throw new UnauthorizedHttpException('token has expired');
      }
      Yii::trace('token is valid', __METHOD__);
    }
  }

}
