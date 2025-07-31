<?php
// api/get_top_rated_games.php

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
        // SQL query to get top rated games for a specific console
        // We use LIKE %...% because the 'Platform' column contains comma-separated values.
        // It's important to include commas/boundaries in the LIKE pattern if platform names can be substrings of others,
        // but for exact console names, %Console Name% is usually sufficient.
        // For robustness, you might consider:
        // WHERE Platform LIKE CONCAT('%', :console_name, '%') OR Platform LIKE CONCAT(:console_name, ',%') OR Platform LIKE CONCAT('%,', :console_name, '%') OR Platform = :console_name
        // However, for typical D3.js display, a simple LIKE is often enough.
        $sql = "SELECT Name, Rating, Released, ESRB, ImageURL
                FROM Games
                WHERE Platform LIKE CONCAT('%, ', :console_name, ',%') OR Platform LIKE CONCAT(:console_name, ',%') OR Platform LIKE CONCAT('%, ':console_name) OR Platform = :console_name
                ORDER BY Rating DESC
                LIMIT 10"; // Limit to top 10 games, adjust as needed

        $stmt = $pdo->prepare($sql);
        // Bind the parameter with wildcards for LIKE operation
        // Note: We're adding '%' in CONCAT in the SQL query itself,
        // so we bind just the console name here.
        $stmt->bindParam(':console_name', $consoleName, PDO::PARAM_STR);
        $stmt->execute();

        $topGames = $stmt->fetchAll();

        if ($topGames) {
            $response['success'] = true;
            $response['message'] = 'Top rated games fetched successfully for ' . $consoleName . '.';
            $response['data'] = $topGames;
        } else {
            $response['message'] = 'No top rated games found for ' . $consoleName . ' or console name is incorrect.';
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