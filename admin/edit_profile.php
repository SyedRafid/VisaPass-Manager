<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($id == 0) {
        echo "<script>
            alert('Invalid Profile ID');
            window.location.href = 'manage_record.php';
          </script>";
        exit;
    }
    $title = "Edit Profile";
?>
    <!doctype html>
    <html lang="en" class="no-js">

    <?php require('includes/_head.php') ?>

    <body>
        <?php include('includes/header.php'); ?>

        <div class="ts-main-content">
            <?php include('includes/leftbar.php'); ?>
            <div class="content-wrapper">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <h2 class="page-title">Edit Profile</h2>
                            <div class="panel panel-default">
                                <div class="panel-heading">Edit Details</div>
                                <div class="panel-body">
                                    <form id="addProfileForm" method="post" enctype="multipart/form-data">
                                        <?php
                                        $sql = "SELECT * from profile where pro_id = :id";
                                        $query = $dbh->prepare($sql);
                                        $query->bindParam(':id', $id, PDO::PARAM_INT);
                                        $query->execute();
                                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                                        if ($query->rowCount() > 0) {
                                            foreach ($results as $result) {
                                                // Default values if the result is null
                                                $name = isset($result->name) ? htmlentities($result->name) : '';
                                                $nationality = isset($result->nationality) ? htmlentities($result->nationality) : '';
                                                $dob = isset($result->dob) ? htmlentities($result->dob) : '';
                                                $phone = isset($result->pNumber) ? htmlentities($result->pNumber) : '';
                                                $cont_id = isset($result->cont_id) ? htmlentities($result->cont_id) : '';
                                        ?>

                                                <div class="form-group">
                                                    <label for="fullName">Name</label>
                                                    <input type="text" name="name" class="form-control" value="<?php echo $name; ?>" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="nationality">Nationality</label>
                                                    <input type="text" name="nationality" class="form-control" value="<?php echo $nationality; ?>" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="dBirth">Date of Birth</label>
                                                    <input type="text" name="dBirth" id="datepicker-dob" class="form-control" value="<?php echo $dob; ?>" required>
                                                </div>

                                                <div class="form-row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="contType" class="form-label">Contact Type<span style="color: red;">*</span></label>
                                                        <select name="contType" id="contType" class="form-select" required>
                                                            <option value="">Select Contact Type</option>
                                                            <?php
                                                            $query = "SELECT cont_id, contType FROM `contact_type`";
                                                            $stmt = $dbh->prepare($query);
                                                            $stmt->execute();
                                                            $results = $stmt->fetchAll(PDO::FETCH_OBJ);
                                                            foreach ($results as $row) {
                                                                $selected = ($cont_id == $row->cont_id) ? 'selected' : '';
                                                                echo "<option value='" . htmlentities($row->cont_id) . "' $selected>" . htmlentities($row->contType) . "</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6 mb-3" id="passportDiv">
                                                        <label for="phone">Contact Number<span style="color: red;"> *</span></label>
                                                        <input type="tel" name="phone" class="form-control" value="<?php echo $phone; ?>" required>
                                                    </div>
                                                </div>
                                                &nbsp;
                                                <div class="form-group">
                                                    <label for="proImage">Select a Profile Photo. (max 3MB)</label>
                                                    <input type="file" name="proImage" class="form-control" accept="image/*">
                                                </div>

                                                <div class="form-group">
                                                    <input type="hidden" name="id" class="form-control" value="<?php echo $id; ?>" required>
                                                </div>
                                        <?php
                                            }
                                        }
                                        ?>
                                        <button type="submit" id="submit" class="btn" style="background-color: #2b7f19; color: white;">Update</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Scripts -->
        <?php require('includes/_scripts.php') ?>
        <script>
            flatpickr("#datepicker-dob", {
                dateFormat: "Y-m-d",
                maxDate: "today",
                disableMobile: true
            });

            $(document).ready(function() {
                if (!$('#contType').data('select2')) {
                    $('#contType').select2({
                        placeholder: "Select Contact Type...",
                        allowClear: true,
                        width: '100%'
                    });
                }
            });

            $('#addProfileForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this)[0]; // Get raw DOM form
                var formData = new FormData(form); // Create FormData object

                var submitButton = $(this).find('button[type="submit"]');
                var originalButtonText = submitButton.html();

                submitButton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

                $.ajax({
                    url: 'editProfile_pro.php',
                    type: 'POST',
                    data: formData,
                    contentType: false, // Important for file upload
                    processData: false, // Important for file upload
                    success: function(response) {
                        response = response.trim();

                        if (response === "success") {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Profile information has been successfully updated.',
                                icon: 'success',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'manage_profile.php';
                                }
                            });

                        } else if (response.startsWith("error:")) {
                            Swal.fire({
                                title: 'Error!',
                                text: response.replace('error:', '').trim(),
                                icon: 'error',
                            });
                        } else if (response === "warning") {
                            Swal.fire({
                                title: 'Warning',
                                text: 'Please fill all required fields correctly.',
                                icon: 'warning',
                            });
                        } else {
                            // Log the response to console for debugging
                            console.log("Unexpected Response from server:", response);
                            Swal.fire({
                                title: 'Unexpected Response',
                                text: 'Server gave an unknown reply. Please contact support.',
                                icon: 'error',
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("XHR Error:", xhr);
                        console.error("Status:", status);
                        console.error("Error:", error);
                        Swal.fire({
                            title: 'Unexpected Response',
                            text: 'A server error occurred. Please contact support.',
                            icon: 'error',
                        });
                    },
                    complete: function() {
                        submitButton.prop('disabled', false).html(originalButtonText);
                    }
                });
            });
        </script>
    </body>

    </html>

<?php } ?>