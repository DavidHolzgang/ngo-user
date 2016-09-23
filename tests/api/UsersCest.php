<?php 
class UsersCest
{
  public function _before(\ApiTester $I)
  {
   $I->haveHttpHeader('Content-Type', 'application/json');    
  }

  public function listAllUsers(\ApiTester $I)
  {
    $I->seeInDatabase('user', array('username' => 'ngodemox@nowhere.com'));
    $I->wantTo('check list of all users');
    $I->sendGET('/users');
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson(['username' => 'ngodemox@nowhere.com']);
  }
  
}

