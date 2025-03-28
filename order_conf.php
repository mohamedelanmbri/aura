<?php
session_start();
include('db.php'); // Include database connection

// Check if session has order details; redirect if not
if (empty($_SESSION['order_summary'])) {
    header("Location: basket1.php");
    exit;
}

// Order details from session
$order_summary = $_SESSION['order_summary'];
$total_amount = $order_summary['total_amount'];
$shipping_cost = $order_summary['shipping_cost'];
$final_total = $order_summary['final_total'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'];
    
    if ($payment_method === 'cash') {
        // Process Cash on Delivery
        $user_email = $_SESSION['user_email']; // Assuming user email is stored in session

        // Insert into `orders` table
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, order_status, creation_date) VALUES (?, ?, 'pending', NOW())");
        $stmt->bind_param("id", $_SESSION['user_id'], $final_total);
        $stmt->execute();
        $order_id = $stmt->insert_id; // Get newly created order ID

        // Insert items into `order_item` table
        foreach ($order_summary['items'] as $item) {
            $stmt = $conn->prepare("INSERT INTO order_item (order_id, product_id, quantity, price, size, color) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiidss", $order_id, $item['product_id'], $item['quantity'], $item['price'], $item['size'], $item['color']);
            $stmt->execute();
        }

        // Send confirmation email
        $confirm_link = "https://yourwebsite.com/confirm_order.php?order_id=$order_id&token=" . md5($order_id);
        mail($user_email, "Order Confirmation", "Click here to confirm your order: $confirm_link");

        echo "<script>alert('Order placed! A confirmation link has been sent to your email.'); window.location = 'thank_you.php';</script>";

    } elseif ($payment_method === 'card') {
        echo "<script>alert('Card payment is not available at the moment. Please select Cash on Delivery.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="order.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">AURA - Confirm Order</h1>
        <form action="" method="POST">
            <!-- Order Summary Section -->
            <div class="card mt-3">
                <div class="card-header">
                    <h2>Order Summary</h2>
                </div>
                <div class="card-body">
                    <p><strong>Total Articles:</strong> <?= htmlspecialchars(number_format($total_amount, 2)) ?> MAD</p>
                    <p><strong>Shipping Costs:</strong> <?= htmlspecialchars(number_format($shipping_cost, 2)) ?> MAD</p>
                    <p><strong>Item Total:</strong> <?= htmlspecialchars(number_format($final_total, 2)) ?> MAD</p>
                </div>
            </div>

            <!-- Payment Method Section -->
            <div class="card mt-3">
                <div class="card-header">
                    <h2>Payment Method</h2>
                </div>
                <div class="card-body">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" value="cash" id="cash" required>
                        <label class="form-check-label" for="cash">Cash on Delivery</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" value="card" id="card" required>
                        <label class="form-check-label" for="card">Card Payment (Not available)</label>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary mt-3">Confirm</button>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
