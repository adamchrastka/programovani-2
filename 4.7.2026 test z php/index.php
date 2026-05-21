<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jmeno = $_POST["jmeno"];
    $heslo = $_POST["heslo"];
    
    $pdo = new PDO("mysql:host=localhost;dbname=test", "root", "");
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE jmeno = ?");
    $stmt->execute([$jmeno]);
    $user = $stmt->fetch();
    if ($user && password_verify($heslo, $user["heslo"])) {
        $_SESSION["jmeno"] = $user["jmeno"];
        header("Location: vitejte.php");
        exit();
    } else {
        echo "Neplatne jmeno nebo heslo.";
    }
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="POST"> 
    <a>Jmeno</a><input type="text" name="jmeno" placeholder="Jmeno">
    <a>Heslo</a><input type="password" name="heslo" placeholder="Heslo">
    <button type="submit">Prihlasit</button>
</form>
</body>
</html>