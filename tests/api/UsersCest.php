<?php 
class UsersCest
{
  public function _before(\ApiTester $I)
  {
   $I->haveHttpHeader('Content-Type', 'application/json');    
  }

  public function listAllUsers(\ApiTester $I)
  {
    $I->wantTo('check list all users');
    $I->sendGET('/users');
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
    $I->seeResponseIsJson();
    $I->seeResponseContainsJson(['username' => 'ngoadmin@redclover.com']);
  }
  
}


