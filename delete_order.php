<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli("localhost", "root", "", "aura");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the order_id is set
if (isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    // Delete related order items first to avoid foreign key constraint issues
    $deleteItemsSql = "DELETE FROM order_item WHERE order_id = ?";
    $stmt = $conn->prepare($deleteItemsSql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();

    // Delete the order itself
    $deleteOrderSql = "DELETE FROM orders WHERE order_id = ?";
    $stmt = $conn->prepare($deleteOrderSql);
    $stmt->bind_param("i", $order_id);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Order deleted successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to delete order."]);
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Order ID not provided."]);
}

$conn->close();
?>
