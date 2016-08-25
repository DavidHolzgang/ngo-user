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

  public function testFindIdentityByAccessTokenFailsForInvalidToken()
  {
    $expectedUser = $this->_user->findIdentityByAccessToken('token');
    $this->assertNull($expectedUser);
  }
  
  // findIdentityByAccessToken($token)
  public function testFindIdentityByAccessTokenSucceedsForValidToken()
  {
    // id 1 (ngoadmin/admin) should always be present    
    $token = 'validDemoToken';
    $expectedUser = $this->_user->findIdentityByAccessToken($token);
    $this->assertNotNull($expectedUser);

  } 
}
