<?php
include('db.php'); // Include database connection
include('session_check.php'); // Ensure user is authenticated

// Redirect to basket if the basket is empty
if (empty($_SESSION['basket'])) {
    header("Location: basket1.php");
    exit;
}

// Initialize variables
$errors = [];
$total_amount = 0;
$shipping_cost = 50.00;

// Calculate totals
foreach ($_SESSION['basket'] as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}
$final_total = $total_amount + $shipping_cost; // Final total including shipping

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_token'];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token.");
    }

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    // Sanitize and validate inputs
    $user_id = $_SESSION['user_id'];
    $first_lastname = trim($_POST['first_lastname'] ?? '');
    $user_address = trim($_POST['user_adress'] ?? '');
    $user_neighborhood = trim($_POST['user_neighborhood'] ?? '');
    $user_city = trim($_POST['user_city'] ?? '');
    $user_region = trim($_POST['user_region'] ?? '');
    $user_zipcode = trim($_POST['user_zipcode'] ?? '');
    $user_phonenum = trim($_POST['user_phonenum'] ?? '');

    // Basic validation
    if (empty($first_lastname) || empty($user_address) || empty($user_neighborhood) || empty($user_city) || empty($user_region) || empty($user_zipcode)) {
        $errors[] = "All required fields must be filled out.";
    }

    // Further validation can be added here (e.g., regex for phone number, zipcode)

    if (empty($errors)) {
        // Prepare and execute insert into user_address table
        $stmt = $conn->prepare("INSERT INTO user_adress (user_id, first_lastname, user_adress, user_neighborhood, user_city, user_region, user_zipcode, user_phonenum) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt === false) {
            die("Prepare failed: (" . $conn->errno . ") " . htmlspecialchars($conn->error));
        }
        $stmt->bind_param("isssssss", $user_id, $first_lastname, $user_address, $user_neighborhood, $user_city, $user_region, $user_zipcode, $user_phonenum);
        if (!$stmt->execute()) {
            $errors[] = "Failed to save address information.";
        }
        $stmt->close();

        // Prepare and execute insert into orders table
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, order_status, creation_date) VALUES (?, ?, 'pending', NOW())");
        if ($stmt === false) {
            die("Prepare failed: (" . $conn->errno . ") " . htmlspecialchars($conn->error));
        }
        $stmt->bind_param("id", $user_id, $final_total);
        if (!$stmt->execute()) {
            $errors[] = "Failed to create order.";
        }
        $order_id = $stmt->insert_id;
        $stmt->close();

        // Store order_id and total_amount in the session for use in confirm_order.php
        $_SESSION['order_id'] = $order_id;
        $_SESSION['total_amount'] = $final_total;

        // Insert each item in the basket into the order_item table
        foreach ($_SESSION['basket'] as $item) {
            $product_id = (int)$item['product_id'];
            $quantity = (int)$item['quantity'];
            $price = (float)$item['price'];
            $size = htmlspecialchars($item['size']);
            $color = htmlspecialchars($item['color']);

            $stmt = $conn->prepare("INSERT INTO order_item (order_id, product_id, quantity, price, size, color) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt === false) {
                die("Prepare failed: (" . $conn->errno . ") " . htmlspecialchars($conn->error));
            }
            $stmt->bind_param("iiidss", $order_id, $product_id, $quantity, $price, $size, $color);
            if (!$stmt->execute()) {
                $errors[] = "Failed to add item to order.";
            }
            $stmt->close();
        }

        if (empty($errors)) {
            // Clear the basket after successful order
            unset($_SESSION['basket']);

            // Redirect to confirmation page
            header("Location: confirm_order.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Page</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="order.css">
</head>
<body>
    <?php include("navbar.php"); ?>

    <div class="container mt-5">
        <h1 class="text-center">AURA</h1>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form action="order.php" method="POST">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <!-- Address Information -->
            <div class="card">
                <div class="card-header">
                    <h2>1. Address Information</h2>
                </div>
                <div class="card-body">
                    <!-- Address Fields -->
                    <div class="form-group">
                        <label for="first_lastname">First and Last Name</label>
                        <input type="text" class="form-control" id="first_lastname" name="first_lastname" required value="<?= isset($_POST['first_lastname']) ? htmlspecialchars($_POST['first_lastname']) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="user_adress">Address</label>
                        <input type="text" class="form-control" id="user_adress" name="user_adress" required value="<?= isset($_POST['user_adress']) ? htmlspecialchars($_POST['user_adress']) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="user_neighborhood">Neighborhood</label>
                        <input type="text" class="form-control" id="user_neighborhood" name="user_neighborhood" required value="<?= isset($_POST['user_neighborhood']) ? htmlspecialchars($_POST['user_neighborhood']) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="user_city">City</label>
                        <input type="text" class="form-control" id="user_city" name="user_city" required value="<?= isset($_POST['user_city']) ? htmlspecialchars($_POST['user_city']) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="user_region">Region</label>
                        <input type="text" class="form-control" id="user_region" name="user_region" required value="<?= isset($_POST['user_region']) ? htmlspecialchars($_POST['user_region']) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="user_zipcode">Zip Code</label>
                        <input type="number" class="form-control" id="user_zipcode" name="user_zipcode" required value="<?= isset($_POST['user_zipcode']) ? htmlspecialchars($_POST['user_zipcode']) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="user_phonenum">Phone Number</label>
                        <input type="number" class="form-control" id="user_phonenum" name="user_phonenum" value="<?= isset($_POST['user_phonenum']) ? htmlspecialchars($_POST['user_phonenum']) : '' ?>">
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-success mt-3">Continue</button>
        </form>
    </div>

    <!-- Optional JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXDZKaV2w/qYQhoS/yK5KrwQbF1n5xBiDXa6OlqvmLjga3O9WNVJQ3c4uGxAC5k" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-LtrjvnR4+oN8Qb6xe4kc7V2Dhw74a8Sh07UbwLaN1suh1Ryl0Rk1yCTG5mBsVYJF" crossorigin="anonymous"></script>
</body>
</html>
