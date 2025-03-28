<?php
session_start();
include('db.php');

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Fetch product information
    $product_sql = "SELECT * FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($product_sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product_result = $stmt->get_result();
    $product = $product_result->fetch_assoc();

    // Fetch product images
    $images_sql = "SELECT img_url FROM product_img WHERE product_id = ?";
    $img_stmt = $conn->prepare($images_sql);
    $img_stmt->bind_param("i", $product_id);
    $img_stmt->execute();
    $images_result = $img_stmt->get_result();

    // Fetch available colors
    $colors_sql = "SELECT color FROM products WHERE product_id = ?";
    $color_stmt = $conn->prepare($colors_sql);
    $color_stmt->bind_param("i", $product_id);
    $color_stmt->execute();
    $colors_result = $color_stmt->get_result();
    $available_colors = $colors_result->fetch_assoc()['color'];
    $available_colors = explode(',', $available_colors); // Assuming colors are stored as comma-separated values
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['product_name']) ?> - Item Page</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="item.css">
</head>
<body>
    <?php include("navbar.php"); ?>

    <main class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner">
                        <?php
                        $active = true;
                        while ($img = $images_result->fetch_assoc()) {
                            $img_url = 'admin/' . $img['img_url'];
                        ?>
                        <div class="carousel-item <?= $active ? 'active' : '' ?>">
                            <img src="<?= $img_url ?>" class="d-block w-100" alt="Product Image">
                        </div>
                        <?php
                            $active = false;
                        }
                        ?>
                    </div>
                    <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="item-details">
                    <h1 class="item-title"><?= htmlspecialchars($product['product_name']) ?></h1>
                    <p class="item-price"><?= htmlspecialchars($product['product_price']) ?> MAD</p>
                    <p class="item-description"><?= htmlspecialchars($product['product_description']) ?></p>

                    <form method="POST" action="add_to_basket.php">
                        <div class="item-colors form-group">
                            <label for="color-select">Color:</label>
                            <select id="color-select" class="form-control" name="color">
                                <?php foreach ($available_colors as $color): ?>
                                    <option value="<?= htmlspecialchars($color) ?>"><?= htmlspecialchars($color) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="item-sizes form-group">
                            <label for="size-select">Sizes:</label>
                            <select id="size-select" class="form-control" name="size">
                                <option value="XS">XS</option>
                                <option value="S">S</option>
                                <option value="M">M</option>
                                <option value="L">L</option>
                                <option value="XL">XL</option>
                                <option value="XXL">XXL</option>
                            </select>
                        </div>

                        <input type="hidden" name="product_id" value="<?= $product_id ?>">
                        <button type="submit" class="btn btn-success btn-block">Add to Basket <i class="fa-solid fa-bag-shopping"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>

    <?php include("aboutsection.php"); ?>
</body>
</html>
