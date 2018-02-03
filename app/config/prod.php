<?php
$app['debug'] = true;
$app['log.level'] = Monolog\Logger::ERROR;
$app['charset'] = 'UTF-8';
$app['locale'] = 'es';
$app['api.version'] = "v1.0.0";
$app['api.endpoint'] = "/api";
$app['db.options'] = array(
  "driver" => "",
  "user" => "",
  "password" => "",
  "dbname" => "",
  "host" => "",
  'driverOptions' => array(
      1002 => 'SET NAMES utf8'
  )
);
$app['swiftmailer.options'] = [
	'host' => 'localhost',
    'port' => '2515',
    //'username' => 'username',
    //'password' => 'password',
    'encryption' => null,
    'auth_mode' => null
];
$app['swiftmailer.use_spool'] = false;


$app['app.mail.sender.email'] = "adrianlopezalvez@hotmail.com";
$app['app.mail.sender.name'] = "ObrasSupCor";
