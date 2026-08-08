<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: https://purpleuniverse.site");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(204);
    exit;
}

require_once "../config/database.php";

try {

    $stmt = $db->query("
        SELECT
            id,
            name,
            price,
            category,
            in_stock,
            description
        FROM products
        ORDER BY id DESC
    ");

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as &$product) {

        $imageStmt = $db->prepare("
            SELECT image
            FROM product_images
            WHERE product_id = ?
            ORDER BY id ASC
        ");

        $imageStmt->execute([
            $product["id"]
        ]);

        $images = $imageStmt->fetchAll(PDO::FETCH_COLUMN);

        $product["images"] = array_map(
            function ($image) {
                return "https://puadminsiteprivate.onrender.com/" . $image;
            },
            $images
        );

        $product["price"] = (float)$product["price"];
        $product["in_stock"] = (int)$product["in_stock"];
        $product["discount"] = 0;
    }

    echo json_encode([
        "success" => true,
        "products" => $products
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}