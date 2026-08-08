<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "../config/database.php";

try {

    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input) {
        throw new Exception("Yanlış məlumat göndərildi.");
    }

    $customerName = trim($input["customer_name"] ?? "");
    $customerEmail = trim($input["customer_email"] ?? "");
    $customerPhone = trim($input["customer_phone"] ?? "");
    $items = $input["items"] ?? [];

    if ($customerName === "") {
        throw new Exception("Müştəri adı daxil edilməlidir.");
    }

    if (empty($items)) {
        throw new Exception("Səbət boşdur.");
    }

    $db->beginTransaction();

    $total = 0;
    $preparedItems = [];

    foreach ($items as $item) {

        $productId = (int)($item["id"] ?? 0);
        $quantity = (int)($item["quantity"] ?? 0);

        if ($productId <= 0 || $quantity <= 0) {
            continue;
        }

        $stmt = $db->prepare("
            SELECT id, name, price, in_stock
            FROM products
            WHERE id = ?
        ");

        $stmt->execute([$productId]);

        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            continue;
        }

        if ((int)$product["in_stock"] !== 1) {
            throw new Exception(
                $product["name"] . " hazırda stokda yoxdur."
            );
        }

        $price = (float)$product["price"];

        $lineTotal = $price * $quantity;

        $total += $lineTotal;

        $preparedItems[] = [
            "product_id" => $productId,
            "product_name" => $product["name"],
            "price" => $price,
            "quantity" => $quantity
        ];
    }

    if (empty($preparedItems)) {
        throw new Exception("Sifariş üçün məhsul tapılmadı.");
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE ORDER
    |--------------------------------------------------------------------------
    */

    $stmt = $db->prepare("
        INSERT INTO orders (
            customer_name,
            customer_email,
            customer_phone,
            total,
            status
        )
        VALUES (?, ?, ?, ?, 'pending')
    ");

    $stmt->execute([
        $customerName,
        $customerEmail,
        $customerPhone,
        $total
    ]);

    $orderId = (int)$db->lastInsertId();


    /*
    |--------------------------------------------------------------------------
    | CREATE ORDER ITEMS
    |--------------------------------------------------------------------------
    */

    $itemStmt = $db->prepare("
        INSERT INTO order_items (
            order_id,
            product_id,
            product_name,
            price,
            quantity
        )
        VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($preparedItems as $item) {

        $itemStmt->execute([
            $orderId,
            $item["product_id"],
            $item["product_name"],
            $item["price"],
            $item["quantity"]
        ]);
    }


    $db->commit();


    echo json_encode([
        "success" => true,
        "order_id" => $orderId,
        "total" => number_format($total, 2, ".", ""),
        "message" => "Sifariş uğurla yaradıldı."
    ]);

} catch (Throwable $e) {

    if ($db->inTransaction()) {
        $db->rollBack();
    }

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}