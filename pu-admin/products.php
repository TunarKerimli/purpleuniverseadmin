<?php

session_start();

if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    header("Location: index.php");
    exit;
}

require_once "config/database.php";

$products = $db->query("
    SELECT * FROM products
    ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Products — Purple Universe Admin</title>

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

            <a href="dashboard.php">
                <span>⌂</span>
                Dashboard
            </a>

            <a href="products.php" class="active">
                <span>◈</span>
                Products
            </a>

            <a href="orders.php">
                <span>▣</span>
                Orders
            </a>

            <a href="users.php">
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


    <!-- MAIN -->

    <main class="main-content">

        <header class="topbar">

            <div>

                <p class="page-label">
                    STORE MANAGEMENT
                </p>

                <h1>Products</h1>

            </div>

            <div class="admin-profile">

                <div class="avatar">
                    A
                </div>

                <div>

                    <strong>Admin</strong>

                    <small>
                        Administrator
                    </small>

                </div>

            </div>

        </header>


        <!-- PRODUCTS HEADER -->

        <section class="products-header">

            <div>

                <p class="panel-label">
                    CATALOG
                </p>

                <h2>
                    Your Products
                </h2>

                <p class="products-description">
                    Manage the products available in your Purple Universe store.
                </p>

            </div>

            <a href="add-product.php" class="add-product-btn">
            + Add First Product
            </a>

        </section>


        <!-- PRODUCTS TABLE -->

        <section class="products-panel">

            <div class="products-table-header">

                <span>PRODUCT</span>
                <span>PRICE</span>
                <span>STOCK</span>
                <span>CATEGORY</span>
                <span>ACTION</span>

            </div>


            <?php if (count($products) === 0): ?>

                <div class="empty-products">

                    <div class="empty-icon">
                        ◈
                    </div>

                    <h3>
                        No products yet
                    </h3>

                    <p>
                        Your products will appear here after you add them.
                    </p>

                    <a href="#" class="add-product-btn">
                        + Add First Product
                    </a>

                </div>

            <?php else: ?>

                <?php foreach ($products as $product): ?>

                    <div class="product-row">

                        <div class="product-info">

                            <div class="product-image">

<?php

$imageStmt = $db->prepare("
    SELECT image
    FROM product_images
    WHERE product_id = ?
    ORDER BY id ASC
    LIMIT 1
");

$imageStmt->execute([$product["id"]]);

$productImage = $imageStmt->fetchColumn();

?>

<?php if ($productImage): ?>

    <img
        src="<?= htmlspecialchars($productImage) ?>"
        alt="<?= htmlspecialchars($product["name"]) ?>"
    >

<?php else: ?>

    <span>◈</span>

<?php endif; ?>
                            </div>

                            <div>

                                <strong>
                                    <?= htmlspecialchars($product["name"]) ?>
                                </strong>

                                <small>
                                    ID #<?= htmlspecialchars($product["id"]) ?>
                                </small>

                            </div>

                        </div>


                        <div class="product-price">

                            $<?= number_format((float)$product["price"], 2) ?>

                        </div>


<div class="product-stock">

    <?php if ((int)$product["in_stock"] === 1): ?>

        <span class="stock-badge in-stock">
            ● Stokda var
        </span>

    <?php else: ?>

        <span class="stock-badge out-of-stock">
            ● Stokda yoxdur
        </span>

    <?php endif; ?>

</div>


                        <div>

                            <span class="category-badge">

                                <?= htmlspecialchars($product["category"] ?: "Uncategorized") ?>

                            </span>

                        </div>


                        <div class="product-actions">

<a href="edit-product.php?id=<?= (int)$product["id"] ?>">
    Edit
</a>

<a
    href="delete-product.php?id=<?= (int)$product["id"] ?>"
    onclick="return confirm('Bu məhsulu silmək istədiyinizə əminsiniz?')"
>
    Delete
</a>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </section>

    </main>

</div>

</body>

</html>