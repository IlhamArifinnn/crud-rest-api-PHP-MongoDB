<?php

// Require headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database file
include_once 'mongodb_config.php';

$dbname = 'toko';
$collection = 'barang';

// DB connection
$db = new DbManager();
$conn = $db->getConnection();

// Read input data
$data = json_decode(file_get_contents("php://input"), false);

// Validate input
if (!isset($data->where)) {
   echo json_encode(["message" => "Missing 'where' field."]);
   exit;
}

$id = $data->where;

try {
   // Delete record
   $delete = new MongoDB\Driver\BulkWrite();
   $delete->delete(
      ['_id' => new MongoDB\BSON\ObjectId($id)],
      ['limit' => 1] // Set limit to 1 to delete a single document
   );

   $result = $conn->executeBulkWrite("$dbname.$collection", $delete);

   // Verify
   if ($result->getDeletedCount() > 0) {
      echo json_encode(["message" => "Record deleted successfully."]);
   } else {
      echo json_encode(["message" => "No record found to delete."]);
   }
} catch (MongoDB\Driver\Exception\Exception $e) {
   echo json_encode(["message" => "Error: " . $e->getMessage()]);
}
