<?php include('session_check1.php'); ?>
<html lang="en">
<head>
    <link rel="stylesheet" href="create.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>UPDATE PRODUCT</title>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1 class="mb-4">UPDATE PRODUCT</h1>
                <?php
                if (isset($_GET['id'])) {
                    $id = $_GET['id'];
                    $cnx = mysqli_connect('localhost', 'root', '', 'aura');

                    if (!$cnx) {
                        die("Connection failed: " . mysqli_connect_error());
                    }

                    if (isset($_POST['update'])) {
                        $product_name = mysqli_real_escape_string($cnx, $_POST['product_name']);
                        $category_id = mysqli_real_escape_string($cnx, $_POST['category_id']);
                        $quantity = mysqli_real_escape_string($cnx, $_POST['quantity']);
                        $price = mysqli_real_escape_string($cnx, $_POST['price']);
                        $product_description = mysqli_real_escape_string($cnx, $_POST['product_description']);
                        $selected_colors = isset($_POST['colors']) ? implode(',', $_POST['colors']) : '';

                        $img_name = $_FILES['img']['name'];
                        $img_location = $_FILES['img']['tmp_name'];
                        $img_up = "imgs/" . $img_name;

                        if (!empty($img_name)) {
                            move_uploaded_file($img_location, $img_up);
                            $query = "UPDATE products SET product_name='$product_name', category_id='$category_id', product_quantity='$quantity', product_price='$price', product_description='$product_description', main_img_url='$img_up', color='$selected_colors' WHERE product_id='$id'";
                        } else {
                            $query = "UPDATE products SET product_name='$product_name', category_id='$category_id', product_quantity='$quantity', product_price='$price', product_description='$product_description', color='$selected_colors' WHERE product_id='$id'";
                        }

                        $result = mysqli_query($cnx, $query);

                        if ($result) {
                            echo "<div class='alert alert-success' role='alert'>Product updated successfully</div>";
                        } else {
                            echo "Error updating record: " . mysqli_error($cnx);
                        }
                    }

                    $query = "SELECT * FROM products WHERE product_id = $id";
                    $result = mysqli_query($cnx, $query);

                    if ($result && mysqli_num_rows($result) > 0) {
                        $row = mysqli_fetch_assoc($result);
                        $current_colors = explode(',', $row['color']);
                        ?>
                        <form method="post" action="update.php?id=<?php echo $id; ?>" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="img" class="form-label">UPLOAD MAIN IMAGE</label>
                                <?php if (!empty($row['main_img_url'])): ?>
                                    <p>Current Main Image:</p>
                                    <img src="<?php echo $row['main_img_url']; ?>" class="img-fluid mb-2" alt="Current Image">
                                <?php endif; ?>
                                <input type="file" id="img" class="form-control" name="img">
                            </div>
                            <div class="mb-3">
                                <label for="product_name" class="form-label">Product Name:</label>
                                <input type="text" id="product_name" name="product_name" class="form-control" value="<?php echo $row['product_name']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category ID:</label>
                                <input type="number" id="category_id" name="category_id" class="form-control" value="<?php echo $row['category_id']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity:</label>
                                <input type="number" id="quantity" name="quantity" class="form-control" value="<?php echo $row['product_quantity']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Price (MAD):</label>
                                <input type="number" id="price" name="price" class="form-control" value="<?php echo $row['product_price']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="product_description" class="form-label">Description:</label>
                                <textarea name="product_description" id="product_description" class="form-control" rows="7" required><?php echo htmlspecialchars($row['product_description']); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="colors" class="form-label">Select Colors:</label>
                                <?php
                                $colors = ['Red', 'Blue', 'dark-Green', 'Yellow', 'Black', 'White','light-blue'];
                                foreach ($colors as $color) {
                                    $checked = in_array($color, $current_colors) ? 'checked' : '';
                                    echo "<div class='form-check'>
                                            <input class='form-check-input' type='checkbox' name='colors[]' value='$color' id='color_$color' $checked>
                                            <label class='form-check-label' for='color_$color'>$color</label>
                                          </div>";
                                }
                                ?>
                            </div>
                            <button type="submit" name="update" class="btn btn-danger w-100">UPDATE</button><br><br>
                            <a href="../admin_profile.php" class="btn btn-primary w-100">Back to Profile</a>
                        </form>
                        <?php
                    } else {
                        echo "Record not found";
                    }

                    mysqli_close($cnx);
                } else {
                    echo "ID not provided";
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>
