<?php
// api/get_console_price_history.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../database.php'; // Adjust path

$response = [
    'success' => false,
    'message' => 'An unknown error occurred.',
    'data' => []
];

// Check if console_name parameter is provided
if (isset($_GET['console_name']) && !empty(trim($_GET['console_name']))) {
    $consoleName = trim($_GET['console_name']);

    try {
        // SQL query to get price history for a specific console
        $sql = "SELECT Date, Price
                FROM ConsolePrice
                WHERE Console = :console_name AND Price > 0 AND Price > 0
                ORDER BY Date ASC"; // Order by date for a proper line chart timeline

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':console_name', $consoleName, PDO::PARAM_STR);
        $stmt->execute();

        $priceHistory = $stmt->fetchAll();

        if ($priceHistory) {
            $response['success'] = true;
            $response['message'] = 'Price history fetched successfully for ' . $consoleName . '.';
            $response['data'] = $priceHistory;
        } else {
            $response['message'] = 'No price history found for ' . $consoleName . ' or console name is incorrect.';
        }

    } catch (PDOException $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    } catch (Exception $e) {
        $response['message'] = 'General error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Error: Console name parameter is missing or empty.';
}

echo json_encode($response);
?>