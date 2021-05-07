<?php

use App\Model\Connection;

require 'vendor/autoload.php';
require 'config/config.php';
require 'config/db.php';

try {
    $pdo = (new Connection())->getPdoConnection();
    if (file_exists(DB_DUMP_PATH)) {
        $sql = file_get_contents(DB_DUMP_PATH);
        $statement = $pdo->prepare($sql);
        $statement->execute();
    } else {
        print(DB_DUMP_PATH . ' file does not exist');
    }
} catch (PDOException $exception) {
    echo $exception->getMessage();
}
