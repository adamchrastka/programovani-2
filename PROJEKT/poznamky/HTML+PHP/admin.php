<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

$studentsCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM students"))["total"];

$instructorsCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM instructors"))["total"];

$vozidlaCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM vozidla"))["total"];

$jizdyCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM jizdy"))["total"];
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin panel</title>
    <link rel="stylesheet" type="text/css" href="../CSS/styles.css">
</head>
<body>

<div class="container">

    <h1>Administrátorský panel</h1>

    <p>
        Vítejte <?php echo htmlspecialchars($_SESSION['username']); ?>,
        jste přihlášeni jako administrátor.
    </p>

    <div class="button-row">

        <button class="admin-btn" type="button" onclick="window.location.href='uzivatele.php'">
            Správa uživatelů
        </button>

        <button class="admin-btn" type="button" onclick="window.location.href='vozidla.php'">
            Správa vozidel
        </button>

        <button class="admin-btn" type="button" onclick="window.location.href='students.php'">
            Správa studentů
        </button>

        <button class="admin-btn" type="button" onclick="window.location.href='jizdyadmin.php'">
            Správa jízd
        </button>

        <button class="admin-btn" type="button" onclick="window.location.href='instruktori.php'">
            Správa instruktorů
        </button>

    </div>
    <table id="tabulka">

        <tr>
            <th>Sekce</th>
            <th>Počet</th>
        </tr>

        <tr>
            <td>Studenti</td>
            <td><?php echo $studentsCount; ?></td>
        </tr>

        <tr>
            <td>Instruktoři</td>
            <td><?php echo $instructorsCount; ?></td>
        </tr>

        <tr>
            <td>Vozidla</td>
            <td><?php echo $vozidlaCount; ?></td>
        </tr>

        <tr>
            <td>Jízdy</td>
            <td><?php echo $jizdyCount; ?></td>
        </tr>

    </table>

    <div class="button-row">
        <a href="index.php?logout=1">
            <button class="odhlasit-btn" type="button">Odhlásit se</button>
        </a>
    </div>

</div>

</body>
</html>