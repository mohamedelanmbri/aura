<?php include('session_check1.php'); ?>
<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli("localhost", "root", "", "aura");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check for the 'action' parameter in the request to determine what action to take
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'fetch') {
    // Fetch orders with associated items for DataTables
    $sql = "SELECT o.order_id, o.total_amount, o.order_status, o.creation_date, u.user_firstname, u.user_lastname,
                   GROUP_CONCAT(CONCAT('Product ID: ', oi.product_id, ', Product Name: ', p.product_name, ', Quantity: ', oi.quantity, ', Size: ', oi.size, ', Color: ', oi.color) SEPARATOR '<br>') AS items
            FROM orders o
            JOIN user u ON o.user_id = u.user_id
            JOIN order_item oi ON o.order_id = oi.order_id
            JOIN products p ON oi.product_id = p.product_id
            GROUP BY o.order_id";
    
    $result = $conn->query($sql);
    
    $data = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                "order_id" => $row['order_id'],
                "user_firstname" => $row['user_firstname'],
                "user_lastname" => $row['user_lastname'],
                "total_amount" => $row['total_amount'],
                "order_status" => $row['order_status'],
                "creation_date" => $row['creation_date'],
                "items" => $row['items']
            ];
        }
    }
    
    echo json_encode(["data" => $data]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders</title>
    <!-- Include Bootstrap and DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
</head>
<body class="bg-light">
    <div class="container my-4">
    <h1><a href="../admin_profile.php" class="text-center" style="text-decoration: none;">Go Back to Profile</a></h1>
        <h2 class="text-center">Manage Orders</h2>
        <table id="ordersTable" class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Order ID</th>
                    <th>User</th>
                    <th>Total Amount</th>
                    <th>Order Status</th>
                    <th>Creation Date</th>
                    <th>Items</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $('#ordersTable').DataTable({
                "ajax": {
                    "url": "order_datatable.php?action=fetch",
                    "type": "GET",
                    "dataSrc": function (json) {
                        console.log("Data received:", json); // Log response data for debugging
                        return json.data;
                    },
                    "error": function (xhr, status, error) {
                        console.error("Error: " + error);
                    }
                },
                "columns": [
                    { "data": "order_id" },
                    { "data": "user_firstname", "render": function(data, type, row) {
                        return row.user_firstname + ' ' + row.user_lastname;
                    }},
                    { "data": "total_amount" },
                    { "data": "order_status" },
                    { "data": "creation_date" },
                    { "data": "items", "render": function(data, type, row) {
                        return data.replace(/, /g, "<br>");
                    }},
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return `
                                <button class="btn btn-sm btn-danger" onclick="deleteOrder(${row.order_id})">Delete</button>
                            `;
                        }
                    }
                ]
            });
        });

        function deleteOrder(order_id) {
            if (confirm("Are you sure you want to delete this order?")) {
                $.ajax({
                    url: 'delete_order.php',
                    type: 'POST',
                    data: { order_id: order_id },
                    success: function(response) {
                        const res = JSON.parse(response);
                        if (res.success) {
                            alert(res.message);
                            $('#ordersTable').DataTable().ajax.reload();
                        } else {
                            alert(res.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error deleting order: " + error);
                    }
                });
            }
        }
    </script>
</body>
</html>
