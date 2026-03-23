<?php
session_start();
$content = $_POST['content'] ?? '';
header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="notes.txt"');
echo $content;
exit;
?>