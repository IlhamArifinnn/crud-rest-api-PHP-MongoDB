<?php

// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database file
include_once 'mongodb_config.php';

$dbname = 'toko';
$collection = 'barang';

// DB connection
$db = new DbManager();
$conn = $db->getConnection();

// Record to update
$data = json_decode(file_get_contents("php://input"), false); // Decode as object

if (!isset($data->fields) || !is_array($data->fields)) {
   echo json_encode(["message" => "Invalid fields structure."]);
   exit;
}

if (!isset($data->where)) {
   echo json_encode(["message" => "Missing 'where' field."]);
   exit;
}

$set_values = [];
foreach ($data->fields as $field) {
   foreach ($field as $key => $value) {
      $set_values[$key] = $value;
   }
}

if (empty($set_values)) {
   echo json_encode(["message" => "No fields to update."]);
   exit;
}

$id = $data->where;

// Update record
$update = new MongoDB\Driver\BulkWrite();
$update->update(
   ['_id' => new MongoDB\BSON\ObjectId($id)],
   ['$set' => $set_values],
   ['multi' => false, 'upsert' => false]
);

try {
   $writeResult = $conn->executeBulkWrite("$dbname.$collection", $update);
   if ($writeResult->getModifiedCount() > 0) {
      echo json_encode(["message" => "Record successfully updated."]);
   } else {
      echo json_encode(["message" => "Error while updating record."]);
   }
} catch (MongoDB\Driver\Exception\Exception $e) {
   echo json_encode(["message" => "Error: " . $e->getMessage()]);
}
