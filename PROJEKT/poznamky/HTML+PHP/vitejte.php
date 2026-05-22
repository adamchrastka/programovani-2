<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vítejte</title>
    <link rel="stylesheet" href="../CSS/styles.css">
</head>
<body>
    <div class="container">
        <div class="top-actions">
            <button type="button" onclick="window.location.href='index.php?logout=1'">Odhlásit se</button>
        </div>

        <h1>Vítejte, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <p>Toto je uživatelská stránka pro správu autoškoly.</p>
        <p>Zde můžete prohlížet své jízdy, instruktory a vozidla.</p>

        <div class="button-row" style="margin-top: 24px;">
            <button class="admin-btn" type="button" onclick="window.location.href='jizdyadmin.php'">Jízdy</button>
            <button class="admin-btn" type="button" onclick="window.location.href='instruktori.php'">Instruktoři</button>
            <button class="admin-btn" type="button" onclick="window.location.href='vozidla.php'">Vozidla</button>
        </div>

        <div style="text-align: center; margin-top: 32px;">
            <img src="../obrazek/turbokara.jpg" alt="Nejlepší auto" class="welcome-image">
        </div>
    </div>
</body>
</html>