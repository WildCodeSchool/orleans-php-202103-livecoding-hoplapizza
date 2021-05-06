<?php

use App\Model\Connection;

require 'vendor/autoload.php';
require 'config/config.php';
require 'config/db.php';

try {
    $pdo = (new Connection())->getPdoConnection();
    if (file_exists(__DIR__ . '/database.sql')) {
        $sql = file_get_contents(__DIR__ . '/database.sql');
        $statement = $pdo->prepare($sql);
        $statement->execute();
    } else {
        echo 'No database.sql file';
    }
} catch (PDOException $exception) {
    echo $exception->getMessage();
}
