<?php
include('includes/config.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "error: Invalid request method!";
    exit();
}

// Assign POST values (directly, because all fields are required)
$id = $_POST['id'];
$name = $_POST['name'];
$nationality = $_POST['nationality'];
$dBirth = $_POST['dBirth'];
$phone = $_POST['phone'];
$contType = $_POST['contType'];

// Validation: Check if important fields are empty
if (empty($id) || empty($name) || empty($nationality) || empty($dBirth) || empty($phone) || empty($contType)) {
    echo "error: Missing required fields.";
    exit();
}

try {
    $proImage = null;

    // Handle file upload if provided
    if (isset($_FILES['proImage']) && $_FILES['proImage']['error'] === UPLOAD_ERR_OK) {

        // Validate new image
        $fileSize = $_FILES['proImage']['size'];
        $maxFileSize = 3 * 1024 * 1024; // 3 MB

        if ($fileSize > $maxFileSize) {
            echo "error: File size exceeds 3MB. Please upload a smaller file.";
            exit();
        }

        // Validate file type (is image)
        $check = getimagesize($_FILES["proImage"]["tmp_name"]);
        if ($check === false) {
            echo "error: Uploaded file is not a valid image.";
            exit();
        }

        // Fetch the old profile image
        $sql = "SELECT proPhoto FROM profile WHERE pro_id = :id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);

        if ($result && !empty($result->proPhoto)) {
            $filePath = "../assets/img/profile/" . $result->proPhoto;
            if (file_exists($filePath)) {
                if (!unlink($filePath)) {
                    throw new Exception("Failed to delete old profile image.");
                }
            }
        }

        $targetDir = "../assets/img/profile/";
        $imageFileType = strtolower(pathinfo($_FILES["proImage"]["name"], PATHINFO_EXTENSION));

        // Generate unique filename
        $uniqueFileName = uniqid() . '.' . $imageFileType;
        $targetFile = $targetDir . $uniqueFileName;

        // Verify the file is an actual image
        $check = getimagesize($_FILES["proImage"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["proImage"]["tmp_name"], $targetFile)) {
                $proImage = $uniqueFileName;
            } else {
                echo "error: Failed to upload the image.";
                exit();
            }
        } else {
            echo "error: File is not a valid image.";
            exit();
        }
    }

    // Prepare the UPDATE query
    if (!empty($proImage)) {
        $sql = "UPDATE profile 
                SET name = :name, dob = :dBirth, nationality = :nationality, pNumber = :phone, cont_id = :contType, proPhoto = :proImage 
                WHERE pro_id = :id";
    } else {
        $sql = "UPDATE profile 
                SET name = :name, dob = :dBirth, nationality = :nationality, pNumber = :phone, cont_id = :contType
                WHERE pro_id = :id";
    }

    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':dBirth', $dBirth, PDO::PARAM_STR);
    $stmt->bindParam(':nationality', $nationality, PDO::PARAM_STR);
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
    $stmt->bindParam(':contType', $contType, PDO::PARAM_STR);

    if (!empty($proImage)) {
        $stmt->bindParam(':proImage', $proImage, PDO::PARAM_STR);
    }

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error: Failed to update profile.";
    }
} catch (Exception $e) {
    echo "error: " . $e->getMessage();
}
