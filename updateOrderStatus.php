<?php
include 'db.php'; // Include your database connection file

if (isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    // Update the order status to confirmed
    $stmt = $conn->prepare("UPDATE orders SET order_status = 'confirmed' WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Order status updated successfully.";
    } else {
        echo "Failed to update order status.";
    }
}
?>
