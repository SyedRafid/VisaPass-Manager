<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
}
$update = $_GET['update'];

$sqlCheck = "SELECT contType FROM contact_type WHERE cont_id = :cont_id";
$queryCheck = $dbh->prepare($sqlCheck);
$queryCheck->bindParam(':cont_id', $update, PDO::PARAM_INT);
$queryCheck->execute();
$result = $queryCheck->fetch(PDO::FETCH_ASSOC);
$contType = htmlentities($result['contType']);

if (isset($_POST['submit'])) {
    if (empty(trim($_POST['contType']))) {
        $err = "Blank Values Not Accepted";
        $redirect = "edit_contactType.php?update=" . urlencode($update);
    } else {
        $cat_name = ucfirst(trim($_POST['contType']));

        $kolaQuery = "SELECT COUNT(*) AS count FROM contact_type WHERE contType = :contType";
        $kolaStmt = $dbh->prepare($kolaQuery);
        $kolaStmt->bindParam(':contType', $cat_name, PDO::PARAM_STR);
        $kolaStmt->execute();
        $row = $kolaStmt->fetch(PDO::FETCH_ASSOC);

        if ($row['count'] > 0) {
            $err = "Contact Type Already Exists";
            $redirect = "contactType.php";
        } else {
            $postQuery = "UPDATE contact_type SET contType = :contType WHERE cont_id = :cont_id ";
            $kolaStmt = $dbh->prepare($postQuery);
            $kolaStmt->bindParam(':contType', $cat_name, PDO::PARAM_STR);
            $kolaStmt->bindParam(':cont_id', $update, PDO::PARAM_STR);

            // Execute the query
            if ($kolaStmt->execute()) {
                $success = "Contact type updated successfully.";
                $redirect = "contactType.php";
            } else {
                $err = "Please Try Again Or Try Later!";
                $redirect = "contactType.php";
            }
        }
    }
}
$title = "Edit Contact Type";
?>
<!doctype html>
<html lang="en" class="no-js">
<?php include('includes/_head.php'); ?>

<body>
    <?php include('includes/header.php'); ?>
    <div class="ts-main-content">
        <?php include('includes/leftbar.php'); ?>
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="page-title">Update Contact Type</h2>
                        <div class="panel panel-default">
                            <div class="panel-heading">Fill all the information <span style="color: red;">*</span></div>
                            <div class="panel-body">
                                <div class="card-header border-0 mb-2">
                                    <form method="POST">
                                        <div class="form-group">
                                            <label for="contType" style="font-weight: bolder;">Contact Type</label>
                                            <input type="text" name="contType" id="contType" value="<?php echo $contType ?>" class="form-control" placeholder="Enter Contact Type">
                                        </div>
                                        <div class="text-center mt-3">
                                            <button type="submit" name="submit" class="btn" style="background-color: #2b7f19; color: white;">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Scripts -->
    <?php include('includes/_scripts.php'); ?>
</body>

</html>