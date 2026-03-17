<?php
session_start();

$content = $_SESSION['notes_content'] ?? '';

if (isset($_POST['load']) && isset($_FILES['file'])) {
    $file = $_FILES['file']['tmp_name'];
    if (is_uploaded_file($file) && pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION) === 'txt') {
        $content = file_get_contents($file);
        $_SESSION['notes_content'] = $content;
        $message = "File loaded successfully!";
    } else {
        $message = "Failed to load file. Please select a valid .txt file.";
    }
}

if (isset($_POST['save'])) {
    $content = $_POST['content'] ?? '';
    $_SESSION['notes_content'] = $content;
    file_put_contents('notes.txt', $content);
    $message = "Notes saved!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes Editor</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        textarea { width: 100%; height: 400px; padding: 10px; font-family: monospace; }
        button { margin: 10px 5px; padding: 10px 20px; }
    </style>
</head>
<body>
    <h1>Notes Editor</h1>
    <?php if (isset($message)) echo "<p>$message</p>"; ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="file" accept=".txt">
        <button type="submit" name="load">Load File</button>
    </form>
    <form method="POST">
        <textarea name="content"><?php echo htmlspecialchars($content); ?></textarea>
        <br>
        <button type="submit" name="save">Save</button>
    </form>
    <button type="button" onclick="downloadNotes()">Download</button>
    <script>
        function downloadNotes() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '';

            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }
    </script>
</body>
</html>

