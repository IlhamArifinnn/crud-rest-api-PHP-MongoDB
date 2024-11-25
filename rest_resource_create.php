<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Request-With");


// include database file
include_once 'mongodb_config.php';

$dbname = 'toko';
$collection = 'barang';

// DB connection
$db = new DbManager();
$conn = $db->getConnection();

// record to add
$data = json_decode(file_get_contents("php://input"), true);

// insert record
$insert = new MongoDB\Driver\BulkWrite();
$insert->insert($data);

$result = $conn->executeBulkWrite("$dbname.$collection", $insert);

// verify
if ($result->getInsertedCount() == 1) {
   echo json_encode(
      [
         'message' => 'record successfully created'
      ]
   );
} else {
   echo json_encode(
      [
         'message' => 'error while saving record'
      ]
   );
}
