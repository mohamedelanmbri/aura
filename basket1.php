<?php
session_start();
include('db.php'); 


if (!isset($_SESSION['basket'])) {
    $_SESSION['basket'] = [];
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_index'])) {
    $delete_index = $_POST['delete_index'];


    if (isset($_SESSION['basket'][$delete_index])) {
        unset($_SESSION['basket'][$delete_index]);
        $_SESSION['basket'] = array_values($_SESSION['basket']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $color = $_POST['color'];
    $size = $_POST['size'];
    $quantity = $_POST['quantity'];

    $image_sql = "SELECT main_img_url FROM products WHERE product_id = ?";
    $img_stmt = $conn->prepare($image_sql);
    $img_stmt->bind_param("i", $product_id);
    $img_stmt->execute();
    $img_result = $img_stmt->get_result();
    $img_row = $img_result->fetch_assoc();

    if ($img_row && !empty($img_row['main_img_url'])) {
        $main_img_url = 'admin/' . $img_row['main_img_url'];
    } else {
        $main_img_url = 'default_image.jpg';
    }

    $item = [
        'product_id' => $product_id,
        'product_name' => $product_name,
        'price' => $price,
        'color' => $color,
        'size' => $size,
        'quantity' => $quantity,
        'main_img_url' => $main_img_url
    ];

    $_SESSION['basket'][] = $item;
}

$total_amount = 0;
$total_items = count($_SESSION['basket']);
$shipping_cost = 50.00;

foreach ($_SESSION['basket'] as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}

$final_total = $total_amount + $shipping_cost;

// Save totals in session storage using JavaScript
$_SESSION['total_amount'] = $total_amount;
$_SESSION['final_total'] = $final_total;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart - Aura</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="basket.css">
    <script>
        // Save order summary details to session storage
        function saveOrderSummary() {
            const totalAmount = <?= json_encode($total_amount) ?>;
            const shippingCost = <?= json_encode($shipping_cost) ?>;
            const finalTotal = <?= json_encode($final_total) ?>;

            sessionStorage.setItem('totalAmount', totalAmount);
            sessionStorage.setItem('shippingCost', shippingCost);
            sessionStorage.setItem('finalTotal', finalTotal);
        }
        window.onload = saveOrderSummary;
    </script>
</head>
<body>
    <?php include("navbar.php"); ?>

    <main class="container mt-5">
        <h1>My Cart (<?= $total_items ?> Items)</h1>

        <div class="row">
            <div class="col-md-8">
                <?php if ($total_items > 0): ?>
                    <?php foreach ($_SESSION['basket'] as $index => $item): ?>
                        <div class="card mb-3">
                            <div class="row no-gutters align-items-center">
                                <div class="col-md-4">
                                    <img src="<?= htmlspecialchars($item['main_img_url']) ?>" alt="Product Image" class="img-fluid">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($item['product_name']) ?></h5>
                                        <p class="card-text">
                                            <strong>Size:</strong> <?= htmlspecialchars($item['size']) ?><br>
                                            <strong>Color:</strong> <?= htmlspecialchars($item['color']) ?><br>
                                            <strong>Quantity:</strong> <?= htmlspecialchars($item['quantity']) ?>
                                        </p>
                                        <p class="card-text"><strong>Price:</strong> <?= htmlspecialchars(number_format($item['price'], 2)) ?> MAD</p>
                                        <form method="POST" action="basket1.php" style="display:inline;">
                                            <input type="hidden" name="delete_index" value="<?= $index ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Your cart is empty.</p>
                <?php endif; ?>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Order Summary</h5>
                        <p class="card-text"><strong>Total article:</strong> <?= htmlspecialchars(number_format($total_amount, 2)) ?> MAD</p>
                        <p class="card-text"><strong>Shipping costs:</strong> <?= htmlspecialchars(number_format($shipping_cost, 2)) ?> MAD</p>
                        <p class="card-text"><strong>Total:</strong> <?= htmlspecialchars(number_format($final_total, 2)) ?> MAD</p>
                        <a href="order.php" class="btn btn-dark btn-block">Proceed to the Checkout Step</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
