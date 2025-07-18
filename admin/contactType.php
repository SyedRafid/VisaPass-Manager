<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
} else {
    if (isset($_GET['delete'])) {
        $id = intval($_GET['delete']);

        $sqlCheck = "SELECT COUNT(*) FROM `profile` WHERE cont_id = :id";
        $queryCheck = $dbh->prepare($sqlCheck);
        $queryCheck->bindParam(':id', $id, PDO::PARAM_INT);
        $queryCheck->execute();

        $paymentCount = $queryCheck->fetchColumn();
        if ($paymentCount > 0) {
            $err = "Unable to delete the contact type because there is existing profile information associated with it!!";
            $redirect = "contactType.php";
        } else {
            $sqlDeleteProfile = "DELETE FROM contact_type WHERE cont_id = :id";
            $queryDeleteProfile = $dbh->prepare($sqlDeleteProfile);
            $queryDeleteProfile->bindParam(':id', $id, PDO::PARAM_INT);

            if ($queryDeleteProfile->execute()) {
                $success = "Contact type deleted successfully.";
                $redirect = "contactType.php";
            } else {
                $err = "Something went wrong please try again later!!";
                $redirect = "contactType.php";
            }
        }
    }
    
    if (isset($_POST['submit'])) {
        if (empty(trim($_POST['contType']))) {
            $err = "Blank Values Not Accepted";
            $redirect = "contactType.php";
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
                $postQuery = "INSERT INTO contact_type (contType) VALUES (:contType)";
                $kolaStmt = $dbh->prepare($postQuery);
                $kolaStmt->bindParam(':contType', $cat_name, PDO::PARAM_STR);

                // Execute the query
                if ($kolaStmt->execute()) {
                    $success = "Contact type added successfully.";
                    $redirect = "contactType.php";
                } else {
                    $err = "Please Try Again Or Try Later!";
                    $redirect = "contactType.php";
                }
            }
        }
    }
}
$title = "Contact Type";
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
                        <h2 class="page-title">Manage Contact Type</h2>
                        <div class="card-header border-0 mb-2">
                            <form method="POST">
                                <div class="text-center mb-2" style="border-bottom: 2px solid #e4e4e4;">
                                    <h5 class="text-success border rounded py-2 px-3 mb-3" style="font-size: 16px; display: inline-block; border: 2px solid #2b7f19 !important;">
                                        Add New Contact Type
                                    </h5>
                                </div>
                                <div class="form-group">
                                    <label for="contType" style="font-weight: bolder;">New Contact Type <span style="color: red;">*</span></label>
                                    <input type="text" name="contType" id="contType" class="form-control" placeholder="Enter Contact Type">
                                </div>
                                <div class="text-center mt-3">
                                    <button type="submit" name="submit" class="btn" style="background-color: #2b7f19; color: white;">Submit</button>
                                </div>
                            </form>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">Listed Contact Type</div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table id="zctb" class="display table table-striped table-bordered table-hover" style="text-align: center;" cellspacing="0" width="100%">
                                        <thead class="thead-light">
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Creation Date</th>
                                                <th scope="col">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT * FROM  contact_type ";

                                            $query = $dbh->prepare($sql);
                                            $query->execute();
                                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                                            $cnt = 1;
                                            if ($query->rowCount() > 0) {
                                                foreach ($results as $result) {
                                            ?>
                                                    <tr>
                                                        <td><?php echo htmlentities($cnt); ?></td>
                                                        <td><?php echo htmlentities($result->contType); ?></td>
                                                        <td>
                                                            <?php echo date("h:i A", strtotime($result->created_at)) . " <strong style='color: red;'>:</strong> " . date("jS M Y", strtotime($result->created_at)); ?>
                                                        </td>
                                                        <td>                                            
                                                            <a href="edit_contactType.php?update=<?php echo htmlentities($result->cont_id); ?>">
                                                                <button class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-edit"></i>
                                                                    Update
                                                                </button>
                                                            </a>
                                                            <a href="contactType.php?delete=<?php echo htmlentities($result->cont_id); ?>">
                                                                <button class="btn btn-sm btn-danger">
                                                                    <i class="fas fa-trash"></i>
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
                                </div>
                            </div> <!-- End row -->
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