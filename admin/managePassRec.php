<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
} else {

    if (isset($_GET['del']) && isset($_GET['id'])) {
        $id = $_GET['id'];

        $sqlCheck = "SELECT COUNT(*) FROM `visa_info` WHERE pass_id = :id";
        $queryCheck = $dbh->prepare($sqlCheck);
        $queryCheck->bindParam(':id', $id, PDO::PARAM_INT);
        $queryCheck->execute();
        $visaCount = $queryCheck->fetchColumn();

        if ($visaCount > 0) {
            $err = "Unable to delete the passport record!! Please change the status in profile page to inactive!!";
            $redirect = "managePassRec.php";
        } else {
            $sql = "SELECT passPhoto FROM pass_info WHERE pass_id = :id";
            $query = $dbh->prepare($sql);
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_OBJ);

            if ($result && !empty($result->passPhoto)) {
                $filePath = "../assets/img/passport/" . $result->passPhoto;

                if (file_exists($filePath)) {
                    // Try to delete the file
                    if (!unlink($filePath)) {
                        $err = "Unable to delete the passport image. Please try again later!";
                        $redirect = "managePassRec.php";
                        return;
                    }
                }
            }

            // Proceed with deleting the profile
            $sqlDeleteProfile = "DELETE FROM pass_info WHERE pass_id = :id";
            $queryDeleteProfile = $dbh->prepare($sqlDeleteProfile);
            $queryDeleteProfile->bindParam(':id', $id, PDO::PARAM_INT);

            if ($queryDeleteProfile->execute()) {
                $success = "Passport record deleted successfully.";
                $redirect = "managePassRec.php";
            } else {
                $err = "Something went wrong while deleting the passport record!";
                $redirect = "managePassRec.php";
            }
        }
    }
}
$title = "Manage Passport Record";
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
                        <h2 class="page-title">Manage Passport Record</h2>

                        <div class="panel panel-default">
                            <div class="panel-heading">Listed Passport Record</div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="table-responsive">
                                        <table id="zctb" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                            <thead>
                                                <tr style="text-align: center;">
                                                    <th>#</th>
                                                    <th>Photo</th>
                                                    <th>Name</th>
                                                    <th>Passport</th>
                                                    <th>Date of Issue</th>
                                                    <th>Date of Expiry</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sql = "
                                                SELECT 
                                                    p.name,
                                                    p.proPhoto,
                                                    pi.*
                                                FROM 
                                                    pass_info pi
                                                LEFT JOIN 
                                                    profile p ON p.pro_id = pi.pro_id
                                                ORDER BY 
                                                    pi.created_at DESC;
                                                ";
                                                $query = $dbh->prepare($sql);
                                                $query->execute();
                                                $results = $query->fetchAll(PDO::FETCH_OBJ);
                                                $cnt = 1;
                                                if ($query->rowCount() > 0) {
                                                    foreach ($results as $result) {
                                                ?>
                                                        <tr style="text-align: center;">
                                                            <td><?php echo htmlentities($cnt); ?></td>
                                                            <td>
                                                                <?php
                                                                $image = ($result->proPhoto);
                                                                ?>
                                                                <div style="display: inline-block; border: 1px solid #6a25d7; border-radius: 10px; padding: 5px; background-color: #f9f9f9; box-shadow: -3px 3px 10px rgba(0,0,0,0.34); width: 100px; height: 100px; overflow: hidden; text-align: center;">
                                                                    <img src="<?php echo !empty($image) ? '../assets/img/profile/' . htmlentities($image) : 'uploads/default-image.png'; ?>"
                                                                        alt="Additional Image"
                                                                        style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 8px;">
                                                                </div>
                                                            </td>
                                                            <td><?php echo htmlentities($result->name); ?></td>
                                                            <td>
                                                                <?php if (!empty($result->passNo)): ?>
                                                                    <?php echo htmlentities($result->passNo); ?>
                                                                <?php endif; ?>

                                                                <?php if (!empty($result->passPhoto)): ?>
                                                                    <br><br>
                                                                    <a href="../assets/img/passport/<?php echo htmlentities($result->passPhoto); ?>"
                                                                        target="_blank"
                                                                        class="btn btn-sm btn-info"
                                                                        style="font-style: normal; font-size: 12px; padding: 5px 10px; margin-top: 10px;">
                                                                        <i class="fa fa-eye"></i>&nbsp; VIEW
                                                                    </a>
                                                                <?php endif; ?>

                                                                <?php if (empty($result->passNo) && empty($result->passPhoto)): ?>
                                                                    <span style="color: #999;">Not available</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                if (!empty($result->doi)) {
                                                                    $date = new DateTime($result->doi);
                                                                    echo $date->format('jS M Y');
                                                                } else {
                                                                    echo '<span style="color: #999;">Not available</span>';
                                                                }
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                if (!empty($result->doe)) {
                                                                    $date = new DateTime($result->doe);
                                                                    echo $date->format('jS M Y');
                                                                } else {
                                                                    echo '<span style="color: #999;">Not available</span>';
                                                                }
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <a href="edit_pass_record.php?id=<?php echo htmlentities($result->pass_id); ?>">
                                                                    <button class="btn btn-sm btn-primary">
                                                                        <i class="fas fa-edit"></i>
                                                                        Update
                                                                    </button>
                                                                </a>
                                                                <br><br>
                                                                <a href="managePassRec.php?id=<?php echo htmlentities($result->pass_id); ?>&del=delete">
                                                                    <button class="btn btn-sm btn-danger">
                                                                        <i class="fas fa-trash"></i>&nbsp;
                                                                        Delete
                                                                    </button>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                <?php $cnt = $cnt + 1;
                                                    }
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div> <!-- End table-responsive -->
                                </div> <!-- End row -->
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Loading Scripts -->
    <?php include('includes/_scripts.php'); ?>

    <script>
        $(document).ready(function() {
            $('.delete-btn').on('click', function(e) {
                e.preventDefault(); // stop immediate navigation

                var link = $(this).attr('href'); // get the href

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this action!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = link; // redirect manually
                    }
                });
            });
        });
    </script>
</body>

</html>