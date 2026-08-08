<?php

require_once "config/database.php";


/*
|--------------------------------------------------------------------------
| STATUS UPDATE
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (isset($_POST["update_status"])) {

        $orderId = (int) $_POST["order_id"];
        $status = $_POST["status"];

        $allowedStatuses = [
            "pending",
            "processing",
            "shipped",
            "completed",
            "cancelled"
        ];

        if (
            $orderId > 0 &&
            in_array($status, $allowedStatuses, true)
        ) {

            $stmt = $db->prepare("
                UPDATE orders
                SET status = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $status,
                $orderId
            ]);
        }

        header("Location: orders.php");
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE ORDER
    |--------------------------------------------------------------------------
    */

    if (isset($_POST["delete_order"])) {

        $orderId = (int) $_POST["order_id"];

        if ($orderId > 0) {

            /*
             * Əvvəlcə order items silinir
             */

            $stmt = $db->prepare("
                DELETE FROM order_items
                WHERE order_id = ?
            ");

            $stmt->execute([
                $orderId
            ]);


            /*
             * Sonra order silinir
             */

            $stmt = $db->prepare("
                DELETE FROM orders
                WHERE id = ?
            ");

            $stmt->execute([
                $orderId
            ]);
        }

        header("Location: orders.php");
        exit;
    }
}


/*
|--------------------------------------------------------------------------
| GET ORDERS
|--------------------------------------------------------------------------
*/

$stmt = $db->query("
    SELECT *
    FROM orders
    ORDER BY id DESC
");

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| GET ORDER ITEMS
|--------------------------------------------------------------------------
*/

$orderItems = [];

$itemStmt = $db->query("
    SELECT *
    FROM order_items
    ORDER BY id ASC
");

$items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($items as $item) {

    $orderId = (int) $item["order_id"];

    if (!isset($orderItems[$orderId])) {
        $orderItems[$orderId] = [];
    }

    $orderItems[$orderId][] = $item;
}


/*
|--------------------------------------------------------------------------
| STATUS HELPER
|--------------------------------------------------------------------------
*/

function getStatusClass($status)
{
    switch ($status) {

        case "processing":
            return "processing";

        case "shipped":
            return "shipped";

        case "completed":
            return "completed";

        case "cancelled":
            return "cancelled";

        default:
            return "pending";
    }
}


function getStatusText($status)
{
    switch ($status) {

        case "processing":
            return "Processing";

        case "shipped":
            return "Shipped";

        case "completed":
            return "Completed";

        case "cancelled":
            return "Cancelled";

        default:
            return "Pending";
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

<title>Orders — Purple Universe Admin</title>

<link rel="stylesheet" href="css/admin.css">

<style>

/* =========================================================
   ORDERS PAGE
========================================================= */

.orders-header {

    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 30px;

    margin-bottom: 30px;
}

.orders-description {

    color: #8c86a5;
    margin-top: 8px;

}

.orders-panel {

    background: #ffffff;

    border: 1px solid #eeeaf7;

    border-radius: 20px;

    overflow: hidden;

    box-shadow: 0 10px 30px rgba(50, 30, 90, 0.04);
}


/* TABLE HEADER */

.orders-table-header {

    display: grid;

    grid-template-columns:
        80px
        minmax(180px, 1.5fr)
        120px
        150px
        180px
        170px;

    gap: 15px;

    padding: 18px 24px;

    background: #faf9fd;

    border-bottom: 1px solid #eeeaf7;

    font-size: 11px;

    font-weight: 700;

    letter-spacing: 1px;

    color: #938ca9;
}


/* ORDER ROW */

.order-row {

    display: grid;

    grid-template-columns:
        80px
        minmax(180px, 1.5fr)
        120px
        150px
        180px
        170px;

    gap: 15px;

    align-items: center;

    padding: 22px 24px;

    border-bottom: 1px solid #f0edf7;

    transition: 0.2s ease;
}

.order-row:last-child {
    border-bottom: none;
}

.order-row:hover {
    background: #fcfbff;
}


/* ORDER ID */

.order-id {

    font-weight: 700;

    color: #7250a8;

}


/* CUSTOMER */

.customer-info {

    display: flex;

    flex-direction: column;

    gap: 4px;
}

.customer-info strong {

    color: #302b3d;

    font-size: 14px;
}

.customer-info small {

    color: #9a94a9;

    font-size: 12px;
}


/* TOTAL */

.order-total {

    font-weight: 700;

    color: #33284a;
}


/* ITEMS */

.order-items {

    display: flex;

    flex-direction: column;

    gap: 5px;
}

.order-item {

    font-size: 12px;

    color: #686176;
}

.order-item strong {

    color: #3d354b;
}


/* STATUS */

.status-form {

    margin: 0;
}

.status-select {

    width: 100%;

    padding: 9px 12px;

    border: 1px solid #e4dfed;

    border-radius: 10px;

    background: white;

    color: #4a4355;

    font-size: 12px;

    outline: none;

    cursor: pointer;
}

.status-select:focus {

    border-color: #9b72d1;

}


/* ACTIONS */

.order-actions {

    display: flex;

    align-items: center;

    gap: 8px;
}

.order-delete-form {

    margin: 0;
}

.order-delete-btn {

    border: none;

    background: #fff0f2;

    color: #d95367;

    border-radius: 9px;

    padding: 9px 12px;

    cursor: pointer;

    font-size: 12px;

    font-weight: 600;

    transition: 0.2s ease;
}

.order-delete-btn:hover {

    background: #ffe0e5;

}


/* DATE */

.order-date {

    color: #8d8799;

    font-size: 12px;

}


/* EMPTY */

.empty-orders {

    text-align: center;

    padding: 80px 30px;
}

.empty-orders .empty-icon {

    width: 65px;

    height: 65px;

    margin: 0 auto 18px;

    border-radius: 18px;

    display: flex;

    align-items: center;

    justify-content: center;

    background: #f5effc;

    color: #8c63bd;

    font-size: 25px;
}

.empty-orders h3 {

    margin: 0 0 8px;

    color: #3d354b;
}

.empty-orders p {

    margin: 0;

    color: #9992a8;
}


/* MOBILE */

@media (max-width: 1100px) {

    .orders-panel {

        overflow-x: auto;
    }

    .orders-table-header,
    .order-row {

        min-width: 900px;
    }

}

</style>

</head>


<body>


<!-- =========================================================
     SIDEBAR
========================================================= -->

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


        <a href="products.php">

            <span>◈</span>

            Products

        </a>


        <a href="orders.php" class="active">

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



<!-- =========================================================
     MAIN
========================================================= -->

<main class="main-content">


    <!-- TOPBAR -->

    <header class="topbar">

        <div>

            <p class="page-label">

                STORE MANAGEMENT

            </p>


            <h1>

                Orders

            </h1>

        </div>


        <div class="admin-profile">

            <div class="avatar">

                A

            </div>


            <div>

                <strong>

                    Admin

                </strong>


                <small>

                    Administrator

                </small>

            </div>

        </div>

    </header>



    <!-- ORDERS HEADER -->

    <section class="orders-header">

        <div>

            <p class="panel-label">

                ORDER MANAGEMENT

            </p>


            <h2>

                Customer Orders

            </h2>


            <p class="orders-description">

                Manage and track orders placed through your Purple Universe store.

            </p>

        </div>

    </section>



    <!-- ORDERS TABLE -->

    <section class="orders-panel">


        <?php if (count($orders) === 0): ?>


            <div class="empty-orders">

                <div class="empty-icon">

                    ▣

                </div>


                <h3>

                    No orders yet

                </h3>


                <p>

                    Customer orders will appear here after they place an order.

                </p>

            </div>


        <?php else: ?>


            <!-- TABLE HEADER -->

            <div class="orders-table-header">

                <span>

                    ORDER

                </span>


                <span>

                    CUSTOMER

                </span>


                <span>

                    ITEMS

                </span>


                <span>

                    TOTAL

                </span>


                <span>

                    STATUS

                </span>


                <span>

                    ACTION

                </span>

            </div>



            <?php foreach ($orders as $order): ?>


                <div class="order-row">


                    <!-- ORDER ID -->

                    <div class="order-id">

                        #<?= (int) $order["id"] ?>

                    </div>



                    <!-- CUSTOMER -->

                    <div class="customer-info">

                        <strong>

                            <?= htmlspecialchars(
                                $order["customer_name"] ?: "Unknown Customer"
                            ) ?>

                        </strong>


                        <?php if (!empty($order["customer_email"])): ?>

                            <small>

                                <?= htmlspecialchars(
                                    $order["customer_email"]
                                ) ?>

                            </small>

                        <?php endif; ?>


                        <?php if (!empty($order["customer_phone"])): ?>

                            <small>

                                <?= htmlspecialchars(
                                    $order["customer_phone"]
                                ) ?>

                            </small>

                        <?php endif; ?>


                        <?php if (!empty($order["created_at"])): ?>

                            <small class="order-date">

                                <?= htmlspecialchars(
                                    $order["created_at"]
                                ) ?>

                            </small>

                        <?php endif; ?>

                    </div>



                    <!-- ITEMS -->

                    <div class="order-items">

                        <?php

                        $itemsForOrder =
                            $orderItems[(int) $order["id"]]
                            ?? [];

                        ?>

                        <?php if (count($itemsForOrder) === 0): ?>

                            <span class="order-item">

                                No items

                            </span>

                        <?php else: ?>


                            <?php foreach ($itemsForOrder as $item): ?>

                                <span class="order-item">

                                    <strong>

                                        <?= htmlspecialchars(
                                            $item["product_name"]
                                        ) ?>

                                    </strong>

                                    ×

                                    <?= (int) $item["quantity"] ?>

                                </span>

                            <?php endforeach; ?>


                        <?php endif; ?>

                    </div>



                    <!-- TOTAL -->

                    <div class="order-total">

                        <?= number_format(
                            (float) $order["total"],
                            2
                        ) ?>

                        AZN

                    </div>



                    <!-- STATUS -->

                    <div>

                        <form
                            method="POST"
                            class="status-form"
                        >

                            <input
                                type="hidden"
                                name="order_id"
                                value="<?= (int) $order["id"] ?>"
                            >


                            <input
                                type="hidden"
                                name="update_status"
                                value="1"
                            >


                            <select
                                name="status"
                                class="status-select"
                                onchange="this.form.submit()"
                            >

                                <option
                                    value="pending"
                                    <?= $order["status"] === "pending"
                                        ? "selected"
                                        : "" ?>
                                >

                                    Pending

                                </option>


                                <option
                                    value="processing"
                                    <?= $order["status"] === "processing"
                                        ? "selected"
                                        : "" ?>
                                >

                                    Processing

                                </option>


                                <option
                                    value="shipped"
                                    <?= $order["status"] === "shipped"
                                        ? "selected"
                                        : "" ?>
                                >

                                    Shipped

                                </option>


                                <option
                                    value="completed"
                                    <?= $order["status"] === "completed"
                                        ? "selected"
                                        : "" ?>
                                >

                                    Completed

                                </option>


                                <option
                                    value="cancelled"
                                    <?= $order["status"] === "cancelled"
                                        ? "selected"
                                        : "" ?>
                                >

                                    Cancelled

                                </option>

                            </select>

                        </form>

                    </div>



                    <!-- ACTION -->

                    <div class="order-actions">


                        <form
                            method="POST"
                            class="order-delete-form"
                            onsubmit="return confirm('Bu sifarişi silmək istədiyinizə əminsiniz?')"
                        >

                            <input
                                type="hidden"
                                name="order_id"
                                value="<?= (int) $order["id"] ?>"
                            >


                            <input
                                type="hidden"
                                name="delete_order"
                                value="1"
                            >


                            <button
                                type="submit"
                                class="order-delete-btn"
                            >

                                🗑 Delete

                            </button>

                        </form>

                    </div>


                </div>


            <?php endforeach; ?>


        <?php endif; ?>


    </section>


</main>


</body>

</html>