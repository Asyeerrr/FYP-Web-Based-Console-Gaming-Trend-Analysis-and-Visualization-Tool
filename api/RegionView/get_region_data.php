<?php
// api/get_region_platform_domination.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../database.php'; // Adjust path

$response = [
    'success' => false,
    'message' => 'An unknown error occurred.',
    'data' => []
];

// Check if region_name parameter is provided
if (isset($_GET['region_name']) && !empty(trim($_GET['region_name']))) {
    $regionName = trim($_GET['region_name']);

    try {
        // SQL query to get total sales for each console within the specified region
        // This will allow you to build a pie chart of platform domination
        $sql = "SELECT Platform, SUM(Sales) AS TotalSales
                FROM ConsoleSales
                WHERE Region = :region_name
                GROUP BY Platform
                ORDER BY TotalSales DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':region_name', $regionName, PDO::PARAM_STR);
        $stmt->execute();

        $platformDominationData = $stmt->fetchAll();

        if ($platformDominationData) {
            $response['success'] = true;
            $response['message'] = 'Platform domination data fetched successfully for ' . $regionName . '.';
            $response['data'] = $platformDominationData;
        } else {
            $response['message'] = 'No platform sales data found for ' . $regionName . '.';
        }

    } catch (PDOException $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    } catch (Exception $e) {
        $response['message'] = 'General error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Error: Region name parameter is missing or empty.';
}

echo json_encode($response);
?>