<?php include('session_check1.php'); ?>
<div class="d-flex flex-column flex-shrink-0 p-3 bg-light" style="width: 250px;">
    <ul class="nav nav-pills flex-column mb-auto">
        <li>
            <a href="#" class="nav-link" id="products-tab" onclick="loadContent('admin/datatable.php')">My Products</a>
        </li>
        <li>
            <a href="admin/add_product.php" class="nav-link">Add New Product</a>
        </li>
        <li>
            <a href="admin/order_datatable.php" class="nav-link">orders</a>
        </li>
        <li>
            <a href="signout.php" class="nav-link">Sign Out</a>
        </li>
    </ul>
</div>
