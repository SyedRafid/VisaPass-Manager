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
                            <h2 class="page-title">Edit Passport Record</h2>
                            <div class="panel panel-default">
                                <div class="panel-heading">Edit Passport Details</div>
                                <div class="panel-body">
                                    <form id="addProfileForm" method="post" enctype="multipart/form-data">
                                        <?php
                                        $sql = "SELECT * FROM pass_info WHERE pass_id = :id";
                                        $query = $dbh->prepare($sql);
                                        $query->bindParam(':id', $id, PDO::PARAM_INT);
                                        $query->execute();
                                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                                        if ($query->rowCount() > 0) {
                                            foreach ($results as $result) {
                                                // Default values if the result is null
                                                $passNo = isset($result->passNo) ? htmlentities($result->passNo) : '';
                                                $doi = isset($result->doi) ? htmlentities($result->doi) : '';
                                                $doe = isset($result->doe) ? htmlentities($result->doe) : '';
                                                $passPhoto = isset($result->passPhoto) ? htmlentities($result->passPhoto) : '';
                                                $pro_id = isset($result->pro_id) ? htmlentities($result->pro_id) : '';
                                            }
                                        }
                                        ?>
                                        <div class="form-group">
                                            <label for="profile">Name</label>
                                            <select name="pro_id" id="profile" class="form-select" required>
                                                <option value="">Select profile</option>
                                                <?php
                                                $query = "SELECT p.pro_id, p.name, p.pNumber, ct.contType 
                                                          FROM profile p
                                                          LEFT JOIN contact_type ct ON ct.cont_id = p.cont_id
                                                          WHERE p.status = 'active'";
                                                $stmt = $dbh->prepare($query);
                                                $stmt->execute();
                                                $results = $stmt->fetchAll(PDO::FETCH_OBJ);

                                                foreach ($results as $row) {
                                                    $selected = ($pro_id == $row->pro_id) ? 'selected' : '';
                                                    echo "<option value='" . htmlentities($row->pro_id) . "' $selected>" .
                                                        htmlentities("{$row->name} ({$row->pNumber} - {$row->contType})") .
                                                        "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="passport">Passport No.</label>
                                            <input type="text" name="passport" class="form-control" value="<?php echo $passNo; ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="dIssue">Date of Issue</label>
                                            <input type="text" name="dIssue" id="datepicker-issue" class="form-control" value="<?php echo $doi; ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="dExpiry">Date of Expiry</label>
                                            <input type="text" name="dExpiry" id="datepicker-expiry" class="form-control" value="<?php echo $doe; ?>" required>
                                        </div>
                                        &nbsp;
                                        <div class="form-group">
                                            <label for="passimage">Select a Passport Photo (max 5MB)</label>
                                            <input type="file" name="passimage" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf">
                                        </div>

                                        <input type="hidden" name="id" class="form-control" value="<?php echo $id; ?>" required>
                                        <input type="hidden" name="oldPassImage" class="form-control" value="<?php echo $passPhoto; ?>" required>

                                        <button type="submit" name="submit" class="btn" style="background-color: #2b7f19; color: white;">Submit</button>
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
            $(document).ready(function() {
                if (!$('#profile').data('select2')) {
                    $('#profile').select2({
                        placeholder: "Select Name",
                        allowClear: true,
                        width: '100%'
                    });
                }
            });

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
                    url: 'editPassRecord_pro.php',
                    type: 'POST',
                    data: formData,
                    contentType: false, // Important for file upload
                    processData: false, // Important for file upload
                    success: function(response) {
                        response = response.trim();

                        if (response === "success") {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Passport record has been successfully updated.',
                                icon: 'success',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'managePassRec.php';
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