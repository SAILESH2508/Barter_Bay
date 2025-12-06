<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<style>
/* NAVBAR */
.navbar {
    display:flex;justify-content:space-between;align-items:center;
    background:linear-gradient(to right, red, blue);
    padding:10px 20px;
    position: sticky; top:0; z-index:9999;
    box-shadow:0 2px 6px rgba(0,0,0,0.15);
}
.logo-link { display:flex;align-items:center;text-decoration:none;gap:15px; }
.logo h1 { color:white;margin:0;font-size:24px;font-weight:bold; }
.seal-img { width:60px; height:60px; object-fit:contain; }

/* NAV LINKS */
.nav-links { list-style:none;display:flex;gap:10px;margin:0;padding:0; }
.nav-links li { position:relative; }
.nav-links a {
    color:white;text-decoration:none;font-weight:bold;padding:10px;
    border-radius:6px; transition:.15s ease;
}
.nav-links a:hover, .nav-links a.active {
    transform:scale(1.05);
    background:rgba(255,255,255,0.15);
}

/* DROPDOWNS */
.dropdown-content {
    position:absolute;
    display:none;
    background:purple;
    padding:6px 0;
    min-width:160px;
    border-radius:5px;
    top:100%;
    z-index:9999;
}
.dropdown-content a {
    padding:10px 12px; display:block;
    white-space:nowrap; color:white; text-decoration:none;
}
.dropdown-content a:hover { background:white; color:black; }

.dropdown:hover .dropdown-content,
.dropdown:focus-within .dropdown-content {
    display:block;
}

/* HAMBURGER */
.hamburger {
    display:none; font-size:28px; color:white;
    background:none; border:none; cursor:pointer;
}

/* RESPONSIVE */
@media(max-width:768px){
    .hamburger { display:block; }
    .nav-links {
        display:none; flex-direction:column;
        background:rgba(0,0,0,0.95); width:100%; left:0; top:100%;
        position:absolute; padding:12px 0; border-top:2px solid rgba(255,255,255,0.2);
    }
    .nav-links.show { display:flex; }
    .nav-links a { color:white; }
    .dropdown-content {
        position:relative;
        background:rgba(255,255,255,0.1);
        margin-left:20px;
    }
}
</style>

<!-- ========== NAVBAR ========== -->
<nav class="navbar">
    <div class="logo">
        <a href="index.php" class="logo-link">
            <img src="images/seal.png?v=1" class="seal-img" alt="Barter Bay logo">
            <h1>Barter Bay</h1>
        </a>
    </div>

    <button class="hamburger" id="hambtn" aria-expanded="false">☰</button>

    <ul class="nav-links" id="nav-links">
        <?php if (isset($_SESSION['customer'])): ?>

            <li><a href="dashboard.php" class="<?= $currentPage=='dashboard.php'?'active':'' ?>">Dashboard</a></li>
            <li><a href="products.php" class="<?= $currentPage=='products.php'?'active':'' ?>">Products</a></li>
            <li><a href="trade.php" class="<?= $currentPage=='trade.php'?'active':'' ?>">Trade</a></li>
            <li><a href="contact.php" class="<?= $currentPage=='contact.php'?'active':'' ?>">Contact</a></li>
            <li><a href="cart.php" class="<?= $currentPage=='cart.php'?'active':'' ?>">Cart</a></li>

            <li class="dropdown">
                <a href="#" class="dropbtn" role="button" aria-haspopup="true">Account ▼</a>
                <div class="dropdown-content" role="menu">
                    <a href="profile.php" role="menuitem">Profile</a>
                    <a href="my_purchases.php" role="menuitem">My Purchases</a>
                    <a href="my_trades.php" role="menuitem">My Trades</a>
                    <a href="logout.php" role="menuitem">Logout</a>
                </div>
            </li>

            <li class="dropdown">
                <a href="#" class="dropbtn" role="button" aria-haspopup="true">More ▼</a>
                <div class="dropdown-content" role="menu">
                    <a href="rate_product.php" role="menuitem">Rate Products</a>
                    <a href="faq.php" role="menuitem">FAQ</a>
                </div>
            </li>

        <?php elseif (isset($_SESSION['admin'])): ?>

            <li><a href="admin_dashboard.php" class="<?= $currentPage=='admin_dashboard.php'?'active':'' ?>">Dashboard</a></li>

            <!-- ✔️ ADDED PRODUCTS PAGE FOR ADMIN -->
            <li><a href="products.php" class="<?= $currentPage=='products.php'?'active':'' ?>">Products</a></li>

            <li class="dropdown">
                <a href="#" class="dropbtn" role="button" aria-haspopup="true">Manage ▼</a>
                <div class="dropdown-content" role="menu">
                    <a href="manage_users.php" role="menuitem">Users</a>
                    <a href="view_products.php" role="menuitem">Products</a>
                    <a href="view_trades.php" role="menuitem">Trades</a>
                </div>
            </li>

            <li class="dropdown">
                <a href="#" class="dropbtn" role="button" aria-haspopup="true">Account ▼</a>
                <div class="dropdown-content" role="menu">
                    <a href="logout.php" role="menuitem">Logout</a>
                </div>
            </li>

        <?php else: ?>

            <li><a href="index.php" class="<?= $currentPage=='index.php'?'active':'' ?>">Login</a></li>
            <li><a href="signup.php" class="<?= $currentPage=='signup.php'?'active':'' ?>">Sign Up</a></li>

            <!-- ✔️ ADDED PRODUCTS PAGE FOR GUEST -->
            <li><a href="products.php" class="<?= $currentPage=='products.php'?'active':'' ?>">Products</a></li>

            <li><a href="contact.php" class="<?= $currentPage=='contact.php'?'active':'' ?>">Contact</a></li>
            <li><a href="faq.php" class="<?= $currentPage=='faq.php'?'active':'' ?>">FAQ</a></li>

        <?php endif; ?>
    </ul>
</nav>

<!-- ========== JS ========== -->
<script>
(function(){
    'use strict';
    
    const ham = document.getElementById('hambtn');
    const nav = document.getElementById('nav-links');

    // Hamburger toggle
    if (ham && nav) {
        ham.addEventListener('click', function() {
            nav.classList.toggle('show');
            const isExpanded = nav.classList.contains('show');
            ham.setAttribute('aria-expanded', isExpanded);
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!ham.contains(event.target) && !nav.contains(event.target)) {
                nav.classList.remove('show');
                ham.setAttribute('aria-expanded', 'false');
            }
        });

        // Close menu on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && nav.classList.contains('show')) {
                nav.classList.remove('show');
                ham.setAttribute('aria-expanded', 'false');
                ham.focus();
            }
        });
    }

    // Dropdown keyboard navigation
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(function(dropdown) {
        const trigger = dropdown.querySelector('.dropbtn');
        const menu = dropdown.querySelector('.dropdown-content');
        
        if (trigger && menu) {
            trigger.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
                }
            });
        }
    });
})();
</script>
