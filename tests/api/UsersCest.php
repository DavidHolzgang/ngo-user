<?php 
class UsersCest
{
  public function _before(\ApiTester $I)
  {
    /* 
     * the Header should only be sent with GET queries (!??)
     * $I->haveHttpHeader('Content-Type', 'application/json');
     * 
     */    
  }

  public function listAllUsers(\ApiTester $I)
  {
    $I->seeInDatabase('user', array('username' => 'ngodemox@nowhere.com'));
    $I->wantTo('check list of all users');
    $I->haveHttpHeader('Content-Type', 'application/json');    
    $I->sendGET('/users');
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson(array('username' => 'ngodemox@nowhere.com'));
  }

    public function loginDemoUser(\ApiTester $I)
  {
    $I->seeInDatabase('user', array('username' => 'ngodemox@nowhere.com', 'token_expiration' => NULL));
    $I->wantTo('login Demo user');
    $I->sendPOST('/users/login', [
        'username' => 'ngodemox@nowhere.com',
        'password' => 'demo'
    ]);
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson(array('token' => 'validDemoToken'));
    $I->dontSeeInDatabase('user', array('username' => 'ngodemox@nowhere.com', 'token_expiration' => NULL));
  }

  public function loginUserFailsNoData(\ApiTester $I)
  {
    $I->seeInDatabase('user', array('username' => 'ngodemox@nowhere.com'));
    $I->wantTo('check that login must have username and password');
    $I->sendPOST('/users/login', array());
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNPROCESSABLE_ENTITY); // 422
  }

  public function loginUserFailsInvalidUser(\ApiTester $I)
  {
    $I->seeInDatabase('user', array('username' => 'ngodemox@nowhere.com'));
    $I->wantTo('check that login must have valid username');
    $I->sendPOST('/users/login', [
        'username' => 'ngodemo@nowhere.com',
        'password' => 'demo'
    ]);
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson(array('message' => 'invalid login parameters'));
  }

  public function loginUserFailsInvalidPassword(\ApiTester $I)
  {
    $I->seeInDatabase('user', array('username' => 'ngodemox@nowhere.com'));
    $I->wantTo('check that login must have valid password');
    $I->sendPOST('/users/login', [
        'username' => 'ngodemox@nowhere.com',
        'password' => 'invalid'
    ]);
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson(array('message' => 'invalid login parameters'));
  }

  // CREATE function
  public function createNewUser(\ApiTester $I)
  {
    $I->wantTo('create a new user');
    $I->sendPOST('/users', [
        'username' => 'novus@nowhere.com',
        'password' => 'NewMan',
        'first_name' => 'New',
        'last_name' => 'User'
    ]);
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::CREATED); // 201
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson(array('first_name' => 'New'));
    $I->seeInDatabase('user', array('username' => 'novus@nowhere.com'));
  }

  // READ function
  public function listOneUser(\ApiTester $I)
  {
    $I->seeInDatabase('user', array('username' => 'ngodemox@nowhere.com'));
    $I->dontSeeInDatabase('user', array('username' => 'ngodemox@nowhere.com', 'token_expiration' => NULL));
    $I->wantTo('retrieve existing user');
    $I->haveHttpHeader('Content-Type', 'application/json');    
    $I->amHttpAuthenticated('validDemoToken', '');    
    $I->sendGET('/users/2');
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson(array('authkey' => 'validDemoKey'));
  }

  // UPDATE function
  public function updateOneUser(\ApiTester $I)
  {
    $I->seeInDatabase('user', array('username' => 'ngodemox@nowhere.com'));
    $I->dontSeeInDatabase('user', array('username' => 'ngodemox@nowhere.com', 'token_expiration' => NULL));
    $I->seeInDatabase('user', array('username' => 'ngodemox@nowhere.com', 'last_name' => NULL));
    $I->wantTo('update an existing user');
    $I->amHttpAuthenticated('validDemoToken', '');    
    $I->sendPATCH('/users/2', [
        'last_name' => 'User'
    ]);
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson(array('last_name' => 'User'));
    $I->seeInDatabase('user', array('username' => 'ngodemox@nowhere.com', 'last_name' => 'User'));
  }

  // DELETE function
  public function deleteOneUser(\ApiTester $I)
  {
    $I->seeInDatabase('user', array('username' => 'novus@nowhere.com'));
    $I->wantTo('login the new user');
    $I->sendPOST('/users/login', [
        'username' => 'novus@nowhere.com',
        'password' => 'NewMan'
    ]);    
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
    $I->dontSeeInDatabase('user', array('username' => 'novus@nowhere.com', 'token_expiration' => NULL));
    $token = $I->grabFromDatabase('user', 'access_token', array('username' => 'novus@nowhere.com'));
    $I->wantTo('delete the new user');
    $I->amHttpAuthenticated($token, '');    
    $I->sendDELETE('/users/3');
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NO_CONTENT); // 204
    $I->dontSeeInDatabase('user', array('username' => 'novus@nowhere.com'));
  }

  /*
   * Token handling methods
   */
  // invalid token function
  public function accessFailsInvalidToken(\ApiTester $I)
  {
    $I->seeInDatabase('user', array('username' => 'ngodemox@nowhere.com'));
    $I->dontSeeInDatabase('user', array('username' => 'ngodemox@nowhere.com', 'token_expiration' => NULL));
    $I->wantTo('check that invalid token will not provide access');
    $I->haveHttpHeader('Content-Type', 'application/json');    
    $I->amHttpAuthenticated('invalidDemoToken', '');    
    $I->sendGET('/users/2');
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson(array('message' => 'Your request was made with invalid credentials.'));
  }

    // expired token function
  public function accessFailsExpiredToken(\ApiTester $I)
  {
    $I->wantTo('check that expired token will not provide access');
    $data = array(
        'username' => 'testuser@nowhere.com',
        'password' => 'nothing',
        'authkey' => 'authTestKey',
        'access_token' => 'validTestToken',
        'last_login_time' => '2016-09-20 00:00:00',
        'token_expiration' => '2016-09-21 00:00:00'
    );    
    $I->haveInDatabase('user', $data);
    $I->haveHttpHeader('Content-Type', 'application/json');    
    $I->amHttpAuthenticated('validTestToken', '');    
    $I->sendGET('/users/2');
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson(array('message' => 'token has expired'));
  }

    public function logoutDemoUser(\ApiTester $I)
  {
    $I->seeInDatabase('user', array('username' => 'ngodemox@nowhere.com'));
    $I->dontSeeInDatabase('user', array('username' => 'ngodemox@nowhere.com', 'token_expiration' => NULL));
    $I->wantTo('logout Demo user');
    $I->amHttpAuthenticated('validDemoToken', '');    
    $I->sendPOST('/users/logout');
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
    $I->seeInDatabase('user', array('username' => 'ngodemox@nowhere.com', 'token_expiration' => NULL));
  }

}

