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
        $passport = $_POST['passport'];
        $dIssue = $_POST['dIssue'];
        $dExpiry = $_POST['dExpiry'];
        $pNumber = $_POST['pNumber'];
        $status = 'active';

        $proImage = null;

        if (isset($_FILES['proImage']) && $_FILES['proImage']['error'] === UPLOAD_ERR_OK) {
            $fileSize = $_FILES['proImage']['size'];
            $maxFileSize = 3 * 1024 * 1024; // 3 MB in bytes

            if ($fileSize > $maxFileSize) {
                echo "<script>
                        alert('File size exceeds 3MB. Please upload a smaller file.');
                        window.location.href = 'add_record.php';
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
                    $redirect = "add_record.php";
                }
            } else {
                $err = "Error: File is not an image.";
                $redirect = "add_record.php";
            }
        }

        $passimage = null;

        if (isset($_FILES['passimage']) && $_FILES['passimage']['error'] === UPLOAD_ERR_OK) {
            $fileSize = $_FILES['passimage']['size'];
            $maxFileSize = 5 * 1024 * 1024; // 5 MB

            if ($fileSize > $maxFileSize) {
                echo "<script>
                alert('File size exceeds 5MB. Please upload a smaller file.');
                window.location.href = 'add_record.php';
              </script>";
                exit();
            }

            $targetDir = "../assets/img/passport/";
            $fileType = strtolower(pathinfo($_FILES["passimage"]["name"], PATHINFO_EXTENSION));

            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'];

            if (!in_array($fileType, $allowedTypes)) {
                echo "<script>
                alert('Invalid file type. Only JPG, PNG, GIF, WEBP, and PDF are allowed.');
                window.location.href = 'add_record.php';
              </script>";
                exit();
            }

            $uniqueFileName = uniqid() . '.' . $fileType;
            $targetFile = $targetDir . $uniqueFileName;

            $isValid = true;

            if ($fileType !== 'pdf') {
                $check = getimagesize($_FILES["passimage"]["tmp_name"]);
                if ($check === false) {
                    $isValid = false;
                }
            }

            if ($isValid) {
                if (move_uploaded_file($_FILES["passimage"]["tmp_name"], $targetFile)) {
                    $passimage = $uniqueFileName; // Store only filename in DB
                } else {
                    $err = "Error: Failed to upload the file.";
                    $redirect = "add_record.php";
                }
            } else {
                $err = "Error: File is not a valid image.";
                $redirect = "add_record.php";
            }
        }

        if (!empty($name) && !empty($dBirth) && !empty($nasti) && !empty($pNumber) && !empty($passport) && !empty($dIssue) && !empty($dExpiry)) {
            $sql = "INSERT INTO profile (name, dob, nationality, pNumber, proPhoto, status) VALUES (:name, :dBirth, :nasti, :pNumber, :proImage, :status)";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':dBirth', $dBirth, PDO::PARAM_STR);
            $stmt->bindParam(':nasti', $nasti, PDO::PARAM_STR);
            $stmt->bindParam(':pNumber', $pNumber, PDO::PARAM_STR);
            $stmt->bindParam(':proImage', $proImage, PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $pro_id = $dbh->lastInsertId();

                $sql = "INSERT INTO pass_info (pro_id, passNo, doi, doe, passPhoto) VALUES (:pro_id, :passport, :dIssue, :dExpiry, :passimage)";
                $stmt = $dbh->prepare($sql);
                $stmt->bindParam(':pro_id', $pro_id, PDO::PARAM_STR);
                $stmt->bindParam(':passport', $passport, PDO::PARAM_STR);
                $stmt->bindParam(':dIssue', $dIssue, PDO::PARAM_STR);
                $stmt->bindParam(':dExpiry', $dExpiry, PDO::PARAM_STR);
                $stmt->bindParam(':passimage', $passimage, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    $success = "Record added successfully";
                    $redirect = "manage_record.php";
                } else {
                    $errorInfo = implode(", ", $stmt->errorInfo());
                    $err = "Error adding application: $errorInfo";
                    $redirect = "add_record.php";
                }
            } else {
                $errorInfo = implode(", ", $stmt->errorInfo());
                $err = "Error adding application: $errorInfo";
                $redirect = "add_record.php";
            }
        } else {
            $err = "Some required fields are empty.";
            $redirect = "add_record.php";
        }
    }
    $title = "Add Passport Record";
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
                            <h2 class="page-title">Add Passport Record</h2>
                            <div class="panel panel-default">
                                <div class="panel-heading">Add Information</div>
                                <div class="panel-body">
                                    <form role="form" name="editApplication" method="post" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="fullName">Name</label>
                                            <input type="text" name="name" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="dob">Date of Birth</label>
                                            <input type="text" name="dBirth" id="datepicker-dob" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="nasti">Nationality</label>
                                            <input type="text" name="nasti" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="passport">Passport No.</label>
                                            <input type="text" name="passport" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="dIssue">Date of Issue</label>
                                            <input type="text" name="dIssue" id="datepicker-issue" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="dExpiry">Date of Expiry</label>
                                            <input type="text" name="dExpiry" id="datepicker-expiry" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="pNumber">Phone Number</label>
                                            <input type="tel" name="pNumber" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="proImage">Select a Profile Photo. (max 3MB)</label>
                                            <input type="file" name="proImage" class="form-control" accept="image/*">
                                        </div>
                                        &nbsp;
                                        <div class="form-group">
                                            <label for="passimage">Select a Passport Photo (max 5MB)</label>
                                            <input type="file" name="passimage" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf">
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