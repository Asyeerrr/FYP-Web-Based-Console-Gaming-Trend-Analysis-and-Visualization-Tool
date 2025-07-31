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
    // SQL query to get all Genres from Games table
    $sql = "SELECT Genre FROM Games";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $allGenres = [];
    while ($row = $stmt->fetch()) {
        // Handle potential NULL or empty genre strings
        if (!empty($row['Genre'])) {
            $genresInGame = explode(',', $row['Genre']);
            foreach ($genresInGame as $genre) {
                $trimmedGenre = trim($genre);
                if (!empty($trimmedGenre)) {
                    $allGenres[] = $trimmedGenre;
                }
            }
        }
    }

    // Count occurrences of each genre
    $genreCounts = array_count_values($allGenres);

    // Format for D3.js (e.g., [{genre: "Action", count: 123}, ...])
    $formattedGenreData = [];
    foreach ($genreCounts as $genre => $count) {
        $formattedGenreData[] = ['genre' => $genre, 'count' => $count];
    }

    // Sort by count for better visualization in a bar chart (optional, but good practice)
    usort($formattedGenreData, function($a, $b) {
        return $b['count'] <=> $a['count']; // Descending order
    });

    $response['success'] = true;
    $response['message'] = 'Genre distribution data fetched successfully.';
    $response['data'] = $formattedGenreData;

} catch (PDOException $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
} catch (Exception $e) {
    $response['message'] = 'General error: ' . $e->getMessage();
}

echo json_encode($response);
?>