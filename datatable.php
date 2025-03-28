<?php include('session_check1.php'); ?>

<?php
$cnx = mysqli_connect('localhost', 'root', '');
mysqli_select_db($cnx, 'aura');
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product</title>
    <link rel="stylesheet" href="product.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script>
        $(document).ready(function () {
            // Destroy existing DataTable if it exists
            if ($.fn.DataTable.isDataTable('#myTable')) {
                $('#myTable').DataTable().destroy();
            }

            // Reinitialize DataTable
            $('#myTable').DataTable({
                "paging": true,      // Enables pagination
                "searching": true,   // Adds search box
                "info": true,        // Shows info about entries
                "lengthChange": true, // Allows changing the number of entries per page
                "pageLength": 5,      // Sets default entries per page
                "language": {         // Customize DataTable text
                    "lengthMenu": "Show _MENU_ entries per page",
                    "search": "Search:",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    }
                }
            });
        });
    </script>
</head>
<body>
    <style>
        body {
            background-color: #ffffff;
            color: #000;
        }
        h1, h5 {
            color: red;
        }
        .btn-outline-dark {
            color: #000;
            border-color: #000;
        }
        .btn-outline-dark:hover {
            background-color: #000;
            color: #fff;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: black;
            background-color: #e9ecef;
            border: 1px solid #e9ecef;
            padding: 5px 10px;
            margin: 0 2px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background-color: #adb5bd;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            .table-responsive {
                margin-bottom: 20px;
            }
        }
    </style>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1>Product List</h1>
            </div>
        </div>

        <div class="table-responsive">
            <table id="myTable" class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Product Name</th>
                        <th scope="col">Category ID</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Price</th>
                        <th scope="col">Colors</th>
                        <th scope="col">Description</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $req = "SELECT * FROM products";
                    $res = mysqli_query($cnx, $req);
                    while ($data = mysqli_fetch_array($res)) {
                    ?>
                    <tr>
                        <td><?php echo $data['product_id']; ?></td>
                        <td><?php echo $data['product_name']; ?></td>
                        <td><?php echo $data['category_id']; ?></td>
                        <td><?php echo $data['product_quantity']; ?></td>
                        <td><?php echo $data['product_price']; ?></td>
                        <td><?php echo $data['color']; ?></td>
                        <td><?php echo $data['product_description']; ?></td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="admin/update.php?id=<?php echo $data['product_id']; ?>" class="btn btn-outline-dark"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
                                <a onclick="return confirm('Do you want to delete?')" href="admin/deletee.php?product_id=<?php echo $data['product_id']; ?>" class="btn btn-outline-danger"><i class="fa-solid fa-trash"></i> Delete</a>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
