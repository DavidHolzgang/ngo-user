<?php
namespace tests\models;
use app\models\User;

class UserTest extends \Codeception\Test\Unit
{
    public function testFindUserById()
    {
        expect_that($user = User::findIdentity(2));
        expect($user->username)->equals('ngoadmin@redclover.com');

        expect_not(User::findIdentity(999));
    }

    public function testFindUserByAccessToken()
    {
        expect_that($user = User::findIdentityByAccessToken('eYVErsGcxLeIdYQSQigtIfz46V7ss5yN'));
        expect($user->username)->equals('ngoadmin@redclover.com');

        expect_not(User::findIdentityByAccessToken('non-existing'));        
    }

    public function testFindUserByUsername()
    {
        expect_that($user = User::findByUsername('ngoadmin@redclover.com'));
        expect_not(User::findByUsername('not-admin'));
    }

    /**
     * @depends testFindUserByUsername
     */
    public function testValidateUser($user)
    {
        $user = User::findByUsername('ngoadmin@redclover.com');
        expect_that($user->validateAuthKey('57ba124c3dc79'));
        expect_not($user->validateAuthKey('57b8dfc2256b1'));

        expect_that($user->validatePassword('admin'));
        expect_not($user->validatePassword('123456'));        
    }

}
