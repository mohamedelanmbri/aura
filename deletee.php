<?php
if (isset($_GET['product_id'])) {
    $id = $_GET['product_id'];
    
    $cnx = mysqli_connect('localhost', 'root', '');
    mysqli_select_db($cnx, 'aura');
    
    // First, delete related images
    $deleteImagesQuery = "DELETE FROM product_img WHERE product_id = '$id'";
    if (!mysqli_query($cnx, $deleteImagesQuery)) {
        echo "Error deleting images: " . mysqli_error($cnx);
        exit();
    }

    // Now, delete the product
    $deleteProductQuery = "DELETE FROM products WHERE product_id = '$id'";
    $result = mysqli_query($cnx, $deleteProductQuery);
    
    if ($result) {
        header("Location: ../admin_profile.php");
        exit();
    } else {
        echo "Error deleting product: " . mysqli_error($cnx);
    }
    
    mysqli_close($cnx);
} else {
    header("Location: ../admin_profile.php?error");
    exit();
}
?>
