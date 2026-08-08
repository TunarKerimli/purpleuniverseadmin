<?php

session_start();

if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    header("Location: index.php");
    exit;
}

require_once "config/database.php";

$totalProducts = (int)$db->query("
    SELECT COUNT(*) FROM products
")->fetchColumn();

$inStockProducts = (int)$db->query("
    SELECT COUNT(*)
    FROM products
    WHERE in_stock = 1
")->fetchColumn();

$outOfStockProducts = (int)$db->query("
    SELECT COUNT(*)
    FROM products
    WHERE in_stock = 0
")->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Purple Universe — Dashboard</title>

    <link rel="stylesheet" href="css/admin.css">

</head>

<body>

<div class="admin-layout">

    <!-- SIDEBAR -->

    <aside class="sidebar">

        <div class="sidebar-logo">
            PURPLE<span>UNIVERSE</span>
        </div>

        <div class="sidebar-label">
            ADMIN PANEL
        </div>

        <nav>

            <a href="#" class="active">
                <span>⌂</span>
                Dashboard
            </a>

           <a href="products.php">
    <span>◈</span>
    Products
</a>

            <a href="#">
                <span>▣</span>
                Orders
            </a>

            <a href="#">
                <span>♙</span>
                Users
            </a>

            <a href="#">
                <span>◴</span>
                Analytics
            </a>

        </nav>

        <div class="sidebar-bottom">

            <a href="#">
                ⚙ Settings
            </a>

            <a href="index.php">
                ↪ Logout
            </a>

        </div>

    </aside>


    <!-- MAIN CONTENT -->

    <main class="main-content">

        <header class="topbar">

            <div>
                <p class="page-label">OVERVIEW</p>
                <h1>Dashboard</h1>
            </div>

            <div class="admin-profile">

                <div class="avatar">
                    A
                </div>

                <div>
                    <strong>Admin</strong>
                    <small>Administrator</small>
                </div>

            </div>

        </header>


        <!-- WELCOME -->

        <section class="welcome">

            <div>

                <p class="welcome-small">
                    PURPLE UNIVERSE
                </p>

                <h2>Good afternoon, Admin 👋</h2>

                <p>
                    Here's what's happening with your store today.
                </p>

            </div>

        </section>


        <!-- STATISTICS -->

        <section class="stats-grid">

            <div class="stat-card">

                <div class="stat-top">
                    <span>USERS</span>
                    <span class="stat-icon">♙</span>
                </div>

                <h3>1,542</h3>

                <p class="positive">
                    ↑ 18.4% <span>vs last month</span>
                </p>

            </div>


            <div class="stat-card">

                <div class="stat-top">
                    <span>ORDERS</span>
                    <span class="stat-icon">▣</span>
                </div>

                <h3>284</h3>

                <p class="positive">
                    ↑ 12.2% <span>vs last month</span>
                </p>

            </div>


            <div class="stat-card">

                <div class="stat-top">
                    <span>PRODUCTS</span>
                    <span class="stat-icon">◈</span>
                </div>

<h3><?= $totalProducts ?></h3>

                <p class="positive">
                    ↑ 5 new <span>this month</span>
                </p>

            </div>


            <div class="stat-card">

                <div class="stat-top">
                    <span>REVENUE</span>
                    <span class="stat-icon">$</span>
                </div>

                <h3>$8,420</h3>

                <p class="positive">
                    ↑ 24.8% <span>vs last month</span>
                </p>

            </div>

        </section>


        <!-- LOWER SECTION -->

        <section class="dashboard-grid">

            <div class="panel">

                <div class="panel-header">

                    <div>
                        <p class="panel-label">STATISTICS</p>
                        <h3>User Growth</h3>
                    </div>

                    <select>
                        <option>Last 30 days</option>
                        <option>Last 7 days</option>
                        <option>Last 12 months</option>
                    </select>

                </div>

                <div class="chart">

                    <div class="chart-line"></div>

                    <div class="chart-placeholder">
                        USER GROWTH CHART
                    </div>

                </div>

            </div>


            <div class="panel">

                <div class="panel-header">

                    <div>
                        <p class="panel-label">STORE</p>
                        <h3>Quick Overview</h3>
                    </div>

                </div>

                <div class="overview-item">
                    <span>Active Users</span>
                    <strong>1,284</strong>
                </div>

                <div class="overview-item">
                    <span>New Users</span>
                    <strong>+258</strong>
                </div>

                <div class="overview-item">
                    <span>Products</span>
<strong><?= $totalProducts ?></strong>
                </div>

<div class="overview-item">
    <span>In Stock</span>

    <strong>
        <?= $inStockProducts ?>
    </strong>
</div>

<div class="overview-item">
    <span>Out of Stock</span>

    <strong class="warning">
        <?= $outOfStockProducts ?>
    </strong>
</div>

            </div>

        </section>

    </main>

</div>

</body>

</html>