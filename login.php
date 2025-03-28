<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Signup</title>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    
<?php
    session_start();

    // Generate CSRF token
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    $csrfToken = $_SESSION['csrf_token'];
    $loginError = '';
    $signupError = '';
    $signupSuccess = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $csrfToken) {
            die("Invalid CSRF token.");
        }

        try {
            $pdo = new PDO('mysql:host=localhost;dbname=aura', 'root', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            if (isset($_POST['login'])) {
                $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                $password = $_POST['password'];

                if (!empty($email) && !empty($password)) {
                    $stmt = $pdo->prepare('SELECT * FROM user WHERE user_email = ? AND user_pass = ?');
                    $stmt->execute([$email, $password]);
                    $user = $stmt->fetch();

                    if ($user) {
                        $_SESSION['user'] = $user;
                        $_SESSION['user_id'] = $user['user_id'];
                        header("Location: dashboard.php");
                        exit;
                    } else {
                        $loginError = "Login or password is incorrect";
                    }
                } else {
                    $loginError = "All fields are required";
                }
            }

            if (isset($_POST['signup'])) {
                $firstname = htmlspecialchars(trim($_POST['firstname']));
                $lastname = htmlspecialchars(trim($_POST['lastname']));
                $number = htmlspecialchars(trim($_POST['number']));
                $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                $password = $_POST['password'];
                $confirmpass = $_POST['confirmpass'];

                if (!empty($firstname) && !empty($lastname) && !empty($number) && !empty($email) && !empty($password) && !empty($confirmpass)) {
                    if ($password === $confirmpass) {
                        $stmt = $pdo->prepare('INSERT INTO user (user_firstname, user_lastname, user_phonenum, user_email, user_pass, role, creation_date) VALUES (?, ?, ?, ?, ?, ?, NOW())');
                        $stmt->execute([$firstname, $lastname, $number, $email, $password, 'user']);

                        $_SESSION['user'] = [
                            'user_firstname' => $firstname,
                            'user_lastname' => $lastname,
                            'user_email' => $email,
                        ];
                        $_SESSION['user_id'] = $pdo->lastInsertId();

                        $signupSuccess = "Sign up successful!";
                        header("Location: dashboard.php");
                        exit;
                    } else {
                        $signupError = "The password confirmation does not match";
                    }
                } else {
                    $signupError = "All fields are required";
                }
            }
        } catch (PDOException $e) {
            die("Database error: " . $e->getMessage());
        }
    }
?>
<div>

<div class="form-structor">
    
    <div class="signup">
        <h2 class="form-title" id="signup"><span>or</span>Sign up</h2>
        <h3 class="form-text" style="color:red;font-size:1em;text-align:center;"><?php echo htmlspecialchars($signupError); ?></h3>
        <?php if (!empty($signupSuccess)) : ?>
            <h3 class="form-text success-message" style="color:green;font-size:1em;text-align:center;"><?php echo htmlspecialchars($signupSuccess); ?></h3>
        <?php endif; ?>

        <form method="post" action="">
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            <div class="form-holder">
                <input type="text" class="input" placeholder="First Name" name="firstname">
                <input type="text" class="input" placeholder="Last Name" name="lastname">
                <input type="number" class="input" placeholder="Phone Number" name="number">
                <input type="email" class="input" placeholder="Email" name="email">
                <input type="password" class="input" placeholder="Password" name="password">
                <input type="password" class="input" placeholder="Confirm Password" name="confirmpass">
            </div>
            <button type="submit" class="submit-btn" name="signup">Sign up</button>
        </form>
    </div>
    <div class="login slide-up">
        <div class="center">
            <h2 class="form-title" id="login"><span>or</span>Log in</h2>
            <h3 class="form-text" style="color:red;font-size:1em;text-align:center;"><?php echo htmlspecialchars($loginError); ?></h3>
            <form method="post" action="">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                <div class="form-holder">
                    <input type="email" class="input" placeholder="Email" name="email">
                    <input type="password" class="input" placeholder="Password" name="password">
                </div>
                <button type="submit" class="submit-btn" name="login">Log in</button>
            </form>
        </div>
    </div>
</div>
</div>
<script>
        document.addEventListener('DOMContentLoaded', (event) => {
            const signupForm = document.querySelector('.signup form');
            const loginForm = document.querySelector('.login form');

            signupForm.addEventListener('submit', (e) => {
                const inputs = signupForm.querySelectorAll('.input');
                let valid = true;

                inputs.forEach(input => {
                    if (!input.value) {
                        input.classList.add('error');
                        input.placeholder = 'This field is required';
                        valid = false;
                    } else {
                        input.classList.remove('error');
                        input.placeholder = input.getAttribute('data-placeholder');
                    }
                });

                const password = signupForm.querySelector('input[name="password"]');
                const confirmPassword = signupForm.querySelector('input[name="confirmpass"]');
                if (password.value !== confirmPassword.value) {
                    confirmPassword.classList.add('error');
                    confirmPassword.value = '';
                    confirmPassword.placeholder = 'Passwords do not match';
                    valid = false;
                } else {
                    confirmPassword.classList.remove('error');
                    confirmPassword.placeholder = confirmPassword.getAttribute('data-placeholder');
                }

                if (!valid) {
                    e.preventDefault();
                }
            });

            // Reset error state on input focus
            const inputs = document.querySelectorAll('.input');
            inputs.forEach(input => {
                input.addEventListener('focus', () => {
                    input.classList.remove('error');
                    input.placeholder = input.getAttribute('data-placeholder');
                });
            });

            // Hide messages after 5 seconds
            setTimeout(() => {
                const messages = document.querySelectorAll('.message, .success-message');
                messages.forEach(message => {
                    message.style.display = 'none';
                });
            }, 5000);

            // Slide up/down functionality
            const loginBtn = document.getElementById('login');
            const signupBtn = document.getElementById('signup');

            loginBtn.addEventListener('click', (e) => {
                let parent = e.target.parentNode.parentNode;
                Array.from(e.target.parentNode.parentNode.classList).find((element) => {
                    if(element !== "slide-up") {
                        parent.classList.add('slide-up')
                    }else{
                        signupBtn.parentNode.classList.add('slide-up')
                        parent.classList.remove('slide-up')
                    }
                });
            });

            signupBtn.addEventListener('click', (e) => {
                let parent = e.target.parentNode;
                Array.from(e.target.parentNode.classList).find((element) => {
                    if(element !== "slide-up") {
                        parent.classList.add('slide-up')
                    }else{
                        loginBtn.parentNode.parentNode.classList.add('slide-up')
                        parent.classList.remove('slide-up')
                    }
                });
            });
        });
    </script>
</body>
</html>
