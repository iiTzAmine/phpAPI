<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

include 'DbConnect.php';
$objDb = new DbConnect;
$conn = $objDb->connect();
$method = $_SERVER['REQUEST_METHOD'];
 
switch ($method) {

	case "GET":
        $sql = "SELECT * FROM users";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        if(isset($path[3]) && is_numeric($path[3])) {
            $sql .= " WHERE userID = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $path[3]);
            $stmt->execute();
            $users = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode($users);
        break;

	case "POST":
        $user = json_decode( file_get_contents('php://input') );
        print_r($user);
        $sql = "INSERT INTO users(userID, userFullName, userPhone, userEmail, userPassword, created_at) VALUES(null, :userFullName, :userPhone, :userEmail, :userPassword, :created_at)";
        $stmt = $conn->prepare($sql);
        $created_at = date('Y-m-d-h-m-s');
        $stmt->bindParam(':userFullName', $user->FullName);
        $stmt->bindParam(':userPhone', $user->Phone);
        $stmt->bindParam(':userEmail', $user->Email);
        $stmt->bindParam(':userPassword', $user->Password);
        $stmt->bindParam(':created_at', $user->created_at);
        if($stmt->execute()) {
            $response = ['status' => 1, 'message' => 'Record created successfully.'];
        } else {
            $response = ['status' => 0, 'message' => 'Failed to create record.'];
        }
        echo json_encode($response);
        break;

	case "DELETE":
        $sql = "DELETE FROM users WHERE userID = :id";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $path[4]);

        if($stmt->execute()) {
            $response = ['status' => 1, 'message' => 'Record deleted successfully.'];
        } else {
            $response = ['status' => 0, 'message' => 'Failed to delete record.'];
        }
        echo json_encode($response);
        break;

}