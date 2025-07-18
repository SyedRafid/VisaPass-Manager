<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
} else {
    if (isset($_GET['del']) && isset($_GET['id'])) {
        $id = $_GET['id'];
        $visaImage = $_GET['visaImage'];

        try {
            // Start transaction
            $dbh->beginTransaction();

            // First, delete the visa_info record
            $sqlDeleteProfile = "DELETE FROM visa_info WHERE visa_id = :id";
            $queryDeleteProfile = $dbh->prepare($sqlDeleteProfile);
            $queryDeleteProfile->bindParam(':id', $id, PDO::PARAM_INT);

            if ($queryDeleteProfile->execute()) {
                $filePath = "../assets/img/visa/$visaImage";

                // Check if file exists
                if (file_exists($filePath) && is_file($filePath)) {
                    // Try to delete file
                    if (unlink($filePath)) {
                        // File deleted successfully, commit transaction
                        $dbh->commit();
                        $success = "Visa record deleted successfully.";
                        $redirect = "manage_Visa_record.php";
                    } else {
                        // File deletion failed, rollback
                        $dbh->rollBack();
                        $err = "Error deleting Visa image. Database changes undone.";
                        $redirect = "manage_Visa_record.php";
                    }
                } else {
                    // File not found, still OK to commit
                    $dbh->commit();
                    $success = "Visa record deleted successfully (no image found).";
                    $redirect = "manage_Visa_record.php";
                }
            } else {
                // SQL execution failed
                $dbh->rollBack();
                $err = "Error deleting Visa record.";
                $redirect = "manage_Visa_record.php";
            }
        } catch (Exception $e) {
            // Any unexpected error
            $dbh->rollBack();
            $err = "An unexpected error occurred: " . $e->getMessage();
            $redirect = "manage_Visa_record.php";
        }
    }
}

$title = "Manage Visa Record";
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
                        <h2 class="page-title">Manage Visa Record</h2>

                        <div class="panel panel-default">
                            <div class="panel-heading">Listed Records</div>
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
                                                    <th>Visa No.</th>
                                                    <th>Visa Year</th>
                                                    <th>Visa Category</th>
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
                                                 pi.passNo,
                                                 pi.passPhoto,
                                                 vi.*
                                             FROM 
                                                 visa_info vi
                                             LEFT JOIN 
                                                 pass_info pi ON pi.pass_id = vi.pass_id
                                             LEFT JOIN 
                                                 profile p ON p.pro_id = pi.pro_id
                                             ORDER BY 
                                                 vi.created_at DESC;
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
                                                                    <img src="<?php echo !empty($image) ? '../assets/img/profile/' . htmlentities($image) : '../assets/img/profile/no-image.ico'; ?>"
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
                                                            </td>                                                            <td>
                                                                <?php if (!empty($result->visaNo)): ?>
                                                                    <?php echo htmlentities($result->visaNo); ?>
                                                                <?php endif; ?>

                                                                <?php if (!empty($result->visaImage)): ?>
                                                                    <br><br>
                                                                    <a href="../assets/img/visa/<?php echo htmlentities($result->visaImage); ?>"
                                                                        target="_blank"
                                                                        class="btn btn-sm btn-info"
                                                                        style="font-style: normal; font-size: 12px; padding: 5px 10px; margin-top: 10px;">
                                                                        <i class="fa fa-eye"></i>&nbsp; VIEW
                                                                    </a>
                                                                <?php endif; ?>

                                                                <?php if (empty($result->visaNo) && empty($result->visaImage)): ?>
                                                                    <span style="color: #999;">Not available</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><?php echo htmlentities($result->visaYear); ?></td>
                                                            <td><?php echo htmlentities($result->visaCate); ?></td>
                                                            <td>
                                                                <?php
                                                                if (!empty($result->vDoi)) {
                                                                    $date = new DateTime($result->vDoi);
                                                                    echo $date->format('jS M Y');
                                                                } else {
                                                                    echo '<span style="color: #999;">Not available</span>';
                                                                }
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                if (!empty($result->vDoe)) {
                                                                    $date = new DateTime($result->vDoe);
                                                                    echo $date->format('jS M Y');
                                                                } else {
                                                                    echo '<span style="color: #999;">Not available</span>';
                                                                }
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <a href="edit_visa_record.php?id=<?php echo htmlentities($result->visa_id); ?>">
                                                                    <button class="btn btn-sm btn-primary">
                                                                        <i class="fas fa-edit"></i>
                                                                        Update
                                                                    </button>
                                                                </a>
                                                                <br><br>
                                                                <a href="manage_Visa_record.php?id=<?php echo htmlentities($result->visa_id); ?>&visaImage=<?php echo htmlentities($result->visaImage); ?>&del=delete">
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
            $('.status-select').change(function() {
                var selectElement = $(this);
                var newStatus = selectElement.val();
                var proId = selectElement.data('id');

                // Ask for confirmation with SweetAlert2
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You are about to change the status!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, change it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Send AJAX to update
                        $.ajax({
                            url: 'update_status.php', // Create this PHP file
                            type: 'POST',
                            data: {
                                pro_id: proId,
                                status: newStatus
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Updated!',
                                    'Status has been changed.',
                                    'success'
                                ).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                })
                            },
                            error: function() {
                                Swal.fire(
                                    'Error!',
                                    'Something went wrong!',
                                    'error'
                                )
                            }
                        });
                    } else {
                        // If canceled, reload old value
                        location.reload();
                    }
                });
            });
        });

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