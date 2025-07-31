<?php
// api/get_subscriptions.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../database.php'; // Adjust path

$response = [
    'success' => false,
    'message' => 'An unknown error occurred.',
    'data' => []
];

try {
    $whereClauses = [];
    $params = [];
    // Ensure ID is included in the SELECT statement for detailed display
    $sql = "SELECT ID, SubscriptionName, Platform, Tier, Console, Duration, Price, Benefits, OfficialURL FROM Subscriptions";

    // Define the benefit mapping as provided
    $benefitMapping = [
        // PlayStation Benefits (ensure these match your DB data exactly)
        "Ubisoft+ Classics" => ["Ubisoft+ Classics"],
        "PlayStation Plus Game Catalog" => ["PlayStation Plus Game Catalog"],
        "Classics Catalog" => ["Classics Catalog"],
        "PS5 Streaming" => ["PS5 Streaming"],
        "Game Trials" => ["Game Trials"],
        "Cloud Streaming" => ["Cloud Streaming"],
        "Sony Pictures Catalog" => ["Sony Pictures Catalog"],

        // Nintendo Benefits (ensure these match your DB data exactly)
        "Nintendo Classics (N64, GBA, GameCube, Sega Genesis)" => ["Nintendo Classics (NES, SNES, Game Boy, Nintendo 64, Game Boy Advance, SEGA Genesis, Nintendo GameCube exclusive to Nintendo Switch 2)"],
        "Family Plan" => ["8 Nintendo Accounts"],
        "DLC Bundle" => ["DLC(Mario Kart 8 Deluxe - Booster Course Pass, Animal Crossing: New Horizons - Happy Home Paradise, Splatoon 2: Octo Expansion)"],
        "Edition Upgrade" => ["Nintendo Switch 2 Edition Upgrade Pack (The Legend of Zelda: Tears of the Kingdom, The Legend of Zelda: Breath of the Wild)"],

        // Xbox Benefits (ensure these match your DB data exactly)
        "100+ Games Library Access" => ["100+ games library access on Console"],
        "Game Access on PC and Cloud" => ["PC and Cloud"],
        "Perks and In-Game Benefits for F2P Games" => ["Perks and in-Game Benefits for Free-to-Play Games"],
        "DayOne Game Access" => ["New Games on Day One"],
        "EA Play Membership" => ["EA Play Membership"],
    ];

    // --- Filter by Platform ---
    if (isset($_GET['platform']) && !empty(trim($_GET['platform']))) {
        $platformName = trim($_GET['platform']);
        $whereClauses[] = "Platform = :platform";
        $params[':platform'] = $platformName;
    }

    // --- Filter by Price Range ---
    if (isset($_GET['min_price']) && is_numeric($_GET['min_price'])) {
        $minPrice = (float)$_GET['min_price'];
        $whereClauses[] = "Price >= :min_price";
        $params[':min_price'] = $minPrice;
    }
    if (isset($_GET['max_price']) && is_numeric($_GET['max_price'])) {
        $maxPrice = (float)$_GET['max_price'];
        $whereClauses[] = "Price <= :max_price";
        $params[':max_price'] = $maxPrice;
    }

    // --- Filter by Benefit ---
    if (isset($_GET['benefit']) && is_array($_GET['benefit']) && !empty($_GET['benefit'])) {
        $benefitConditions = [];
        foreach ($_GET['benefit'] as $index => $selectedBenefitFromFrontend) {
            // Re-introducing urldecode() here because we've confirmed the direct API call works
            // when it receives %20, and this compensates for environments where PHP doesn't auto-decode
            // (or if something else encodes it before PHP sees it).
            $decodedSelectedBenefit = urldecode($selectedBenefitFromFrontend);
            $cleanedSelectedBenefit = trim($decodedSelectedBenefit);

            if (isset($benefitMapping[$cleanedSelectedBenefit])) {
                $dbBenefitStrings = $benefitMapping[$cleanedSelectedBenefit];

                $singleBenefitOrConditions = [];
                foreach ($dbBenefitStrings as $dbBenefitValue) {
                    if (!empty($dbBenefitValue)) {
                        $paramName = ":benefit_" . $index . "_" . md5($dbBenefitValue);
                        $singleBenefitOrConditions[] = "(
                            Benefits = " . $paramName . " OR
                            Benefits LIKE CONCAT(" . $paramName . ", ',%') OR
                            Benefits LIKE CONCAT('%, ', " . $paramName . ", '%') OR
                            Benefits LIKE CONCAT('%, ', " . $paramName . ")
                        )";
                        $params[$paramName] = $dbBenefitValue;
                    }
                }
                if (!empty($singleBenefitOrConditions)) {
                    $benefitConditions[] = "(" . implode(" OR ", $singleBenefitOrConditions) . ")";
                }
            }
        }
        if (!empty($benefitConditions)) {
            $whereClauses[] = "(" . implode(" AND ", $benefitConditions) . ")";
        }
    }


    // Append WHERE clauses if any
    if (!empty($whereClauses)) {
        $sql .= " WHERE " . implode(" AND ", $whereClauses);
    }

    // Add an ORDER BY for consistent results (e.g., by Platform, then Tier, then Duration)
    $sql .= " ORDER BY Platform ASC, FIELD(Tier, 'Basic', 'Mid', 'Premium') ASC, Duration ASC";

    $stmt = $pdo->prepare($sql);

    // Bind parameters dynamically
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }

    $stmt->execute();
    $subscriptions = $stmt->fetchAll();

    if ($subscriptions) {
        $response['success'] = true;
        $response['message'] = 'Subscription data fetched successfully.';
        $response['data'] = $subscriptions;
    } else {
        $response['message'] = 'No subscriptions found matching the criteria.';
    }

} catch (PDOException $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
} catch (Exception $e) {
    $response['message'] = 'General error: ' . $e->getMessage();
}

echo json_encode($response);
?>