<?php // Header - global navigation and styles
require_once __DIR__ . '/auth.php';
$cartCount = getCartCount();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodByte <?php echo isset($pageTitle) ? '- '.$pageTitle : ''; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --cream: #FAF7F2;
            --warm-white: #FFFEF9;
            --orange: #E8521A;
            --orange-dark: #C94210;
            --orange-light: #FF7A47;
            --charcoal: #1A1A1A;
            --gray: #6B6B6B;
            --light-gray: #E8E4DF;
            --border: #DDD8D0;
            --shadow: 0 4px 24px rgba(26,26,26,0.08);
            --shadow-lg: 0 12px 48px rgba(26,26,26,0.14);
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'DM Sans',sans-serif; background:var(--cream); color:var(--charcoal); }

        nav {
            background:var(--warm-white);
            border-bottom:1px solid var(--border);
            position:sticky; top:0; z-index:100;
            padding:0 2rem;
            display:flex; align-items:center; justify-content:space-between;
            height:80px;
            box-shadow:0 2px 12px rgba(0,0,0,0.06);
        }
        .nav-brand {
            font-family:'Playfair Display',serif;
            font-size:2rem; 
            font-weight:800;
            color:var(--orange);
            text-decoration:none;
            letter-spacing:-0.5px;
        }
        .nav-links {
            display:flex; align-items:center; gap:0.25rem;
            list-style:none;
        }
        .nav-links a {
            text-decoration:none;
            color:var(--charcoal);
            font-size:1rem; 
            font-weight:500;
            padding:0.45rem 0.9rem;
            border-radius:8px;
            transition:all 0.2s;
        }
        .nav-links a:hover, .nav-links a.active {
            background:var(--cream);
            color:var(--orange);
        }
        .nav-right { display:flex; align-items:center; gap:0.5rem; }
        .btn-nav {
            display:inline-flex; align-items:center; gap:0.4rem;
            padding:0.4rem 1rem;
            border-radius:8px;
            font-size:0.88rem; font-weight:600;
            text-decoration:none;
            transition:all 0.2s;
            cursor:pointer; border:none;
        }
        .btn-nav-outline {
            border:1.5px solid var(--border);
            background:transparent;
            color:var(--charcoal);
        }
        .btn-nav-outline:hover { border-color:var(--orange); color:var(--orange); }
        .btn-nav-primary { background:var(--orange); color:#fff; }
        .btn-nav-primary:hover { background:var(--orange-dark); }
        .cart-badge {
            background:var(--orange);
            color:#fff;
            font-size:0.7rem; font-weight:700;
            padding:2px 6px;
            border-radius:20px;
            min-width:20px; text-align:center;
        }
        .user-info {
            font-size:0.85rem;
            color:var(--gray);
            padding:0.3rem 0.8rem;
            background:var(--cream);
            border-radius:8px;
            border:1px solid var(--border);
            text-decoration:none;
            display:inline-flex;
            align-items:center;
            gap:0.35rem;
            transition:all 0.2s;
        }
        .user-info:hover { border-color:var(--orange); color:var(--orange); }

        .flash {
            padding:0.9rem 1.5rem;
            border-radius:10px;
            margin:1rem 2rem;
            font-size:0.9rem; font-weight:500;
            display:flex; align-items:center; gap:0.5rem;
        }
        .flash-success { background:#E8F5E9; color:#2E7D32; border:1px solid #A5D6A7; }
        .flash-error   { background:#FFEBEE; color:#C62828; border:1px solid #FFCDD2; }

        .btn {
            display:inline-flex; align-items:center; gap:0.4rem;
            padding:0.6rem 1.4rem;
            border-radius:10px;
            font-family:'DM Sans',sans-serif;
            font-size:0.9rem; font-weight:600;
            cursor:pointer; border:none;
            transition:all 0.2s;
            text-decoration:none;
        }
        .btn-primary { background:var(--orange); color:#fff; }
        .btn-primary:hover { background:var(--orange-dark); transform:translateY(-1px); box-shadow:0 4px 12px rgba(232,82,26,0.3); }
        .btn-outline { background:transparent; border:1.5px solid var(--border); color:var(--charcoal); }
        .btn-outline:hover { border-color:var(--orange); color:var(--orange); }
        .btn-danger { background:#E53935; color:#fff; }
        .btn-danger:hover { background:#C62828; }
        .btn-sm { padding:0.35rem 0.8rem; font-size:0.8rem; border-radius:7px; }

        footer {
            background:var(--charcoal);
            color:rgba(255,255,255,0.5);
            text-align:center;
            padding:1.2rem;
            font-size:0.82rem;
            margin-top:4rem;
        }
    </style>
</head>
<body>
<nav>
   <a href="/foodbyte/index.php" class="nav-brand">FoodByte</a>
    <ul class="nav-links">
        <li><a href="/foodbyte/index.php" class="<?= $currentPage==='index'?'active':'' ?>">Home</a></li>
        <li><a href="/foodbyte/menu.php" class="<?= $currentPage==='menu'?'active':'' ?>">Menu</a></li>
        <li><a href="/foodbyte/aboutus.php" class="<?= $currentPage==='about'?'active':'' ?>">About Us</a></li>
        <li><a href="/foodbyte/search.php" class="<?= $currentPage==='search'?'active':'' ?>">Search</a></li>
    </ul>
    <div class="nav-right">
        <?php if(isLoggedIn()): ?>
            <a href="/foodbyte/profile.php" class="user-info">
                👤 <?= htmlspecialchars($_SESSION['username']) ?>
            </a>
            <a href="/foodbyte/cart.php" class="btn-nav btn-nav-outline">
                🛒 Cart <?php if($cartCount > 0): ?><span class="cart-badge"><?= $cartCount ?></span><?php endif; ?>
            </a>
        <?php else: ?>
            <a href="/foodbyte/cart.php" class="btn-nav btn-nav-outline">
                🛒 Cart <?php if($cartCount > 0): ?><span class="cart-badge"><?= $cartCount ?></span><?php endif; ?>
            </a>
            <a href="/foodbyte/login.php" class="btn-nav btn-nav-primary">Log In / Register</a>
        <?php endif; ?>
    </div>
</nav>
<?php if(isset($_SESSION['flash'])): ?>
    <div class="flash flash-<?= $_SESSION['flash']['type'] ?>">
        <?= $_SESSION['flash']['msg'] ?>
    </div>
<?php unset($_SESSION['flash']); endif; ?>