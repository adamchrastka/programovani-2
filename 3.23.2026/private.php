<?php
$uploadedImage = '';
$message = '';

// helper to convert php.ini size string (e.g. "2M") to bytes
function toBytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    $num = (int) $val;
    switch ($last) {
        case 'g': $num *= 1024;
        case 'm': $num *= 1024;
        case 'k': $num *= 1024;
    }
    return $num;
}

// determine the effective maximum upload size in bytes
$maxBytes   = min(
    toBytes(ini_get('upload_max_filesize')),
    toBytes(ini_get('post_max_size'))
);
$maxDisplay = ini_get('upload_max_filesize');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fileToUpload'])) {

    // Check if file was actually uploaded
    $error_code = $_FILES["fileToUpload"]["error"];
    if ($error_code !== UPLOAD_ERR_OK) {
        $error_messages = [
            UPLOAD_ERR_INI_SIZE   => "Error: File is too large (exceeds server limit).",
            UPLOAD_ERR_FORM_SIZE  => "Error: File exceeds form limit (MAX_FILE_SIZE).",
            UPLOAD_ERR_PARTIAL    => "Error: File was only partially uploaded.",
            UPLOAD_ERR_NO_FILE    => "Error: No file was selected.",
            UPLOAD_ERR_NO_TMP_DIR => "Error: Missing temporary folder.",
            UPLOAD_ERR_CANT_WRITE => "Error: Failed to write file to disk.",
            UPLOAD_ERR_EXTENSION  => "Error: File upload blocked by extension."
        ];
        $message = $error_messages[$error_code] ?? "Error: Unknown upload error.";
        if ($error_code === UPLOAD_ERR_INI_SIZE || $error_code === UPLOAD_ERR_FORM_SIZE) {
            $message .= " (reported size=" . ($_FILES['fileToUpload']['size'] ?? 'n/a') . ")";
        }
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

        // Check file size against server limit
        if ($uploadOk && $_FILES["fileToUpload"]["size"] > $maxBytes) {
            $message = "Sorry, your file is too large; server allows up to {$maxDisplay}.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if ($uploadOk && !in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
            $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Try to upload file
        if ($uploadOk == 1) {
            
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
    <a href="private.html">Upload another image</a>
    <a href="login.php">Odhlasit</a>
</body>
</html>