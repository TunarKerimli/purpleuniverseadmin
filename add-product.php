<?php

session_start();

if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    header("Location: index.php");
    exit;
}

require_once "config/database.php";

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $price = $_POST["price"] ?? "";
    $category = trim($_POST["category"] ?? "");
    $in_stock = isset($_POST["in_stock"]) ? 1 : 0;
    $description = trim($_POST["description"] ?? "");

    if ($name === "" || $price === "") {

        $error = "Product name and price are required.";

    } else {


        if ($error === "") {

$stmt = $db->prepare("
    INSERT INTO products
    (name, price, category, in_stock, description)
    VALUES
    (:name, :price, :category, :in_stock, :description)
");

            $stmt->execute([
    ":name" => $name,
    ":price" => $price,
    ":category" => $category,
    ":in_stock" => $in_stock,
    ":description" => $description
]);

$productId = $db->lastInsertId();

if (isset($_FILES["images"])) {

    $uploadDir = __DIR__ . "/uploads/products/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $allowedTypes = [
        "image/jpeg",
        "image/png",
        "image/webp",
        "image/gif"
    ];

    foreach ($_FILES["images"]["tmp_name"] as $key => $tmpName) {

        if ($_FILES["images"]["error"][$key] !== UPLOAD_ERR_OK) {
            continue;
        }

        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
$fileType = finfo_file($fileInfo, $tmpName);
finfo_close($fileInfo);

if (!in_array($fileType, $allowedTypes, true)) {
    continue;
}

        $extension = strtolower(
            pathinfo($_FILES["images"]["name"][$key], PATHINFO_EXTENSION)
        );

        $fileName = uniqid("product_", true) . "." . $extension;

        $destination = $uploadDir . $fileName;

        if (move_uploaded_file($tmpName, $destination)) {

            $imagePath = "uploads/products/" . $fileName;

            $imageStmt = $db->prepare("
                INSERT INTO product_images
                (product_id, image)
                VALUES
                (:product_id, :image)
            ");

            $imageStmt->execute([
                ":product_id" => $productId,
                ":image" => $imagePath
            ]);
        }
    }
}
            header("Location: products.php");
            exit;
        }
    }
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

    <title>Add Product — Purple Universe Admin</title>

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

                <h1>Add Product</h1>

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


        <section class="add-product-panel">

            <div class="add-product-title">

                <p class="panel-label">
                    PRODUCT CATALOG
                </p>

                <h2>
                    Add New Product
                </h2>

                <p>
                    Add a new item to your Purple Universe store.
                </p>

            </div>


            <?php if ($error !== ""): ?>

                <div class="form-error">
                    <?= htmlspecialchars($error) ?>
                </div>

            <?php endif; ?>


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
                        placeholder="e.g. BTS Album"
                        required
                    >

                </div>


                <div class="form-row">

                    <div class="form-group">

                        <label>
                            Price
                        </label>

                        <input
                            type="number"
                            name="price"
                            step="0.01"
                            min="0"
                            placeholder="29.99"
                            required
                        >

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
            checked
        >

        <span class="stock-toggle-text">
            Stokda var
        </span>

    </label>

</div>

                </div>


                <div class="form-group">

                    <label>
                        Category
                    </label>

                    <select name="category">

                        <option value="">
    Kateqoriya seç
</option>

<option value="Albom">
    Albom
</option>

<option value="Peluş">
    Peluş
</option>

<option value="Merch">
    Merch
</option>

<option value="Kart">
    Kart
</option>

<option value="Digər">
    Digər
</option>

                    </select>

                </div>


                <div class="form-group">

                    <label>
                        Product Image
                    </label>

<input
    type="file"
    name="images[]"
    accept="image/jpeg,image/png,image/webp,image/gif"
    multiple
>

                    <small class="input-help">
                        JPG, PNG, WEBP or GIF
                    </small>

                </div>


                <div class="form-group">

                    <label>
                        Description
                    </label>

                    <textarea
                        name="description"
                        rows="5"
                        placeholder="Write a short product description..."
                    ></textarea>

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
                        Add Product
                    </button>

                </div>

            </form>

        </section>

    </main>

</div>

</body>

</html>