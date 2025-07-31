<?php
// api/get_console_generation.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../database.php'; // Adjust path based on where you placed database.php

$response = [
    'success' => false,
    'message' => 'An unknown error occurred.',
    'data' => []
];

try {
    // SQL query to get generation details
    // Ordering by Generation and then Released year for line chart/timeline sequence
    $sql = "SELECT Generation, Console, Released, ImageURL
            FROM ConsoleGen 
            ORDER BY Generation ASC, Released ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $generationData = $stmt->fetchAll();

    $response['success'] = true;
    $response['message'] = 'Console generation data fetched successfully.';
    $response['data'] = $generationData;

} catch (PDOException $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
} catch (Exception $e) {
    $response['message'] = 'General error: ' . $e->getMessage();
}

echo json_encode($response);
?>