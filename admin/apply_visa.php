<?php
include('includes/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['visa_id'])) {
    $visa_id = intval($_POST['visa_id']);  // Ensure visa_id is an integer

    // Check if visa_id exists
    $check = $dbh->prepare("SELECT * FROM visa_info WHERE visa_id = ?");
    $check->execute([$visa_id]);

    if ($check->rowCount() > 0) {
        // Update the vStatus to 'applied' for the given visa_id
        $update = $dbh->prepare("UPDATE visa_info SET vStatus = 'applied' WHERE visa_id = ?");
        $update->execute([$visa_id]);

        if ($update->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update the visa status.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Visa ID not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
