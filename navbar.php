<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check for logged-in status and user role
$isLoggedIn = isset($_SESSION['user']);
$userRole = isset($_SESSION['user']['role']) ? $_SESSION['user']['role'] : 'user';

function getProfileLink($role) {
    return $role === 'admin' ? 'admin_profile.php' : 'profile.php';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

    * {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    scroll-behavior: smooth;
    }

        /* Navbar Styling */
        .navbar {
            background-color: #f8f9fa; /* Light background */
        }



        .navbar-nav .nav-link.active, .navbar-nav .nav-link:hover {
            color: #555; /* Highlight color */
        }

        #logo1 {
            font-family: Arial, sans-serif;
            font-size: 24px;
        }
    </style>


<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="dashboard.php" id="logo1" style="color:#555;font-weight: 600;font-size: 1.7em;text-transform: uppercase;">Aura</a>
        <button class="navbar-toggler" type="button" id="navbarToggle" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="dashboard.php" style="font-size: 1.1em;font-weight: 500;padding-left: 30px;">Home</a>
                </li>
                <li class="nav-item">
                    <?php if ($isLoggedIn): ?>
                        <a class="nav-link"  style="font-size: 1.1em;font-weight: 500;padding-left: 30px;" href="<?php echo getProfileLink($userRole); ?>">Profile <i class="fa-regular fa-user"></i></a>
                    <?php else: ?>
                        <a class="nav-link"  style="font-size: 1.1em;font-weight: 500;padding-left: 30px;" href="login.php">Profile <i class="fa-regular fa-user"></i></a>
                    <?php endif; ?>
                </li>
                <li class="nav-item">
                    <a class="nav-link"  style="font-size: 1.1em;font-weight: 500;padding-left: 30px;" href="basket1.php">Basket <i class="fa-solid fa-bag-shopping"></i></a>
                </li>
            </ul>
        </div>
    </nav>

    <script>
        $(document).ready(function() {
            $('#navbarToggle').click(function() {
                $('#navbarSupportedContent').toggleClass('show');
            });
        });
    </script>
</body>
</html>
