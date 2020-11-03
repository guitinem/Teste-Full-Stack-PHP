<?php
require "../config.php";
use Src\Controller\CarController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Get url
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

$request_var = [
    'method' => $_SERVER["REQUEST_METHOD"],
    'path_params' => $uri,
    'query_params' => $_SERVER["QUERY_STRING"] ?? null,
    'db' => $dbConnection
];

$controller = new CarController($request_var);
$controller->processRequest();
