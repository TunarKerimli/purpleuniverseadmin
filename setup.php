<?php

require_once __DIR__ . "/config/database.php";

/*
|--------------------------------------------------------------------------
| PRODUCTS
|--------------------------------------------------------------------------
*/

$db->exec("
    CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        price REAL NOT NULL DEFAULT 0,
        category TEXT DEFAULT 'album',
        icon TEXT DEFAULT '💿',
        description TEXT DEFAULT '',
        discount INTEGER DEFAULT 0,
        in_stock INTEGER DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
");

/*
|--------------------------------------------------------------------------
| PRODUCT IMAGES
|--------------------------------------------------------------------------
*/

$db->exec("
    CREATE TABLE IF NOT EXISTS product_images (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        product_id INTEGER NOT NULL,
        image TEXT NOT NULL,
        FOREIGN KEY (product_id)
            REFERENCES products(id)
            ON DELETE CASCADE
    )
");

/*
|--------------------------------------------------------------------------
| PROOFS
|--------------------------------------------------------------------------
*/

$db->exec("
    CREATE TABLE IF NOT EXISTS proofs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        image TEXT NOT NULL,
        caption TEXT DEFAULT '',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
");

/*
|--------------------------------------------------------------------------
| ORDERS
|--------------------------------------------------------------------------
*/

$db->exec("
    CREATE TABLE IF NOT EXISTS orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        customer_name TEXT,
        customer_contact TEXT,
        total REAL NOT NULL DEFAULT 0,
        status TEXT DEFAULT 'pending',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
");

/*
|--------------------------------------------------------------------------
| ORDER ITEMS
|--------------------------------------------------------------------------
*/

$db->exec("
    CREATE TABLE IF NOT EXISTS order_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_id INTEGER NOT NULL,
        product_id INTEGER,
        product_name TEXT NOT NULL,
        quantity INTEGER NOT NULL DEFAULT 1,
        price REAL NOT NULL DEFAULT 0,
        FOREIGN KEY (order_id)
            REFERENCES orders(id)
            ON DELETE CASCADE,
        FOREIGN KEY (product_id)
            REFERENCES products(id)
            ON DELETE SET NULL
    )
");

/*
|--------------------------------------------------------------------------
| ADMIN USERS
|--------------------------------------------------------------------------
*/

$db->exec("
    CREATE TABLE IF NOT EXISTS admin_users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
");

/*
|--------------------------------------------------------------------------
| DEFAULT ADMIN
|--------------------------------------------------------------------------
*/

$check = $db->prepare("
    SELECT COUNT(*) FROM admin_users
    WHERE username = ?
");

$check->execute(["puays2026puays"]);

if ((int)$check->fetchColumn() === 0) {

    $password = password_hash(
        "puays2026puays",
        PASSWORD_DEFAULT
    );

    $insert = $db->prepare("
        INSERT INTO admin_users (username, password)
        VALUES (?, ?)
    ");

    $insert->execute([
        "puays2026puays",
        $password
    ]);
}

/*
|--------------------------------------------------------------------------
| Orders table
|--------------------------------------------------------------------------
*/

$db->exec("
    CREATE TABLE IF NOT EXISTS orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        customer_name TEXT,
        customer_email TEXT,
        customer_phone TEXT,
        total REAL NOT NULL DEFAULT 0,
        status TEXT NOT NULL DEFAULT 'pending',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
");


/*
|--------------------------------------------------------------------------
| Order items table
|--------------------------------------------------------------------------
*/

$db->exec("
    CREATE TABLE IF NOT EXISTS order_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_id INTEGER NOT NULL,
        product_id INTEGER,
        product_name TEXT NOT NULL,
        price REAL NOT NULL DEFAULT 0,
        quantity INTEGER NOT NULL DEFAULT 1,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
    )
");

echo "Database updated successfully!";