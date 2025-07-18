<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($id == 0) {
        echo "<script>
            alert('Invalid Visa ID');
            window.location.href = 'manage_record.php';
          </script>";
        exit;
    }
    $title = "Edit Record";
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
                            <h2 class="page-title">Edit Visa Record</h2>
                            <div class="panel panel-default">
                                <div class="panel-heading">Edit Details</div>
                                <div class="panel-body">
                                    <form id="addProfileForm" method="post" enctype="multipart/form-data">
                                        <?php
                                        $sql = "
                                         SELECT 
                                             p.name,
                                             pi.passNo,
                                             vi.*
                                         FROM 
                                             visa_info vi
                                         LEFT JOIN 
                                             pass_info pi ON pi.pass_id = vi.pass_id
                                         LEFT JOIN 
                                             profile p ON p.pro_id = pi.pro_id
                                         WHERE 
                                             vi.visa_id = :id;
                                     ";
                                        $query = $dbh->prepare($sql);
                                        $query->bindParam(':id', $id, PDO::PARAM_INT);
                                        $query->execute();
                                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                                        if ($query->rowCount() > 0) {
                                            foreach ($results as $result) {
                                                // Default values if the result is null
                                                $name = isset($result->name) ? htmlentities($result->name) : '';
                                                $passNo = isset($result->passNo) ? htmlentities($result->passNo) : '';
                                                $visaNo = isset($result->visaNo) ? htmlentities($result->visaNo) : '';
                                                $visaYear = isset($result->visaYear) ? htmlentities($result->visaYear) : '';
                                                $visaCate = isset($result->visaCate) ? htmlentities($result->visaCate) : '';
                                                $vDoi = isset($result->vDoi) ? htmlentities($result->vDoi) : '';
                                                $vDoe = isset($result->vDoe) ? htmlentities($result->vDoe) : '';
                                                $visaImage = isset($result->visaImage) ? htmlentities($result->visaImage) : '';
                                        ?>

                                                <div class="form-row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="name" class="form-label">Name<span style="color: red;">*</span></label>
                                                        <input type="text" value="<?php echo $name; ?>" class="form-control" disabled required>
                                                    </div>

                                                    <div class="col-md-6 mb-3" id="passportDiv">
                                                        <label for="passport">Passport No.<span style="color: red;">*</span></label>
                                                        <input type="text" value="<?php echo $passNo; ?>" class="form-control" disabled required>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="visa">Visa No.</label>
                                                    <input type="text" value="<?php echo $visaNo; ?>" name="visa" class="form-control" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="visaYear">Visa Year</label>
                                                    <input type="text" value="<?php echo $visaYear; ?>" name="visaYear" class="form-control" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="vCatagory">Visa Category</label>
                                                    <input type="text" value="<?php echo $visaCate; ?>" name="vCatagory" class="form-control" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="dIssue">Date of Issue</label>
                                                    <input type="text" value="<?php echo $vDoi; ?>" name="dIssue" id="datepicker-issue" class="form-control" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="dExpiry">Date of Expiry</label>
                                                    <input type="text" value="<?php echo $vDoe; ?>" name="dExpiry" id="datepicker-expiry" class="form-control" required>
                                                </div>

                                                &nbsp;
                                                <div class="form-group">
                                                    <label for="visaImage">Select a Visa Photo (max 5MB)</label>
                                                    <input type="file" name="visaImage" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf">
                                                </div>

                                                <input type="hidden" name="id" class="form-control" value="<?php echo $id; ?>" required>
                                                <input type="hidden" name="oldVisaImage" class="form-control" value="<?php echo $visaImage; ?>" required>

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
            const commonPickerOptions = {
                dateFormat: "Y-m-d",
                disableMobile: true
            };

            flatpickr("#datepicker-issue", commonPickerOptions);
            flatpickr("#datepicker-expiry", commonPickerOptions);


            $('#addProfileForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this)[0]; // Get raw DOM form
                var formData = new FormData(form); // Create FormData object

                var submitButton = $(this).find('button[type="submit"]');
                var originalButtonText = submitButton.html();

                submitButton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

                $.ajax({
                    url: 'edit_visa_record_pro.php',
                    type: 'POST',
                    data: formData,
                    contentType: false, // Important for file upload
                    processData: false, // Important for file upload
                    success: function(response) {
                        response = response.trim();

                        if (response === "success") {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Visa record has been successfully updated.',
                                icon: 'success',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'manage_Visa_record.php';
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