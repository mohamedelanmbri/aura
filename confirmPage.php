<?php
include 'db.php'; // Include your database connection file

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Verify the order ID
    $stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Aura</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #f9f9f9, #eaeaea);
            color: #333;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }
        .confirmation-container {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            padding: 30px;
            max-width: 500px;
            text-align: center;
            animation: fadeIn 0.8s ease-out;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .confirmation-container h1 {
            font-size: 2.8rem;
            color: #555;
            margin-bottom: 20px;
        }
        .confirmation-container p {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 30px;
        }
        .btn-success {
            background-color: #28a745;
            border: none;
            padding: 12px 30px;
            font-size: 1rem;
            font-weight: bold;
            border-radius: 25px;
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
            transition: all 0.3s ease;
        }
        .btn-success:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 7px 20px rgba(40, 167, 69, 0.6);
        }
        .modal-content {
            border: none;
            border-radius: 15px;
            text-align: center;
            animation: popIn 0.5s ease-out;
        }
        @keyframes popIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        .modal-header {
            justify-content: center;
            border-bottom: none;
        }
        .modal-header .checkmark {
            font-size: 4.5rem;
            color: #28a745;
        }
        .modal-body h5 {
            font-size: 1.8rem;
            color: #555;
            margin-bottom: 10px;
        }
        .modal-body p {
            color: #666;
        }
        .modal-footer {
            border-top: none;
            justify-content: center;
        }
        .btn-close-modal {
            background-color: #28a745;
            color: #fff;
            padding: 10px 20px;
            font-size: 1rem;
            border: none;
            border-radius: 25px;
            transition: background-color 0.3s ease;
        }
        .btn-close-modal:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <h1>Thank You for Your Order!</h1>
        <p>Your order has been received and is being processed. Please confirm your order to proceed.</p>
        <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#confirmationModal" onclick="confirmOrder()">Confirm Order</button>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="checkmark"><i class="fa-solid fa-circle-check"></i></span>
                </div>
                <div class="modal-body">
                    <h5>Order Confirmed!</h5>
                    <p>Your order has been successfully confirmed. We’ll notify you once it’s ready to ship.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-close-modal" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmOrder() {
            var orderId = <?php echo $order_id; ?>;
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "updateOrderStatus.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    console.log("Order status updated successfully.");
                }
            };
            xhr.send("order_id=" + orderId);
        }
    </script>
</body>
</html>

<?php      
    } else {
        echo "Invalid order ID.";
    }
}
?>