<?php

session_start();

if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    header("Location: index.php");
    exit;
}

require_once "config/database.php";

$productId = (int)($_GET["id"] ?? 0);

if ($productId > 0) {

    // Məhsula aid şəkilləri tapırıq
    $imageStmt = $db->prepare("
        SELECT image
        FROM product_images
        WHERE product_id = ?
    ");

    $imageStmt->execute([$productId]);

    $images = $imageStmt->fetchAll(PDO::FETCH_COLUMN);

    // Serverdəki şəkilləri silirik
    foreach ($images as $image) {

        $imagePath = __DIR__ . "/" . $image;

        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    // Database-dən şəkilləri silirik
    $deleteImages = $db->prepare("
        DELETE FROM product_images
        WHERE product_id = ?
    ");

    $deleteImages->execute([$productId]);

    // Məhsulu database-dən silirik
    $deleteProduct = $db->prepare("
        DELETE FROM products
        WHERE id = ?
    ");

    $deleteProduct->execute([$productId]);
}

header("Location: products.php");
exit;