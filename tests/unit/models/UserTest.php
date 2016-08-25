<?php
namespace app\tests\unit\models;

use yii\codeception\DbTestCase;
use app\models\User;
use app\tests\fixtures\UserFixture;

class UserTest extends DbTestCase
{
  /**
   * 
   * @var User
   */
  private $_user = null;

  public function fixtures()
  {
    return [
        'user' => UserFixture::className()
    ];
  }

  protected function setUp()
  {
    parent::setUp();
    //  use createFixtures() instead of loadFixtures()
    $this->_user = new \app\models\User;
  }
 

  // Start with basic find user tests
  public function testFindIdentityByIdSucceedsForValidId()
  {
    $expectedId = 1;
    // id 1 (ngoadmin/admin) should always be present
    $this->assertNotNull($this->_user);
    // check user record is returned
    $expectedUser = $this->_user->findIdentity($expectedId);
    $this->assertNotNull($expectedUser);
  }

  
  public function testFindIdentityByIdReturnsNullForInvalidId()
  {
    // id -1 should never be present
    $this->assertNull($this->_user->findIdentity(-1));
  }

  /**
   * @expectedException ErrorException
   */
  public function testFindIdentityByIdFailsIfNoParameters()
  {
    $this->_user->findIdentity();
  }

  // getId()
  public function testGetIdSucceedsForValidUser()
  {
    $expectedId = 1;
    // id 1 (ngoadmin/admin) should always be present
    $expectedUser = $this->_user->findIdentity($expectedId);

    $this->assertEquals($expectedId, $expectedUser->getId());
  }
  
  // findIdentityByAccessToken($token)
  public function testFindIdentityByAccessTokenSucceedsForValidToken()
  {
    // id 1 (ngoadmin/admin) should always be present    
    $token = 'validDemoToken';
    $expectedUser = $this->_user->findIdentityByAccessToken($token);
    $this->assertNotNull($expectedUser);

  } 

  public function testFindIdentityByAccessTokenFailsForInvalidToken()
  {
    $expectedUser = $this->_user->findIdentityByAccessToken('token');
    $this->assertNull($expectedUser);
  }
  
  // findByUsername($username)
  public function testFindByUsernameSucceedsForValidName()
  {
    // user 'ngodemo/demo' has been created by fixture    
    $username = 'ngodemo@nowhere.com';
    $expectedUser = $this->_user->findByUsername($username);
    $this->assertNotNull($expectedUser);

  } 

  public function testFindByUsernameFailsForInvalidName()
  {
    $expectedUser = $this->_user->findIdentityByAccessToken('demo');
    $this->assertNull($expectedUser);
  }
  
  // validateAuthKey($authKey)
  public function testValidateAuthKeySucceedsForValidKey()
  {
    $expectedId = 2;
    // user 2 'ngodemo/demo' has been created by fixture    
    $expectedUser = $this->_user->findIdentity($expectedId);
    $authKey = 'validDemoKey';
    $this->assertTrue($expectedUser->validateAuthKey($authKey));
  } 

  public function testValidateAuthKeyFailsForInvalidKey()
  {
    $authKey = 'invalidKey';
    $this->assertFalse($this->_user->validateAuthKey($authKey));
  }
  
  // validatePassword($password)
  public function testValidatePasswordSucceedsForValidPassword()
  {
    $expectedId = 2;
    // user 2 'ngodemo/demo' has been created by fixture    
    $expectedUser = $this->_user->findIdentity($expectedId);
    $password = 'demo';
    $this->assertTrue($expectedUser->validatePassword($password));
  } 

  public function testValidatePasswordFailsForInvalidPassword()
  {
    $expectedId = 2;
    // user 2 'ngodemo/demo' has been created by fixture    
    $expectedUser = $this->_user->findIdentity($expectedId);
    $password = 'notMyPassword';
    $this->assertFalse($expectedUser->validatePassword($password));
  }

  /**
   * @expectedException yii\base\InvalidParamException
   */
  public function testValidatePasswordFailsForNoCurrentUser()
  {
    // note that we have not set a current user, so $this->_user is uninitialized
    $password = 'demo';
    $this->_user->validatePassword($password);
  }
  
}
