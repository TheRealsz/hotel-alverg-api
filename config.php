<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Accept, Authorization, X-Requested-With, X-Auth-Token, Origin, Application");

$conn = new PDO('mysql:host=localhost;dbname=hotel_alverg', 'root', '');