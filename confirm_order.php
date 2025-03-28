<?php
session_start();
include 'db.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_method = $_POST['payment_method'];
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in the session
    $order_id = $_SESSION['order_id']; // Retrieve order_id from the session
    $total_amount = $_SESSION['total_amount']; // Retrieve total_amount from the session

    // Generate the confirmation link
    $confirmation_link = "http://localhost/dynamicwebsite/confirmPage.php?order_id=$order_id";

    // Retrieve user email from the database
    $stmt = $conn->prepare("SELECT user_email ,user_firstname ,user_lastname FROM user WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $user_email = $user['user_email'];
    $firstname = $user['user_firstname'];
    $lastname = $user['user_lastname'];

    // Send the confirmation email
    $to = $user_email;
    $subject = "Order Confirmation - Aura";
    $message = "Dear $firstname $lastname,
    
Thank you for your order with Aura! Your order has been successfully placed, and we are excited to get started on crafting your unique style statement.

Please confirm your order by clicking the link below:$confirmation_link

If you have any questions, feel free to contact our support team.

Warm regards,  
The Aura Team  ";
    $headers = "From: Aura";

    if ($payment_method === 'cash') {
        if (mail($to, $subject, $message, $headers)) {
            echo "A confirmation email has been sent to your email address.";
        } else {
            echo "Failed to send confirmation email.";
            error_log("Mail error: " . error_get_last()['message']);
        }
    }

    // Redirect to a confirmation page or dashboard
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Order</title>
    <link rel="stylesheet" href="order.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container {
            padding: 10px;
        }
        .card {
            margin: 10px 0;
        }
        h1, h2 {
            font-size: 1.5em;
        }
        p {
            font-size: 0.9em;
        }
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            .card {
                margin: 10px 0;
            }
            h1, h2 {
                font-size: 1.5em;
            }
            p {
                font-size: 0.9em;
            }
        }
    </style>
    <script>
        // Retrieve order summary details from session storage
        document.addEventListener("DOMContentLoaded", function() {
            const totalAmount = sessionStorage.getItem('totalAmount');
            const shippingCost = sessionStorage.getItem('shippingCost');
            const finalTotal = sessionStorage.getItem('finalTotal');

            if (totalAmount && shippingCost && finalTotal) {
                document.getElementById('totalAmount').textContent = `${parseFloat(totalAmount).toFixed(2)} MAD`;
                document.getElementById('shippingCost').textContent = `${parseFloat(shippingCost).toFixed(2)} MAD`;
                document.getElementById('finalTotal').textContent = `${parseFloat(finalTotal).toFixed(2)} MAD`;
            }

            // Set the correct total amount in the hidden input
            const totalAmountInput = document.getElementById('totalAmountInput');
            if (totalAmountInput) {
                totalAmountInput.value = finalTotal;
            }
        });
    </script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Confirm Your Order</h1>

        

        <div class="card mt-3">
            <div class="card-header">
                <h2>Order Summary</h2>
            </div>
            <div class="card-body">
                <p><strong>Total Amount:</strong> <span id="totalAmount">0.00 MAD</span></p>
                <p><strong>Shipping Costs:</strong> <span id="shippingCost">0.00 MAD</span></p>
                <p><strong>Final Total:</strong> <span id="finalTotal">0.00 MAD</span></p>
            </div>
        </div>

        <form action="confirm_order.php" method="POST">
            <input type="hidden" id="totalAmountInput" name="total_amount" value="">

            <div class="card mt-3">
                <div class="card-header">
                    <h2>Choose Payment Method</h2>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <input type="radio" id="cash" name="payment_method" value="cash" required>
                        <label for="cash">Cash on Delivery</label>
                    </div>
                    <div class="form-group">
                        <input type="radio" id="card" name="payment_method" value="card" disabled>
                        <label for="card">Card Payment (Not available)</label>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Confirm</button>
        </form>
    </div>
</body>
</html>