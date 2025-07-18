<?php
include('includes/config.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "error: Invalid request method!";
    exit();
}

// Assign POST values
$id = $_POST['id'];
$visaNO = $_POST['visa'];
$visaYear = $_POST['visaYear'];
$vCatagory = $_POST['vCatagory'];
$dIssue = $_POST['dIssue'];
$dExpiry = $_POST['dExpiry'];
$oldVisaImage = $_POST['oldVisaImage'];

// Validation
if (empty($id) || empty($visaNO) || empty($visaYear) || empty($vCatagory) || empty($dIssue) || empty($dExpiry)) {
    echo "error: Missing required fields.";
    exit();
}

try {
    $visaImage = $oldVisaImage; // Default: keep old image

    // Handle file upload if provided
    if (isset($_FILES['visaImage']) && $_FILES['visaImage']['error'] === UPLOAD_ERR_OK) {

        $fileSize = $_FILES['visaImage']['size'];
        $maxFileSize = 5 * 1024 * 1024; // 5 MB

        if ($fileSize > $maxFileSize) {
            echo "error: File size exceeds 5MB. Please upload a smaller file.";
            exit();
        }

        $fileType = strtolower(pathinfo($_FILES["visaImage"]["name"], PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'];

        if (!in_array($fileType, $allowedTypes)) {
            echo "error: Invalid file type. Only JPG, PNG, GIF, WEBP, and PDF are allowed.";
            exit();
        }

        $isValid = true;
        if ($fileType !== 'pdf') {
            $check = getimagesize($_FILES["visaImage"]["tmp_name"]);
            if ($check === false) {
                $isValid = false;
            }
        }

        if ($isValid) {
            $targetDir = "../assets/img/visa/";
            $uniqueFileName = uniqid() . '.' . $fileType;
            $targetFile = $targetDir . $uniqueFileName;

            if (move_uploaded_file($_FILES["visaImage"]["tmp_name"], $targetFile)) {
                // Only after successful upload, delete old file
                $filePath = $targetDir . $oldVisaImage;
                if (!empty($oldVisaImage) && file_exists($filePath) && is_file($filePath)) {
                    unlink($filePath);
                }
                $visaImage = $uniqueFileName;
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
    $sql = "UPDATE visa_info 
            SET visaNo = :visaNO, visaYear = :visaYear, visaCate = :vCatagory, vDoi = :dIssue, vDoe = :dExpiry, visaImage = :visaImage 
            WHERE visa_id = :id";

    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':visaNO', $visaNO, PDO::PARAM_STR);
    $stmt->bindParam(':visaYear', $visaYear, PDO::PARAM_STR);
    $stmt->bindParam(':vCatagory', $vCatagory, PDO::PARAM_STR);
    $stmt->bindParam(':dIssue', $dIssue, PDO::PARAM_STR);
    $stmt->bindParam(':dExpiry', $dExpiry, PDO::PARAM_STR);
    $stmt->bindParam(':visaImage', $visaImage, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error: Failed to update Visa Record.";
    }
} catch (Exception $e) {
    echo "error: " . $e->getMessage();
}
