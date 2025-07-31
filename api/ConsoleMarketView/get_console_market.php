<?php
// api/get_console_sales_summary.php

header('Content-Type: application/json'); // Tell the browser this is JSON
header('Access-Control-Allow-Origin: *'); // Allow requests from any origin (for development)

// Include database connection
require_once '../database.php'; // Adjust path if db_connect.php is in a different directory

$response = [
    'success' => false,
    'message' => 'An unknown error occurred.',
    'data' => []
];

try {
    // SQL query to get total sales for each console, ordered by sales descending
    // We group by Console and Publisher to ensure unique console identifiers
    $sql = "SELECT TRIM(Platform) AS Platform, SUM(Sales) AS TotalSales
            FROM ConsoleSales
            WHERE Region = 'Global'
            GROUP BY TRIM(Platform)
            ORDER BY TotalSales DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $salesData = $stmt->fetchAll();

    $response['success'] = true;
    $response['message'] = 'Console sales data fetched successfully.';
    $response['data'] = $salesData;

} catch (PDOException $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
} catch (Exception $e) {
    $response['message'] = 'General error: ' . $e->getMessage();
}

echo json_encode($response);
?>