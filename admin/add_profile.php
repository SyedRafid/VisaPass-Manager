<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    if (isset($_POST['submit'])) {
        $name = $_POST['name'];
        $dBirth = $_POST['dBirth'];
        $nasti = $_POST['nasti'];
        $contType = $_POST['contType'];
        $pNumber = $_POST['pNumber'];
        $status = 'active';

        $proImage = null;

        if (isset($_FILES['proImage']) && $_FILES['proImage']['error'] === UPLOAD_ERR_OK) {
            $fileSize = $_FILES['proImage']['size'];
            $maxFileSize = 3 * 1024 * 1024; // 3 MB in bytes

            if ($fileSize > $maxFileSize) {
                echo "<script>
                        alert('File size exceeds 3MB. Please upload a smaller file.');
                        window.location.href = 'add_profile.php';
                      </script>";
                exit();
            }

            $targetDir = "../assets/img/profile/";
            $imageFileType = strtolower(pathinfo($_FILES["proImage"]["name"], PATHINFO_EXTENSION));

            // Generate a unique filename
            $uniqueFileName = uniqid() . '.' . $imageFileType;
            $targetFile = $targetDir . $uniqueFileName;

            // Check if the file is an image
            $check = getimagesize($_FILES["proImage"]["tmp_name"]);
            if ($check !== false) {
                if (move_uploaded_file($_FILES["proImage"]["tmp_name"], $targetFile)) {
                    $proImage = $uniqueFileName; // Store only the unique file name in the database
                } else {
                    $err = "Error: Failed to upload the image.";
                    $redirect = "add_profile.php";
                }
            } else {
                $err = "Error: File is not an image.";
                $redirect = "add_profile.php";
            }
        }

        if (!empty($name) && !empty($dBirth) && !empty($nasti) && !empty($contType) && !empty($pNumber)) {
            $sql = "INSERT INTO profile (name, dob, nationality, cont_id, pNumber, proPhoto, status) VALUES (:name, :dBirth, :nasti, :contType, :pNumber, :proImage, :status)";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':dBirth', $dBirth, PDO::PARAM_STR);
            $stmt->bindParam(':nasti', $nasti, PDO::PARAM_STR);
            $stmt->bindParam(':contType', $contType, PDO::PARAM_STR);
            $stmt->bindParam(':pNumber', $pNumber, PDO::PARAM_STR);
            $stmt->bindParam(':proImage', $proImage, PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $success = "Profile added successfully";
                $redirect = "manage_profile.php";
            } else {
                $errorInfo = implode(", ", $stmt->errorInfo());
                $err = "Error adding profile: $errorInfo";
                $redirect = "add_profile.php";
            }
        } else {
            $err = "Some required fields are empty.";
            $redirect = "add_profile.php";
        }
    }
    $title = "Add Profile";
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
                            <h2 class="page-title">Add New Profile</h2>
                            <div class="panel panel-default">
                                <div class="panel-heading">Add Information<span style="color: red;"> *</span></div>
                                <div class="panel-body">
                                    <form role="form" name="editApplication" method="post" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="fullName">Name<span style="color: red;"> *</span></label>
                                            <input type="text" name="name" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="dob">Date of Birth<span style="color: red;"> *</span></label>
                                            <input type="text" name="dBirth" id="datepicker-dob" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="nasti">Nationality<span style="color: red;"> *</span></label>
                                            <input type="text" name="nasti" class="form-control" required>
                                        </div>

                                        <div class="form-row">
                                            <div class="col-md-6  mb-3">
                                                <label for="contType" class="form-label">Contact Type<span style="color: red;">*</span></label>
                                                <select name="contType" id="contType" class="form-select" required>
                                                    <option value="">Select Contact Type</option>
                                                    <?php
                                                    $query = "SELECT cont_id, contType FROM `contact_type`";
                                                    $stmt = $dbh->prepare($query);
                                                    $stmt->execute();
                                                    $results = $stmt->fetchAll(PDO::FETCH_OBJ);
                                                    foreach ($results as $row) {
                                                        echo "<option value='" . htmlentities($row->cont_id) . "'>" . htmlentities($row->contType) . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3" id="passportDiv">
                                                <label for="pNumber">Contact Number<span style="color: red;"> *</span></label>
                                                <input type="tel" name="pNumber" class="form-control" required>
                                            </div>
                                        </div>
                                        &nbsp;
                                        <div class="form-group">
                                            <label for="proImage">Select a Profile Photo. (max 3MB)</label>
                                            <input type="file" name="proImage" class="form-control" accept="image/*">
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
            flatpickr("#datepicker-dob", {
                dateFormat: "Y-m-d",
                maxDate: "today",
                defaultDate: new Date().toISOString().split('T')[0],
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
        </script>
    </body>

    </html>
<?php } ?>