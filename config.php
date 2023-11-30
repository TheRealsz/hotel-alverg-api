<?php
header('Access-Control-Allow-Origin: http://localhost:3000');
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Accept, Authorization, X-Requested-With, X-Auth-Token, Origin, Application");
date_default_timezone_set('America/Sao_Paulo');
$conn = new PDO('mysql:host=localhost;dbname=hotel_alverg', 'root', '');