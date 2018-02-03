<?php
$app['debug'] = true;
$app['log.level'] = Monolog\Logger::DEBUG;
$app['charset'] = 'UTF-8';
$app['locale'] = 'es';
$app['api.version'] = "v1.0.0";
$app['api.endpoint'] = "/api";
$app['dbs.options'] = array(
  "db_usuarios" => array(
    "driver" => "pdo_mysql",
    "user" => "usr_api_obras",
    "password" => "90pSF0DAqwqVNmCP",
    "dbname" => "api_obras",
    "host" => "localhost",
    "port" => 3306,
    'driverOptions' => array(
      1002 => 'SET NAMES utf8'
    )
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

// url basica del frontend
$app['app.frontend.url_base'] = "http://localhost:5555/";