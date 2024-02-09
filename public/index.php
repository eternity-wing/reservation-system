<?php

use ReservationSystem\Infrastructure\Controller\AppointmentController;

require "../bootstrap.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

if ($uri[1] !== 'appointments') {
    header("HTTP/1.1 404 Not Found");
    exit();
}

try {
    $controller = AppointmentController::createDefault();
    $controller->processRequest($_SERVER["REQUEST_METHOD"], json_decode(file_get_contents('php://input'), true) ?? []);
} catch (JsonException $e) {
    var_dump($e->getMessage());
}