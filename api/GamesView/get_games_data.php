<?php
// api/get_game_details.php

header('Content-Type: application/json'); // Tell the browser this is JSON
header('Access-Control-Allow-Origin: *'); // Allow requests from any origin (for development)

// Include database connection
require_once '../database.php'; // Adjust path if database.php is in a different directory

$response = [
    'success' => false,
    'message' => 'An unknown error occurred.',
    'data' => null
];

// Check if game_name parameter is provided for detailed view
if (isset($_GET['game_name']) && !empty(trim($_GET['game_name']))) {
    $gameName = trim($_GET['game_name']);

    try {
        // SQL query to get all details for a specific game
        // Using LIKE with wildcards to allow for partial matches in search,
        // Limit to 1, assuming unique or first match is sufficient for details view
        $sql = "SELECT Name, Released, Rating, Genre, Platform, Developer, Publisher, ESRB, Metacritic, ImageURL, Description
                FROM Games
                WHERE Name LIKE :game_name
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        // Bind the parameter with wildcards for LIKE operation
        $searchName = '%' . $gameName . '%'; // Add wildcards here
        $stmt->bindParam(':game_name', $searchName, PDO::PARAM_STR);
        $stmt->execute();

        $gameDetails = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch a single row as associative array

        if ($gameDetails) {
            // If ImageURL is null or empty, set a placeholder
            if (empty($gameDetails['ImageURL'])) {
                $gameDetails['ImageURL'] = 'https://placehold.co/400x300/cccccc/333333?text=No+Image+Available';
            }

            $response['success'] = true;
            $response['message'] = 'Game details fetched successfully.';
            $response['data'] = $gameDetails;
        } else {
            $response['message'] = 'Game not found with that name.';
        }

    } catch (PDOException $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    } catch (Exception $e) {
        $response['message'] = 'General error: ' . $e->getMessage();
    }
}
// Check if search_query parameter is provided for autocomplete suggestions
else if (isset($_GET['search_query']) && !empty(trim($_GET['search_query']))) {
    $searchQuery = trim($_GET['search_query']);

    try {
        // SQL query to get game names for autocomplete suggestions
        $sql = "SELECT Name
                FROM Games
                WHERE Name LIKE :search_query
                ORDER BY Name ASC
                LIMIT 10"; // Limit to top 10 suggestions

        $stmt = $pdo->prepare($sql);
        $searchTerm = '%' . $searchQuery . '%';
        $stmt->bindParam(':search_query', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();

        $suggestions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($suggestions) {
            // Extract just the names into a flat array for simpler JS handling
            $names = array_column($suggestions, 'Name');
            $response['success'] = true;
            $response['message'] = 'Game suggestions fetched successfully.';
            $response['data'] = $names;
        } else {
            $response['message'] = 'No suggestions found.';
        }

    } catch (PDOException $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    } catch (Exception $e) {
        $response['message'] = 'General error: ' . $e->getMessage();
    }
}
else {
    $response['message'] = 'Error: No valid game name or search query parameter provided.';
}

echo json_encode($response);
?>