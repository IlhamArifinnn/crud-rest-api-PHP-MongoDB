<?php

// requred header
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database file
include_once "mongodb_config.php";

$dbname = 'toko';
$collection = 'barang';

// db connection
$db = new DbManager();
$conn = $db->getConnection();

// read all record
$filter = [];
$option = [];
$read = new MongoDB\Driver\Query($filter, $option);

// fetch record 
$records = $conn->executeQuery("$dbname.$collection", $read);

echo json_encode(iterator_to_array($records));
