<?php
include('includes/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $passportno = $_POST['passportno'];

    // Prepare the SQL query
    $sql = "SELECT 
                p.*,
                pi.*,
                vi.*
            FROM 
               pass_info pi
            LEFT JOIN 
               `profile` p ON pi.pro_id = p.pro_id
            LEFT JOIN
                visa_info vi ON pi.pass_id = vi.pass_id
            WHERE passNo = :passportno";
    $query = $dbh->prepare($sql);
    $query->bindParam(':passportno', $passportno, PDO::PARAM_STR);
    $query->execute();

    // Fetch the result
    $result = $query->fetch(PDO::FETCH_ASSOC);

    function formatDate($dateStr)
    {
        if (!$dateStr) return "N/A";
        try {
            $date = new DateTime($dateStr);
            return $date->format('jS M Y'); // e.g., 13th Apr 2025
        } catch (Exception $e) {
            return "Invalid Date";
        }
    }

    if ($result) {
        echo json_encode([
            'success' => true,
            'name' => $result['name'],
            'imageBase64' => $result['proPhoto'],
            'nationality' => $result['nationality'],
            'passport' => $result['passNo'],
            'doi' => formatDate($result['doi']),
            'doe' => formatDate($result['doe']),
            'visaNo' => $result['visaNo'],
            'vDoi' => formatDate($result['vDoi']),
            'vDoe' => formatDate($result['vDoe']),
        ]);
    } else {
        // Return a JSON response with an error
        echo json_encode([
            'success' => false,
            'message' => 'No record found for this passport number'
        ]);
    }
} else {
    // Invalid request method
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}
