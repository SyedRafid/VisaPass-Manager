<?php
include('includes/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pass_id'])) {
    $pass_id = intval($_POST['pass_id']);  // Ensure pass_id is an integer

    // Check if pass_id exists
    $check = $dbh->prepare("SELECT * FROM pass_info WHERE pass_id = ?");
    $check->execute([$pass_id]);

    if ($check->rowCount() > 0) {
        // Update the pStatus to 'applied' for the given pass_id
        $update = $dbh->prepare("UPDATE pass_info SET pStatus = 'applied' WHERE pass_id = ?");
        $update->execute([$pass_id]);

        if ($update->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update the passport status.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Passport ID not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
