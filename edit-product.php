<?php

session_start();

if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    header("Location: index.php");
    exit;
}

require_once "config/database.php";

$productId = (int)($_GET["id"] ?? 0);

if ($productId <= 0) {
    header("Location: products.php");
    exit;
}

$stmt = $db->prepare("
    SELECT *
    FROM products
    WHERE id = ?
");

$stmt->execute([$productId]);

$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: products.php");
    exit;
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $price = (float)($_POST["price"] ?? 0);
    $category = trim($_POST["category"] ?? "");
    $in_stock = isset($_POST["in_stock"]) ? 1 : 0;
    $description = trim($_POST["description"] ?? "");

    $update = $db->prepare("
        UPDATE products
        SET
            name = :name,
            price = :price,
            category = :category,
            in_stock = :in_stock,
            description = :description
        WHERE id = :id
    ");

    $update->execute([
        ":name" => $name,
        ":price" => $price,
        ":category" => $category,
        ":in_stock" => $in_stock,
        ":description" => $description,
        ":id" => $productId
    ]);

    header("Location: products.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Product — Purple Universe Admin</title>

    <link rel="stylesheet" href="css/admin.css">

</head>

<body>

<div class="admin-layout">

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


    <main class="main-content">

        <header class="topbar">

            <div>

                <p class="page-label">
                    STORE MANAGEMENT
                </p>

                <h1>Edit Product</h1>

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


        <section class="add-product-panel">

            <div class="add-product-title">

                <p class="panel-label">
                    PRODUCT CATALOG
                </p>

                <h2>
                    Edit Product
                </h2>

                <p>
                    Update your product information.
                </p>

            </div>


            <form
                method="POST"
                enctype="multipart/form-data"
                class="product-form"
            >

                <div class="form-group">

                    <label>
                        Product Name
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="<?= htmlspecialchars($product["name"]) ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Price
                    </label>

                    <input
                        type="number"
                        name="price"
                        step="0.01"
                        min="0"
                        value="<?= htmlspecialchars($product["price"]) ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Category
                    </label>

                    <select name="category">

                        <option value="Albom" <?= $product["category"] === "Albom" ? "selected" : "" ?>>
                            Albom
                        </option>

                        <option value="Peluş" <?= $product["category"] === "Peluş" ? "selected" : "" ?>>
                            Peluş
                        </option>

                        <option value="Merch" <?= $product["category"] === "Merch" ? "selected" : "" ?>>
                            Merch
                        </option>

                        <option value="Kart" <?= $product["category"] === "Kart" ? "selected" : "" ?>>
                            Kart
                        </option>

                        <option value="Digər" <?= $product["category"] === "Digər" ? "selected" : "" ?>>
                            Digər
                        </option>

                    </select>

                </div>


                <div class="form-group">

                    <label>
                        Stok statusu
                    </label>

                    <label class="stock-toggle">

                        <input
                            type="checkbox"
                            name="in_stock"
                            value="1"
                            <?= (int)$product["in_stock"] === 1 ? "checked" : "" ?>
                        >

                        <span class="stock-toggle-text">
                            Stokda var
                        </span>

                    </label>

                </div>


                <div class="form-group">

                    <label>
                        Description
                    </label>

                    <textarea
                        name="description"
                        rows="5"
                    ><?= htmlspecialchars($product["description"] ?? "") ?></textarea>

                </div>


                <div class="form-actions">

                    <a
                        href="products.php"
                        class="cancel-btn"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="save-product-btn"
                    >
                        Save Changes
                    </button>

                </div>

            </form>

        </section>

    </main>

</div>

</body>

</html>