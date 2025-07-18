<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {

    $pro_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $pass_id = isset($_GET['pass_id']) ? intval($_GET['pass_id']) : 0;

    if (isset($_POST['submit'])) {
        $proId = !empty($pro_id) ? $pro_id : $_POST['pro_id'];
        $passId = !empty($pass_id) ? $pass_id : $_POST['passport'];
        $visaNO = $_POST['visa'];
        $visaYear = $_POST['visaYear'];
        $vCatagory = $_POST['vCatagory'];
        $dIssue = $_POST['dIssue'];
        $dExpiry = $_POST['dExpiry'];

        $visaImage = null;

        if (isset($_FILES['visaImage']) && $_FILES['visaImage']['error'] === UPLOAD_ERR_OK) {
            $fileSize = $_FILES['visaImage']['size'];
            $maxFileSize = 5 * 1024 * 1024; // 5 MB

            if ($fileSize > $maxFileSize) {
                echo "<script>
                alert('File size exceeds 5MB. Please upload a smaller file.');
                window.location.href = 'add_visa_record.php';
              </script>";
                exit();
            }

            $targetDir = "../assets/img/visa/";
            $fileType = strtolower(pathinfo($_FILES["visaImage"]["name"], PATHINFO_EXTENSION));

            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'];

            if (!in_array($fileType, $allowedTypes)) {
                echo "<script>
                alert('Invalid file type. Only JPG, PNG, GIF, WEBP, and PDF are allowed.');
                window.location.href = 'add_visa_record.php';
              </script>";
                exit();
            }

            $uniqueFileName = uniqid() . '.' . $fileType;
            $targetFile = $targetDir . $uniqueFileName;

            $isValid = true;

            if ($fileType !== 'pdf') {
                $check = getimagesize($_FILES["visaImage"]["tmp_name"]);
                if ($check === false) {
                    $isValid = false;
                }
            }

            if ($isValid) {
                if (move_uploaded_file($_FILES["visaImage"]["tmp_name"], $targetFile)) {
                    $visaImage = $uniqueFileName; // Store only filename in DB

                    if (!empty($proId) && !empty($passId) && !empty($visaNO) && !empty($visaYear) && !empty($vCatagory) && !empty($dIssue) && !empty($dExpiry)) {
                        $sql = "INSERT INTO visa_info (pro_id, pass_id, visaNo, visaYear, visaCate, vDoi, vDoe, visaImage) VALUES (:proId, :passId, :visaNO, :visaYear, :vCatagory, :dIssue, :dExpiry, :visaImage)";
                        $stmt = $dbh->prepare($sql);
                        $stmt->bindParam(':proId', $proId, PDO::PARAM_STR);
                        $stmt->bindParam(':passId', $passId, PDO::PARAM_STR);
                        $stmt->bindParam(':visaNO', $visaNO, PDO::PARAM_STR);
                        $stmt->bindParam(':visaYear', $visaYear, PDO::PARAM_STR);
                        $stmt->bindParam(':vCatagory', $vCatagory, PDO::PARAM_STR);
                        $stmt->bindParam(':dIssue', $dIssue, PDO::PARAM_STR);
                        $stmt->bindParam(':dExpiry', $dExpiry, PDO::PARAM_STR);
                        $stmt->bindParam(':visaImage', $visaImage, PDO::PARAM_STR);

                        if ($stmt->execute()) {
                            $success = "Record added successfully";
                            $redirect = "manage_Visa_record.php";
                        } else {
                            $errorInfo = implode(", ", $stmt->errorInfo());
                            $err = "Error adding application: $errorInfo";
                            $redirect = "add_visa_record.php";
                        }
                    } else {
                        $err = "Some required fields are empty.";
                        $redirect = "add_visa_record.php";
                    }
                } else {
                    $err = "Error: Failed to upload the file.";
                    $redirect = "add_visa_record.php";
                }
            } else {
                $err = "Error: File is not a valid image.";
                $redirect = "add_visa_record.php";
            }
        } else {
            if (!empty($proId) && !empty($passId) && !empty($visaNO) && !empty($visaYear) && !empty($vCatagory) && !empty($dIssue) && !empty($dExpiry)) {
                $sql = "INSERT INTO visa_info (pro_id, pass_id, visaNo, visaYear, visaCate, vDoi, vDoe, visaImage) VALUES (:proId, :passId, :visaNO, :visaYear, :vCatagory, :dIssue, :dExpiry, :visaImage)";
                $stmt = $dbh->prepare($sql);
                $stmt->bindParam(':proId', $proId, PDO::PARAM_STR);
                $stmt->bindParam(':passId', $passId, PDO::PARAM_STR);
                $stmt->bindParam(':visaNO', $visaNO, PDO::PARAM_STR);
                $stmt->bindParam(':visaYear', $visaYear, PDO::PARAM_STR);
                $stmt->bindParam(':vCatagory', $vCatagory, PDO::PARAM_STR);
                $stmt->bindParam(':dIssue', $dIssue, PDO::PARAM_STR);
                $stmt->bindParam(':dExpiry', $dExpiry, PDO::PARAM_STR);
                $stmt->bindParam(':visaImage', $visaImage, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    $success = "Record added successfully";
                    $redirect = "manage_Visa_record.php";
                } else {
                    $errorInfo = implode(", ", $stmt->errorInfo());
                    $err = "Error adding application: $errorInfo";
                    $redirect = "add_visa_record.php";
                }
            } else {
                $err = "Some required fields are empty.";
                $redirect = "add_visa_record.php";
            }
        }
    }

    $title = "Add Visa Record";
?>
    <!doctype html>
    <html lang="en" class="no-js">
    <?php include('includes/_head.php') ?>

    <body>
        <?php include('includes/header.php'); ?>
        <div class="ts-main-content">
            <?php include('includes/leftbar.php'); ?>
            <div class="content-wrapper">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <h2 class="page-title">Add Visa Record</h2>
                            <div class="panel panel-default">
                                <div class="panel-heading">Add Information</div>
                                <div class="panel-body">
                                    <form role="form" name="editApplication" method="post" enctype="multipart/form-data">
                                        <div class="form-row">
                                            <div class="col-md-6 mb-3">
                                                <label for="profile" class="form-label">Profile<span style="color: red;">*</span></label>
                                                <select name="pro_id" id="profile" class="form-select" <?php echo !empty($pro_id) ? 'disabled' : 'required'; ?>>
                                                    <option value="">Select profile</option>
                                                    <?php
                                                    $query = "SELECT pro_id, name FROM profile WHERE status = 'active'";
                                                    $stmt = $dbh->prepare($query);
                                                    $stmt->execute();
                                                    $results = $stmt->fetchAll(PDO::FETCH_OBJ);

                                                    foreach ($results as $row) {
                                                        $selected = (!empty($pro_id) && $pro_id == $row->pro_id) ? 'selected' : '';
                                                        echo '<option value="' . htmlspecialchars($row->pro_id) . '" ' . $selected . '>' .
                                                            htmlspecialchars($row->name) . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <?php
                                            $passNo = '';
                                            if (!empty($pass_id)) {
                                                // Securely fetch the passport number for the given pass_id
                                                $query = "SELECT passNo FROM pass_info WHERE pass_id = :pass_id";
                                                $stmt = $dbh->prepare($query);
                                                $stmt->bindParam(':pass_id', $pass_id, PDO::PARAM_INT);
                                                $stmt->execute();
                                                $result = $stmt->fetch(PDO::FETCH_OBJ);
                                                if ($result) {
                                                    $passNo = htmlentities($result->passNo);
                                                }
                                            }
                                            ?>

                                            <div class="col-md-6 mb-3" id="passportDiv">
                                                <label for="passport">Passport No.<span style="color: red;">*</span></label>

                                                <?php if (!empty($pass_id)) : ?>
                                                    <input type="text" name="visa" class="form-control" value="<?php echo $passNo; ?>" required disabled>
                                                <?php else : ?>
                                                    <select name="passport" id="passport" class="form-select" required>
                                                        <option value="">Select Passport No.</option>
                                                        <!-- Passports will be dynamically loaded here -->
                                                    </select>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="visa">Visa No.</label>
                                            <input type="text" name="visa" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="visaYear">Visa Year</label>
                                            <input type="text" name="visaYear" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="vCatagory">Visa Category</label>
                                            <input type="text" name="vCatagory" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="dIssue">Date of Issue</label>
                                            <input type="text" name="dIssue" id="datepicker-issue" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="dExpiry">Date of Expiry</label>
                                            <input type="text" name="dExpiry" id="datepicker-expiry" class="form-control" required>
                                        </div>

                                        &nbsp;
                                        <div class="form-group">
                                            <label for="visaImage">Select a Visa Photo (max 5MB)</label>
                                            <input type="file" name="visaImage" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf">
                                        </div>

                                        <button type="submit" name="submit" class="btn" style="background-color: #2b7f19; color: white;">Submit</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include('includes/_scripts.php'); ?>
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

            $(document).ready(function() {
                $('#passport').select2({
                    placeholder: "Select Passport No.",
                    allowClear: true,
                    width: '100%'
                });
            });

            var selectedPassId = <?php echo isset($pass_id) ? json_encode($pass_id) : 'null'; ?>;

            $(document).ready(function() {
                $('#profile').on('change', function() {
                    var profileId = $(this).val();

                    if (profileId) {
                        $.ajax({
                            url: 'fetch_passports.php',
                            type: 'POST',
                            data: {
                                profile_id: profileId
                            },
                            success: function(response) {
                                $('#passport').html(response);
                            },
                            error: function() {
                                alert('Error fetching passport numbers.');
                            }
                        });
                    } else {
                        $('#passport').html('<option value="">Select Passport No.</option>');
                    }
                });
            });

            const commonPickerOptions = {
                dateFormat: "Y-m-d",
                defaultDate: new Date().toISOString().split('T')[0],
                disableMobile: true
            };

            flatpickr("#datepicker-issue", commonPickerOptions);
            flatpickr("#datepicker-expiry", commonPickerOptions);
        </script>
    </body>

    </html>
<?php } ?>