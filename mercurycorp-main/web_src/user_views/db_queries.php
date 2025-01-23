<?php
session_start();
include('../../data_src/includes/db_config.php');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $dbUsername, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Queries
    $recentOrders = $pdo->query("SELECT order_id, rec_id, order_date, order_text, physician_id 
                                 FROM physician_orders
                                 ORDER BY order_date DESC 
                                 LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

    $physicians = $pdo->query("SELECT physician_id, last_name, first_name FROM physician")
                      ->fetchAll(PDO::FETCH_ASSOC);

    $residents = $pdo->query("SELECT res_id, last_name, first_name FROM residents")
                     ->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
