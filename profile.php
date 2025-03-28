<?php
include('db.php');
include('session_check.php');

// Create a database connection
$cnx = new mysqli('localhost', 'root', '', 'aura');
if ($cnx->connect_error) {
    die("Connection failed: " . $cnx->connect_error);
}

// Use the correct session variable
$user_id = $_SESSION['user_id'];
$stmt = $cnx->prepare("SELECT * FROM user WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <?php include("navbar.php"); ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <?php include("profilesidebar.php"); ?>
            </div>
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div id="content-area">
                    <!-- Personal Info Section -->
                    <div id="personal-info" class="content">
                        <h3>Personal Info</h3>
                        <?php
                        if ($data = $res->fetch_assoc()) {
                        ?>
                        <form id="personal-info-form" method="post" action="update_personal_info.php">
                            <div class="form-group">
                                <label for="first-name">First Name</label>
                                <input type="text" class="form-control" id="first-name" name="first-name" value="<?php echo htmlspecialchars($data['user_firstname']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="last-name">Last Name</label>
                                <input type="text" class="form-control" id="last-name" name="last-name" value="<?php echo htmlspecialchars($data['user_lastname']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($data['user_email']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($data['user_phonenum']); ?>">
                            </div>
                        </form>
                        <?php
                        } else {
                            echo "<p>No user information found.</p>";
                        }
                        ?>
                    </div>
                    
                    <!-- Orders Section -->
                    <div id="orders" class="content" style="display: none;">
                        <h3>My Orders</h3>
                        <div id="order-list" class="list-group">
                            <?php
                            $orders_sql = "SELECT order_id, total_amount, order_status, creation_date FROM orders WHERE user_id = ?";
                            $orders_stmt = $cnx->prepare($orders_sql);
                            $orders_stmt->bind_param("i", $user_id);
                            $orders_stmt->execute();
                            $orders_result = $orders_stmt->get_result();

                            while ($order = $orders_result->fetch_assoc()):
                            ?>
                                <a href="#" class="list-group-item list-group-item-action" onclick="showOrderDetails(<?= $order['order_id'] ?>, '<?= htmlspecialchars($order['order_status']) ?>', '<?= htmlspecialchars($order['creation_date']) ?>', '<?= htmlspecialchars($order['total_amount']) ?>')">
                                    Order <?= $order['order_id'] ?> 
                                </a>
                            <?php endwhile; ?>
                        </div>
                        <div id="order-details" class="mt-4" style="display:none;">
                            <h4 id="order-id"></h4>
                            <p>Status: <span id="order-status"></span></p>
                            <p>Date: <span id="order-date"></span></p>
                            <p>Total Amount: <span id="total-amount"></span></p>
                            <div id="order-items"></div>
                        </div>
                    </div>
                    <!-- (Existing code remains unchanged) -->
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery-3.7.1.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#personal-info-tab").click(function() {
                $(".content").hide();
                $("#personal-info").show();
                $(".nav-link").removeClass("active");
                $(this).addClass("active");
            });

            $("#orders-tab").click(function() {
                $(".content").hide();
                $("#orders").show();
                $(".nav-link").removeClass("active");
                $(this).addClass("active");
            });

            // Initially display personal info
            $("#personal-info-tab").click();
        });

        function showOrderDetails(orderId, status, date, total) {
            $.ajax({
                url: 'get_order_items.php',
                type: 'POST',
                data: { order_id: orderId },
                success: function(response) {
                    $("#order-id").text("Order " + orderId);
                    $("#order-status").text(status);
                    $("#order-date").text(date);
                    $("#total-amount").text(total);
                    $("#order-items").html(response);
                    $("#order-details").show();
                }
            });
        }
    </script>
</body>
</html>
