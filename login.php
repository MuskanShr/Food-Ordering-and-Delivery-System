<?php
session_start();
require_once 'includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login    = trim($_POST['login']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$login, $login]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
       
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];


        if ($user['role'] === 'admin') {
            header('Location: admin/dashboard.php');
        } else {
            header('Location: index.php');
        }
        exit;

    } else {
        $error = "Invalid username/email or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login – FoodByte</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --orange: #F97316;
            --orange-dark: #EA6C0A;
            --gray: #6B7280;
            --border: #E5E7EB;
            --light-gray: #F3F4F6;
            --shadow-lg: 0 10px 40px rgba(0,0,0,0.18);
        }

        body { font-family: 'DM Sans', sans-serif; }

        .auth-page {
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 2rem;
            background-image: url('/foodbyte/uploads/loginbg.jpg');
            background-size: cover;
            background-position: center;
            position: relative;
        }
        .auth-page::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(2px);
        }
        .auth-box {
            position: relative; z-index: 1;
            background: white; border-radius: 20px;
            box-shadow: var(--shadow-lg); overflow: hidden;
            width: 100%; max-width: 420px;
        }
        .auth-header {
            background: linear-gradient(135deg, var(--orange), var(--orange-dark));
            color: white; padding: 2.5rem 2rem 2rem; text-align: center;
        }
        .auth-header .logo { font-size: 2rem; font-weight: 800; }
        .auth-header p { margin-top: 0.4rem; opacity: 0.85; font-size: 0.9rem; }

        .auth-body { padding: 2rem; }

        .auth-error {
            background: #FFEBEE; border: 1px solid #FFCDD2;
            border-radius: 9px; padding: 0.75rem 1rem;
            color: #C62828; font-size: 0.87rem; margin-bottom: 1rem;
        }

        .auth-field { margin-bottom: 1.2rem; }
        .auth-field label {
            display: block; font-size: 0.83rem; font-weight: 600;
            color: var(--gray); margin-bottom: 0.4rem;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .auth-field input {
            width: 100%; padding: 0.75rem 1rem;
            border: 1.5px solid var(--border); border-radius: 10px;
            font-family: 'DM Sans', sans-serif; font-size: 0.93rem;
            outline: none; transition: border 0.2s; background: #FAFAF9;
        }
        .auth-field input:focus { border-color: var(--orange); background: white; }

        .auth-submit {
            width: 100%; background: var(--orange); color: white;
            border: none; border-radius: 10px; padding: 0.85rem;
            font-size: 0.95rem; font-weight: 700; cursor: pointer;
            font-family: 'DM Sans', sans-serif; transition: all 0.2s; margin-top: 0.5rem;
        }
        .auth-submit:hover { background: var(--orange-dark); }

        .auth-footer {
            text-align: center; padding: 1rem 2rem 1.5rem;
            font-size: 0.87rem; color: var(--gray);
            border-top: 1px solid var(--light-gray);
        }
        .auth-footer a { color: var(--orange); text-decoration: none; font-weight: 600; }
        .auth-footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="auth-page">
    <div class="auth-box">

        <div class="auth-header">
            <div class="logo">🍴 FoodByte</div>
            <p>Welcome back! Sign in to continue.</p>
        </div>

        <div class="auth-body">

            <?php if ($error): ?>
                <div class="auth-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="auth-field">
                    <label>Username or Email</label>
                    <input type="text" name="login" placeholder="Enter your username or email" required>
                </div>
                <div class="auth-field">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>
                <div style="text-align:center;margin-top:0.8rem">
    <a href="forgot-password.php" 
       style="color:var(--orange);font-size:0.85rem;text-decoration:none;font-weight:600">
        Forgot Password?
    </a>
</div>
                <button type="submit" class="auth-submit">Login</button>
            </form>

        </div>

        <div class="auth-footer">
            Don't have an account? <a href="signup.php">Sign Up</a>
        </div>

    </div>
</div>

</body>
</html>