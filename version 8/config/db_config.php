<?php
return [
    'host' => 'localhost',
    'dbname' => 'eurotours',
    'username' => 'root',
    'password' => 'Ehw2019!',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]
];
