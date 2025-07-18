<?php
include('includes/config.php');

if (isset($_POST['pro_id']) && isset($_POST['status'])) {
    $pro_id = $_POST['pro_id'];
    $status = $_POST['status'];

    $sql = "UPDATE profile SET status = :status WHERE pro_id = :pro_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':status', $status, PDO::PARAM_STR);
    $query->bindParam(':pro_id', $pro_id, PDO::PARAM_INT);

    if ($query->execute()) {
        echo "success";
    } else {
        echo "error";
    }
}
?>
