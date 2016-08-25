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
use yii\web\ForbiddenHttpException;
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
      'except' => ['index'],
    ];
    
    return $behaviors;
  }

  
  /**
   * @inheritdoc
   * @codeCoverageIgnore
   */
  public function checkAccess($action, $model = null, $params = [])
  {
    Yii::trace('action is ' . print_r($action, true), 'controllers/UserController');
    if (\Yii::$app->user->isGuest) {
      // a guest user can only view a user list
      if ($action !== 'index') {
        throw new ForbiddenHttpException;
      }
    }
  }
  
}
