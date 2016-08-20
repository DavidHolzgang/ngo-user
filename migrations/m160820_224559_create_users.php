<?php

use yii\db\Migration;

class m160820_224559_create_users extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'username' => $this->string(24)->notNull(),
            'password' => $this->string(126)->notNull(),
            'authkey' => $this->string(255)->notNull(),
            'first_name' => $this->string(64),
            'last_name' => $this->string(128),
            'access-token' => $this->string(255),
            'last_login_time' => $this->dateTime()->notNull()->defaultValue(0),
            'token_expiration' => $this->dateTime(),
        ]);
        
        // basic set of logins for testing
        $this->insert('user', [
           'username' => 'dholzgang',
            'password' => Yii::$app->getSecurity()->generatePasswordHash('r3dcl0v3r'),
            'authkey' => uniqid(),
            'access-token' => Yii::$app->getSecurity()->generateRandomString(),
        ]);
        $this->insert('user', [
           'username' => 'admin',
            'password' => Yii::$app->getSecurity()->generatePasswordHash('admin'),
            'authkey' => uniqid(),
            'access-token' =>  Yii::$app->getSecurity()->generateRandomString(),
        ]);
        $this->insert('user', [
           'username' => 'demo',
            'password' => Yii::$app->getSecurity()->generatePasswordHash('demo'),
            'authkey' => uniqid(),
            'access-token' =>  Yii::$app->getSecurity()->generateRandomString(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('user');
    }
}
