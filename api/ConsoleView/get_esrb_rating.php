<?php
// api/get_esrb_rating_distribution.php

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
        // SQL query to get ESRB ratings for games available on the specified console
        // Uses LIKE to find the console name within the comma-separated 'Platform' string
        $sql = "SELECT ESRB
                FROM Games
                WHERE Platform LIKE CONCAT('%, ', :console_name, ',%') OR Platform LIKE CONCAT('%, ', :console_name) OR Platform LIKE CONCAT(:console_name, ',%') OR Platform = :console_name
                AND ESRB IS NOT NULL AND ESRB != ''"; // Filter out null/empty ESRB values

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':console_name', $consoleName, PDO::PARAM_STR);
        $stmt->execute();

        $allEsrbRatingsForConsole = [];
        while ($row = $stmt->fetch()) {
            $trimmedEsrb = trim($row['ESRB']);
            if (!empty($trimmedEsrb)) {
                $allEsrbRatingsForConsole[] = $trimmedEsrb;
            }
        }

        // Count occurrences of each ESRB rating
        $esrbCounts = array_count_values($allEsrbRatingsForConsole);

        // Format for D3.js (e.g., [{rating: "E", count: 123}, ...])
        $formattedEsrbData = [];
        foreach ($esrbCounts as $rating => $count) {
            $formattedEsrbData[] = ['rating' => $rating, 'count' => $count];
        }

        // Sort by count for consistent pie chart slice order (optional)
        usort($formattedEsrbData, function($a, $b) {
            return $b['count'] <=> $a['count']; // Descending order
        });

        $response['success'] = true;
        $response['message'] = 'ESRB rating distribution data fetched successfully for ' . $consoleName . '.';
        $response['data'] = $formattedEsrbData;

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