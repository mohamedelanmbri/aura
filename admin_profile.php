<?php include('session_check.php'); ?>
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
                <?php include("adminprofilesidebar.php"); ?>
            </div>
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div id="content-area">
                    <div id="dynamic-content">
                        <!-- Placeholder for dynamic content -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery-3.7.1.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="admin_profile.js"></script>
</body>
</html>
