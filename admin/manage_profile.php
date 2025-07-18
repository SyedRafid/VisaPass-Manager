<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
} else {

    if (isset($_GET['del']) && isset($_GET['id'])) {
        $id = $_GET['id'];

        $sqlCheck = "SELECT COUNT(*) FROM `pass_info` WHERE pro_id = :id";
        $queryCheck = $dbh->prepare($sqlCheck);
        $queryCheck->bindParam(':id', $id, PDO::PARAM_INT);
        $queryCheck->execute();
        $passCount = $queryCheck->fetchColumn();

        $sqlCheck = "SELECT COUNT(*) FROM `visa_info` WHERE pro_id = :id";
        $queryCheck = $dbh->prepare($sqlCheck);
        $queryCheck->bindParam(':id', $id, PDO::PARAM_INT);
        $queryCheck->execute();
        $visaCount = $queryCheck->fetchColumn();

        if ($passCount > 0 || $visaCount > 0) {
            $err = "Unable to delete the profile!! Please change the status to inactive!!";
            $redirect = "manage_profile.php";
        } else {
            $sql = "SELECT proPhoto FROM profile WHERE pro_id = :id";
            $query = $dbh->prepare($sql);
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_OBJ);

            if ($result && !empty($result->proPhoto)) {
                $filePath = "../assets/img/profile/" . $result->proPhoto;

                if (file_exists($filePath)) {
                    // Try to delete the file
                    if (!unlink($filePath)) {
                        $err = "Unable to delete the profile photo. Please try again later!";
                        $redirect = "manage_profile.php";
                        return;
                    }
                }
            }

            // Proceed with deleting the profile
            $sqlDeleteProfile = "DELETE FROM profile WHERE pro_id = :id";
            $queryDeleteProfile = $dbh->prepare($sqlDeleteProfile);
            $queryDeleteProfile->bindParam(':id', $id, PDO::PARAM_INT);

            if ($queryDeleteProfile->execute()) {
                $success = "Profile deleted successfully.";
                $redirect = "manage_profile.php";
            } else {
                $err = "Something went wrong while deleting the profile!";
                $redirect = "manage_profile.php";
            }
        }
    }
}
$title = "Manage Profile";
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
                        <h2 class="page-title">Manage Profiles</h2>

                        <div class="panel panel-default">
                            <div class="panel-heading">Listed Profile</div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="table-responsive">
                                        <table id="zctb" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                            <thead>
                                                <tr style="text-align: center;">
                                                    <th>#</th>
                                                    <th>Photo</th>
                                                    <th>Name</th>
                                                    <th>Nationality</th>
                                                    <th>Date of Birth</th>
                                                    <th>Phone</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sql = "SELECT * FROM profile 
                                                        LEFT JOIN
                                                                 contact_type ON profile.cont_id = contact_type.cont_id
                                                        ORDER BY profile.status ASC;";
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
                                                            <td><?php echo htmlentities($result->nationality); ?></td>
                                                            <td>
                                                                <?php
                                                                if (!empty($result->dob)) {
                                                                    $date = new DateTime($result->dob);
                                                                    echo $date->format('jS M Y');
                                                                } else {
                                                                    echo '<span style="color: #999;">Not available</span>';
                                                                }
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php if (!empty($result->pNumber) && !empty($result->contType)): ?>
                                                                    <strong><?php echo htmlentities(ucfirst($result->contType)); ?>:</strong>
                                                                    <br><br>
                                                                    <?php echo htmlentities($result->pNumber); ?>
                                                                <?php else: ?>
                                                                    <span style="color: #999;">Not available</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                if ($result->status == 'active') {
                                                                    echo '<i style = "font-style: normal;" class="btn btn-sm btn-success"><i class="fa fa-check-circle"></i></i>';
                                                                } else {
                                                                    echo '<i style = "font-style: normal;" class="btn btn-sm btn-danger"><i class="fa fa-times-circle"></i></i>';
                                                                }
                                                                ?>
                                                                <br> &nbsp;
                                                                <select class="form-control form-control-sm status-select"
                                                                    name="status_<?php echo htmlentities($result->pro_id); ?>"
                                                                    data-id="<?php echo htmlentities($result->pro_id); ?>">
                                                                    <option value="active" <?php echo ($result->status == 'active') ? 'selected' : ''; ?>>Active</option>
                                                                    <option value="inactive" <?php echo ($result->status == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <a href="edit_profile.php?id=<?php echo htmlentities($result->pro_id); ?>">
                                                                    <button class="btn btn-sm btn-primary">
                                                                        <i class="fas fa-edit"></i>
                                                                        Update
                                                                    </button>
                                                                </a>
                                                                <br><br>
                                                                <a href="manage_profile.php?id=<?php echo htmlentities($result->pro_id); ?>&del=delete">
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