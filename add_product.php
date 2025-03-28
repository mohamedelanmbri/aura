<?php include('session_check1.php'); ?>
<?php
// Database connection
$host = 'localhost';
$dbname = 'aura';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $_POST['product_name'];
    $category_id = $_POST['category_id'];
    $product_quantity = $_POST['product_quantity'];
    $product_price = $_POST['product_price'];
    $product_description = $_POST['product_description'];
    $colors = implode(",", $_POST['colors']);  // Convert selected colors to a comma-separated string

    try {
        // Insert product information
        $stmt = $pdo->prepare("INSERT INTO products (product_name, product_description, product_price, product_quantity, category_id, color, creation_date) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$product_name, $product_description, $product_price, $product_quantity, $category_id, $colors]);
        
        // Get the product ID of the newly created product
        $product_id = $pdo->lastInsertId();
        
        // Directory for images
        $targetDir = 'imgs/';
        $firstImage = true;

        // Loop through each uploaded file
        foreach ($_FILES['img']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['img']['error'][$key] == 0) {
                $imageName = basename($_FILES['img']['name'][$key]);
                $targetFilePath = $targetDir . $imageName;

                // Move the file to the target directory
                if (move_uploaded_file($tmp_name, $targetFilePath)) {
                    // Insert image path into product_img table
                    $stmt = $pdo->prepare("INSERT INTO product_img (product_id, img_url) VALUES (?, ?)");
                    $stmt->execute([$product_id, $targetFilePath]);

                    // Save the first image as the main image
                    if ($firstImage) {
                        $stmt = $pdo->prepare("UPDATE products SET main_img_url = ? WHERE product_id = ?");
                        $stmt->execute([$targetFilePath, $product_id]);
                        $firstImage = false;
                    }
                }
            }
        }

        echo "<div class='alert alert-success text-center'>Product successfully added!</div>";
        
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger text-center'>Error: " . $e->getMessage() . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Add New Product</h2>
        <form action="add_product.php" method="post" enctype="multipart/form-data" class="bg-light p-4 rounded shadow">
            <div class="mb-3">
                <label for="product_name" class="form-label">Product Name</label>
                <input type="text" name="product_name" id="product_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Category ID</label>
                <input type="number" name="category_id" id="category_id" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="product_quantity" class="form-label">Quantity</label>
                <input type="number" name="product_quantity" id="product_quantity" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="product_price" class="form-label">Price (MAD)</label>
                <input type="text" name="product_price" id="product_price" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="product_description" class="form-label">Description</label>
                <textarea name="product_description" id="product_description" class="form-control" rows="3" required></textarea>
            </div>
            
            <!-- Color selection using checkboxes -->
            <div class="mb-3">
                <label class="form-label">Available Colors</label><br>
                <?php
                // Define available colors (could be replaced with a database call)
                $colors = ['Red', 'Blue', 'dark-Green', 'Black', 'White', 'Yellow', 'Purple','light-blue'];
                
                foreach ($colors as $color) {
                    echo "
                    <div class='form-check form-check-inline'>
                        <input class='form-check-input' type='checkbox' name='colors[]' value='$color' id='color_$color'>
                        <label class='form-check-label' for='color_$color'>$color</label>
                    </div>";
                }
                ?>
            </div>

            <div class="mb-3">
                <label for="img" class="form-label">Upload Images</label>
                <input type="file" name="img[]" id="img" class="form-control" multiple>
                <small class="text-muted">You can upload multiple images</small>
            </div>
            
            <button type="submit" class="btn btn-primary w-100">Add Product</button><br><br>
            <a href="../admin_profile.php" class="btn btn-primary w-100">Back to Profile</a>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
