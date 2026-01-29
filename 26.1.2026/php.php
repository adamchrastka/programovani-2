<?php
$uploadedImage = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fileToUpload'])) {
    // Check if file was actually uploaded
    $error_code = $_FILES["fileToUpload"]["error"];
    if ($error_code !== UPLOAD_ERR_OK) {
        $error_messages = [
            1 => "Error: File is too large. Maximum size is 100MB.",
            2 => "Error: File exceeds the HTML form limit.",
            3 => "Error: File was only partially uploaded.",
            4 => "Error: No file was selected.",
            6 => "Error: Missing temporary folder.",
            7 => "Error: Failed to write file to disk.",
            8 => "Error: File upload blocked by extension."
        ];
        $message = isset($error_messages[$error_code]) ? $error_messages[$error_code] : "Error: Unknown upload error.";
    } else {
        $target_dir = "uploads/";
        
        // Create uploads directory if it doesn't exist
        if (!is_dir($target_dir)) {
            if (!mkdir($target_dir, 0755, true)) {
                $message = "Error: Could not create uploads directory.";
            }
        }
        
        $filename = basename($_FILES["fileToUpload"]["name"]);
        $target_file = $target_dir . $filename;
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image
        if (file_exists($_FILES["fileToUpload"]["tmp_name"])) {
            $check = @getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if ($check === false) {
                $message = "File is not a valid image.";
                $uploadOk = 0;
            }
        } else {
            $message = "Error: Temporary file does not exist.";
            $uploadOk = 0;
        }

        // Check file size (500KB limit)
        if ($uploadOk && $_FILES["fileToUpload"]["size"] > 500000) {
            $message = "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if ($uploadOk && !in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
            $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Try to upload file
        if ($uploadOk == 1) {
            // Debug info
            $message .= "<br>DEBUG: tmp_name = " . $_FILES["fileToUpload"]["tmp_name"];
            $message .= "<br>DEBUG: target_file = " . $target_file;
            $message .= "<br>DEBUG: target_dir = " . $target_dir;
            $message .= "<br>DEBUG: dir exists = " . (is_dir($target_dir) ? "yes" : "no");
            $message .= "<br>DEBUG: dir writable = " . (is_writable($target_dir) ? "yes" : "no");
            
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                $uploadedImage = $target_file;
                $message = "Image uploaded successfully!<br>Path: " . htmlspecialchars($target_file);
            } else {
                $message = "Error: Could not move uploaded file. Check directory permissions.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Response</title>
</head>
<body>
    <h1>Image Upload Result</h1>
    <p><?php echo $message; ?></p>
    
    <?php if ($uploadedImage): ?>
        <div style="margin-top: 20px;">
            <h2>Uploaded Image:</h2>
            <img src="<?php echo htmlspecialchars($uploadedImage); ?>" alt="Uploaded Image" style="max-width: 500px; height: auto;">
        </div>
    <?php endif; ?>
    
    <br>
    <a href="index.html">Upload another image</a>
</body>
</html>