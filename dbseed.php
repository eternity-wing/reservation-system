<?php

use ReservationSystem\Infrastructure\Repository\Connection;

require 'bootstrap.php';

$statement = <<<EOS
    CREATE TABLE `appointments` (
      `schedule` datetime NOT NULL,
      `user_id` varchar(50) NOT NULL,
      `createdAt` datetime NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ALTER TABLE `appointments`
      ADD PRIMARY KEY (`schedule`),
      ADD KEY `user_id` (`user_id`);
    COMMIT;
EOS;

try {
    $createTable = Connection::make()->exec($statement);
    echo "Success!\n";
} catch (\PDOException $e) {
    exit($e->getMessage());
}