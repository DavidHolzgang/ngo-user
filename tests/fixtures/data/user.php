<?php

// tests/unit/fixtures/data/user.php

return [
    'admin' => [
        'id' => '1',
        'username' => 'ngoadmin@redclover.com',
        'password' => Yii::$app->getSecurity()->generatePasswordHash('admin'),
        'authkey' => 'validAuthKey',
        'access_token' => 'validAdminToken',
        'first_name' => 'Admin'
    ],
    'demo' => [
        'id' => '2',
        'username' => 'ngodemo@nowhere.com',
        'password' => Yii::$app->getSecurity()->generatePasswordHash('demo'),
        'authkey' => 'validUserKey',
        'access_token' => 'validDemoToken',
        'first_name' => 'Demo'
    ],
];

