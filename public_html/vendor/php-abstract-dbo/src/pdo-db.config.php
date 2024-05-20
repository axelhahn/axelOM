<?php
/*

https://www.php.net/manual/de/pdo.construct.php

public PDO::__construct(
    string $dsn,
    ?string $username = null,
    ?string $password = null,
    ?array $options = null
)


uri:file:///pfad/zur/dsndatei


Aufbauen einer Verbindung zu einer MySQL-Datenbank über den Aufruf des Treibers
$dsn = 'mysql:dbname=testdb;host=127.0.0.1';
$user = 'dbuser';
$password = 'dbpass';


Aufbauen einer Verbindung zu einer ODBC-Datenbank mittels Aufruf des URIs
$dsn = 'uri:file:///usr/local/dbconnect';
$user = '';
$password = '';

$dbh = new PDO($dsn, $user, $password);


$_SETTINGS = parse_ini_file('./settings.ini', true);

$db = new \PDO(
  "mysql:hostname={$_SETTINGS['db']['host']};dbname={$_SETTINGS['db']['name']}",
  $_SETTINGS['db']['user'],
  $_SETTINGS['db']['pass'],
  [
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'"
  ]
);

[db]
host = "localhost"
name = "dbname"
user = "dbuser"
pass = "dbpassword"



$dsn = 'sqlite:D:\Databases\non_existing.db';   // file does not exist

try {
    $dbh = new PDO($dsn);
}
catch(PDOException $e) {
    print $e->getMessage();
}


*/

return [
    // see https://www.php.net/manual/de/pdo.construct.php
    'dsn' => 'mysql:host=addons-web-db;dbname=addons-web;charset=utf8',
    // 'dsn' => 'sqlite:'.__DIR__.'/../../../protected/data/my-example-app.sqlite3',
    'user' =>'addons-web',
    'password'=>'mypassword',
    'options' => []
];