<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if (isset($_POST['submit'])) {
        $proId = !empty($id) ? $id : $_POST['pro_id'];
        $passport = $_POST['passport'];
        $dIssue = $_POST['dIssue'];
        $dExpiry = $_POST['dExpiry'];

        $passimage = null;

        if (isset($_FILES['passimage']) && $_FILES['passimage']['error'] === UPLOAD_ERR_OK) {
            $fileSize = $_FILES['passimage']['size'];
            $maxFileSize = 5 * 1024 * 1024; // 5 MB

            if ($fileSize > $maxFileSize) {
                echo "<script>
                alert('File size exceeds 5MB. Please upload a smaller file.');
                window.location.href = 'add_passport.php';
              </script>";
                exit();
            }

            $targetDir = "../assets/img/passport/";
            $fileType = strtolower(pathinfo($_FILES["passimage"]["name"], PATHINFO_EXTENSION));

            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'];

            if (!in_array($fileType, $allowedTypes)) {
                echo "<script>
                alert('Invalid file type. Only JPG, PNG, GIF, WEBP, and PDF are allowed.');
                window.location.href = 'add_passport.php';
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
                    $redirect = "add_passport.php";
                }
            } else {
                $err = "Error: File is not a valid image.";
                $redirect = "add_passport.php";
            }
        }

        $sql = "INSERT INTO pass_info (pro_id, passNo, doi, doe, passPhoto) VALUES (:pro_id, :passport, :dIssue, :dExpiry, :passimage)";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':pro_id', $proId, PDO::PARAM_STR);
        $stmt->bindParam(':passport', $passport, PDO::PARAM_STR);
        $stmt->bindParam(':dIssue', $dIssue, PDO::PARAM_STR);
        $stmt->bindParam(':dExpiry', $dExpiry, PDO::PARAM_STR);
        $stmt->bindParam(':passimage', $passimage, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $success = "Passport Record added successfully";
            $redirect = "managePassRec.php";
        } else {
            $errorInfo = implode(", ", $stmt->errorInfo());
            $err = "Error adding Record: $errorInfo";
            $redirect = "add_passport.php";
        }
    }

    $title = "Add New Passport";
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
                                            <label for="profile">Name</label>
                                            <select name="pro_id" id="profile" class="form-select" <?php echo !empty($id) ? 'disabled' : 'required'; ?>>
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
                                                    // Check if the current profile is the selected one
                                                    $selected = (!empty($id) && $id == $row->pro_id) ? 'selected' : '';
                                                    echo "<option value='" . htmlentities($row->pro_id) . "' $selected>" .
                                                        htmlentities("{$row->name} ({$row->pNumber} - {$row->contType})") .
                                                        "</option>";
                                                }
                                                ?>
                                            </select>
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
                defaultDate: new Date().toISOString().split('T')[0],
                disableMobile: true
            };

            flatpickr("#datepicker-issue", commonPickerOptions);
            flatpickr("#datepicker-expiry", commonPickerOptions);
        </script>
    </body>

    </html>
<?php } ?>