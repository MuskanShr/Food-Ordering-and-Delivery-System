
<!DOCTYPE html>
<html>
<head>
    <title>Sign Up – FoodByte</title>
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

        .auth-errors {
            background: #FFEBEE; border: 1px solid #FFCDD2;
            border-radius: 9px; padding: 0.75rem 1rem;
            color: #C62828; font-size: 0.87rem; margin-bottom: 1rem;
        }
        .auth-errors ul { margin-left: 1.2rem; }

        .auth-success {
            background: #E8F5E9; border: 1px solid #C8E6C9;
            border-radius: 9px; padding: 0.75rem 1rem;
            color: #2E7D32; font-size: 0.87rem; margin-bottom: 1rem;
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
            <p>Create your account today</p>
        </div>

        <div class="auth-body">

            <?php if (!empty($errors)): ?>
                <div class="auth-errors">
                    <ul>
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['registered'])): ?>
                <div class="auth-success">Account created! You can now log in.</div>
            <?php endif; ?>

            <form method="POST">
                <div class="auth-field">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Choose a username"
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                </div>
                <div class="auth-field">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="Enter your email"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>
                <div class="auth-field">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Min. 6 characters" required>
                </div>
                <div class="auth-field">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm" placeholder="Re-enter your password" required>
                </div>
                <button type="submit" class="auth-submit">Sign Up →</button>
            </form>

        </div>

        <div class="auth-footer">
            Already have an account? <a href="login.php">Login</a>
        </div>

    </div>
</div>

</body>
</html>