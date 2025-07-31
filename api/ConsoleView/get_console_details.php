<?php
// api/get_console_details.php

header('Content-Type: application/json'); // Tell the browser this is JSON
header('Access-Control-Allow-Origin: *'); // Allow requests from any origin (for development)

// Include database connection
require_once '../database.php'; // Adjust path if database.php is in a different directory

$response = [
    'success' => false,
    'message' => 'An unknown error occurred.',
    'data' => null
];

// Check if console_name parameter is provided
if (isset($_GET['console_name']) && !empty(trim($_GET['console_name']))) {
    $consoleName = trim($_GET['console_name']);

    try {
        // SQL query to get details for a specific console
        $sql = "SELECT ID, ConsoleName, Publisher, Released, Discontinued, OriginalPrice, CurrentPrice, Generation, ImageURL
                FROM ConsoleDetails
                WHERE ConsoleName = :console_name"; // Use a named placeholder for security

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':console_name', $consoleName, PDO::PARAM_STR); // Bind the parameter
        $stmt->execute();

        $consoleDetails = $stmt->fetch(); // Fetch a single row

        if ($consoleDetails) {
            // Handle 'Discontinued' value for 'Ongoing' display
            if ($consoleDetails['Discontinued'] === null) { // Check for NULL as per our previous discussion
                $consoleDetails['Discontinued'] = 'Ongoing';
            }
            $response['success'] = true;
            $response['message'] = 'Console details fetched successfully.';
            $response['data'] = $consoleDetails;
        } else {
            $response['message'] = 'Console not found.';
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