<?php
include('includes/config.php');

if (isset($_POST['profile_id'])) {
    $profile_id = intval($_POST['profile_id']);

    $sql = "SELECT pass_id, passNo FROM pass_info WHERE pro_id = :profile_id ORDER BY created_at DESC";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':profile_id', $profile_id, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_OBJ);

    if ($stmt->rowCount() > 0) {
        echo '<option value="">Select Passport No.</option>';
        foreach ($results as $row) {
            echo "<option value='" . htmlentities($row->pass_id) . "'>" . htmlentities($row->passNo) . "</option>";
        }
    } else {
        echo '<option value="">No Passport Found</option>';
    }
}
?>
