<?php 
    echo "Ahoj";
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/styles.css">
</head>
<body>
    <button type="button" onclick="window.location.href='index.php?logout=1'">Odhlásit se</button>
    <h1>Vítejte, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p>Toto je uživatelská stránka pro správu autoškoly.</p>
    <p>Zde můžete prohlížet své jízdy, instruktory, vozidla.</p>
    <center>
        <button class="admin-btn" type="button" onclick="window.location.href='jizdyadmin.php'">Jízdy</button>
        <button class="admin-btn" type="button" onclick="window.location.href='instruktori.php'">Instruktoři</button>
        <button class="admin-btn" type="button" onclick="window.location.href='vozidla.php'">Vozidla</button>
    </center>
    <style>
        .welcome-image {
            max-width: 55%;
            height: auto;
            margin-top: 20px;
}
    </style>
    <center>
        <img src="../obrazek/turbokara.jpg" alt="Nejlepsi auto" class="welcome-image">
    </center>
</body>
</html>