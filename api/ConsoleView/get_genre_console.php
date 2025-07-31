<?php
// api/get_console_genre_distribution.php

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
        // SQL query to get genres for games available on the specified console
        // Uses LIKE to find the console name within the comma-separated 'Platform' string
        $sql = "SELECT Genre
                FROM Games
                WHERE Platform LIKE CONCAT('%, ', :console_name, ',%') OR Platform LIKE CONCAT('%, ', :console_name) OR Platform LIKE CONCAT(:console_name, ',%') OR Platform = :console_name"; // Filter by console

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':console_name', $consoleName, PDO::PARAM_STR);
        $stmt->execute();

        $allGenresForConsole = [];
        while ($row = $stmt->fetch()) {
            // Handle potential NULL or empty genre strings
            if (!empty($row['Genre'])) {
                $genresInGame = explode(',', $row['Genre']);
                foreach ($genresInGame as $genre) {
                    $trimmedGenre = trim($genre);
                    if (!empty($trimmedGenre)) {
                        $allGenresForConsole[] = $trimmedGenre;
                    }
                }
            }
        }

        // Count occurrences of each genre
        $genreCounts = array_count_values($allGenresForConsole);

        // Format for D3.js (e.g., [{genre: "Action", count: 123}, ...])
        $formattedGenreData = [];
        foreach ($genreCounts as $genre => $count) {
            $formattedGenreData[] = ['genre' => $genre, 'count' => $count];
        }

        // Sort by count for pie chart (optional, but good for consistent slice order)
        usort($formattedGenreData, function($a, $b) {
            return $b['count'] <=> $a['count']; // Descending order
        });

        $response['success'] = true;
        $response['message'] = 'Genre distribution data fetched successfully for ' . $consoleName . '.';
        $response['data'] = $formattedGenreData;

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