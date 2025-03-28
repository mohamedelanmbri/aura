<?php
include('db.php');

if (isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $items_sql = "SELECT p.product_name, oi.quantity, oi.price, oi.size, oi.color, p.main_img_url
                  FROM order_item oi
                  JOIN products p ON oi.product_id = p.product_id
                  WHERE oi.order_id = ?";
    $stmt = $conn->prepare($items_sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $items_result = $stmt->get_result();

    while ($item = $items_result->fetch_assoc()) {
        echo "<div class='card mb-2'>
                <div class='card-body'>
                    <h5 class='card-title'>{$item['product_name']}</h5>
                    <p>Quantity: {$item['quantity']}</p>
                    <p>Price: " . number_format($item['price'], 2) . " MAD</p>
                    <p>Size: {$item['size']}</p>
                    <p>Color: {$item['color']}</p>
                    <img src='admin/{$item['main_img_url']}' alt='Product Image' class='img-thumbnail' style='width: 100px; height: auto;'>
                </div>
              </div>";
    }
}
?>
