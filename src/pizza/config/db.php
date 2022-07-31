<?php

return [
//    'class' => 'yii\db\Connection',
//    'dsn' => 'mysql:host=mysql-8.0;dbname=mytest',
//    'username' => 'root',
//    'password' => 'secret',
//    'charset' => 'utf8',
//    'on afterOpen' => function($event) { 
//        $event->sender->createCommand("SET time_zone='Europe/Moscow';")->execute();
//    },

   'class' => 'yii\db\Connection',
   'dsn' => $_ENV['DB_DSN'],
   'username' => $_ENV['DB_USERNAME'],
   'password' => $_ENV['DB_PASSWORD'],
   'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
