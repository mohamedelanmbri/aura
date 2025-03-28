<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
session_start();
include('db.php');

// Pagination settings
$productsPerPage = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $productsPerPage;

// Handle search input
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchPattern = "%$searchQuery%";

// Fetch products
if ($searchQuery) {
    $stmt = $conn->prepare("SELECT product_id, product_name, product_price, main_img_url FROM products WHERE product_name LIKE ? LIMIT ? OFFSET ?");
    $stmt->bind_param("sii", $searchPattern, $productsPerPage, $offset);
} else {
    $stmt = $conn->prepare("SELECT product_id, product_name, product_price, main_img_url FROM products LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $productsPerPage, $offset);
}
$stmt->execute();
$result = $stmt->get_result();

// Count total products for pagination
if ($searchQuery) {
    $countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM products WHERE product_name LIKE ?");
    $countStmt->bind_param("s", $searchPattern);
} else {
    $countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM products");
}
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalProducts = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalProducts / $productsPerPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<style>
    #main {
    width: 100%;
    min-height: 100vh;
    display: flex; /* Use flexbox for alignment */
    flex-direction: column;
    justify-content: center; /* Center vertically */
    align-items: center; /* Center horizontally */
    background: url(2\ home\ page.jpg) no-repeat;
    background-size: cover;
    background-position: center;
}

#main h2 {
    color: rgb(250, 251, 251);
    font-size: 2em; /* Adjusted font size */
    font-weight: 500;
    text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7); /* Optional: Add text shadow for readability */
}

.socialicons a i {
    color: white; /* Icon color */
    font-size: 1.5em; /* Adjust icon size */
    margin: 0 10px; /* Add spacing between icons */
}

</style>

<?php include("navbar.php"); ?>

<section id="main" class="main d-flex flex-column align-items-center text-center">
    <h2 class="mb-4">Find your favorite clothing design among our wide collection. Click to get started and find the one that speaks to you.</h2>
    <div class="socialicons mb-4">
        <a target="_blank" href="https://www.facebook.com/profile.php?id=61551918190969&mibextid=LQQJ4d" class="me-3">
            <i class="fa-brands fa-facebook"></i>
        </a>
        <a target="_blank" href="https://www.instagram.com/au._.ra01?igsh=bmExNTM2cTBwaDdj" class="me-3">
            <i class="fa-brands fa-instagram"></i>
        </a>
        <a target="_blank" href="#">
            <i class="fa-brands fa-tiktok"></i>
        </a>
    </div>
</section>


    <!-- Search Bar -->
    <div class="container my-4">
        <form method="GET" action="dashboard.php" class="d-flex justify-content-center">
            <input type="text" name="search" class="form-control w-50 me-2" placeholder="Search..." value="<?= htmlspecialchars($searchQuery) ?>">
            <button type="submit" class="btn btn-outline-success"><i class="fa fa-search"></i></button>
        </form>
    </div>

    <!-- Product Cards -->
    <div class="container py-5">
        <div class="row">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <a style="text-decoration: none;" href="item.php?product_id=<?= htmlspecialchars($row['product_id']) ?>" class="card-content">
                            <img src="admin/<?= htmlspecialchars($row['main_img_url'] ?? 'placeholder.jpg') ?>" class="card-img-top" alt="Product Image">
                            <div style="color: black" class="card-header"><?= htmlspecialchars($row['product_name']) ?></div>
                            <div class="list-group list-group-flush">
                                <li style="color: #555;" class="list-group-item"><?= htmlspecialchars($row['product_price']) ?> MAD</li>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            <nav>
                <ul class="pagination">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link text-success" href="?page=<?= $page - 1 ?>&search=<?= urlencode($searchQuery) ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                            <a class="page-link <?= $page == $i ? '' : 'text-success' ?>" href="?page=<?= $i ?>&search=<?= urlencode($searchQuery) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link text-success" href="?page=<?= $page + 1 ?>&search=<?= urlencode($searchQuery) ?>">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <?php include("aboutsection.php"); ?>
</body>
</html>
