<?php
include('includes/config.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "error: Invalid request method!";
    exit();
}

// Assign POST values (directly, because all fields are required)
$id = $_POST['id'];
$pro_id = $_POST['pro_id'];
$passport = $_POST['passport'];
$dIssue = $_POST['dIssue'];
$dExpiry = $_POST['dExpiry'];
$oldPassImage = $_POST['oldPassImage'];

// Validation: Check if important fields are empty
if (empty($pro_id) || empty($passport) || empty($dIssue) || empty($dExpiry)) {
    echo "error: Missing required fields.";
    exit();
}

try {
    $passimage = null;

    // Handle file upload if provided
    if (isset($_FILES['passimage']) && $_FILES['passimage']['error'] === UPLOAD_ERR_OK) {

        $fileSize = $_FILES['passimage']['size'];
        $maxFileSize = 5 * 1024 * 1024; // 5 MB

        if ($fileSize > $maxFileSize) {
            echo "error: File size exceeds 5MB. Please upload a smaller file.";
            exit();
        }

        $fileType = strtolower(pathinfo($_FILES["passimage"]["name"], PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'];

        if (!in_array($fileType, $allowedTypes)) {
            echo "error: Invalid file type. Only JPG, PNG, GIF, WEBP, and PDF are allowed.";
            exit();
        }

        $isValid = true;
        if ($fileType !== 'pdf') {
            $check = getimagesize($_FILES["passimage"]["tmp_name"]);
            if ($check === false) {
                $isValid = false;
            }
        }

        if ($isValid) {
            $targetDir = "../assets/img/passport/";
            $uniqueFileName = uniqid() . '.' . $fileType;
            $targetFile = $targetDir . $uniqueFileName;

            if (move_uploaded_file($_FILES["passimage"]["tmp_name"], $targetFile)) {
                // Only after successful upload, delete old file
                $filePath = $targetDir . $oldPassImage;
                if (!empty($passimage) && file_exists($filePath) && is_file($filePath)) {
                    unlink($filePath);
                }
                $passImage = $uniqueFileName;
            } else {
                echo "error: Failed to upload the new image.";
                exit();
            }
        } else {
            echo "error: Uploaded file is not a valid image.";
            exit();
        }
    }

    // Prepare the UPDATE query
    if (!empty($passImage)) {
        $sql = "UPDATE pass_info 
            SET pro_id = :pro_id, passNo = :passNo, doi = :doi, doe = :doe, passPhoto = :passPhoto
            WHERE pass_id = :id";
    } else {
        $sql = "UPDATE pass_info 
            SET pro_id = :pro_id, passNo = :passNo, doi = :doi, doe = :doe
            WHERE pass_id = :id";
    }

    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':pro_id', $pro_id, PDO::PARAM_STR);
    $stmt->bindParam(':passNo', $passport, PDO::PARAM_STR);
    $stmt->bindParam(':doi', $dIssue, PDO::PARAM_STR);
    $stmt->bindParam(':doe', $dExpiry, PDO::PARAM_STR);

    if (!empty($passImage)) {
        $stmt->bindParam(':passPhoto', $passImage, PDO::PARAM_STR);
    }


    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error: Failed to update Passport Record.";
    }
} catch (Exception $e) {
    echo "error: " . $e->getMessage();
}
