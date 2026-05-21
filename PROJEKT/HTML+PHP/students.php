<?php
include 'db.php';
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}
$error = "";
$editMode = false;
$editStudent = [
    "id" => "",
    "jmeno" => "",
    "prijmeni" => "",
    "birthdate" => "",
    "tel" => "",
    "email" => ""
];

// SMAZANI STUDENTA
if (isset($_GET["delete"])) {
    $id = (int)$_GET["delete"];

    $stmt = mysqli_prepare($conn, "DELETE FROM students WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: students.php");
    exit();
}

// PRIDANI NOVEHO STUDENTA
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_student"])) {
    $jmeno = trim($_POST["jmeno"]);
    $prijmeni = trim($_POST["prijmeni"]);
    $birthdate = $_POST["birthdate"];
    $tel = trim($_POST["tel"]);
    $email = trim($_POST["email"]);

    if ($jmeno != "" && $prijmeni != "" && $birthdate != "" && $tel != "" && $email != "") {
        $stmt = mysqli_prepare($conn, "INSERT INTO students (jmeno, prijmeni, birthdate, tel, email) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssss", $jmeno, $prijmeni, $birthdate, $tel, $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: students.php");
        exit();
    } else {
        $error = "Vypln vsechna pole.";
    }
}

// UPRAVA STUDENTA
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_student"])) {
    $id = (int)$_POST["id"];
    $jmeno = trim($_POST["jmeno"]);
    $prijmeni = trim($_POST["prijmeni"]);
    $birthdate = $_POST["birthdate"];
    $tel = trim($_POST["tel"]);
    $email = trim($_POST["email"]);

    if ($jmeno != "" && $prijmeni != "" && $birthdate != "" && $tel != "" && $email != "") {
        $stmt = mysqli_prepare($conn, "UPDATE students SET jmeno = ?, prijmeni = ?, birthdate = ?, tel = ?, email = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "sssssi", $jmeno, $prijmeni, $birthdate, $tel, $email, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: students.php");
        exit();
    } else {
        $error = "Vypln vsechna pole pro upravu.";
    }
}

// NACITANI STUDENTA PRO UPRAVU
if (isset($_GET["edit"])) {
    $id = (int)$_GET["edit"];
    $stmt = mysqli_prepare($conn, "SELECT id, jmeno, prijmeni, birthdate, tel, email FROM students WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    mysqli_stmt_bind_result($stmt, $sid, $sjmeno, $sprijmeni, $sbirthdate, $stel, $semail);

    if (mysqli_stmt_fetch($stmt)) {
        $editMode = true;
        $editStudent = [
            "id" => $sid,
            "jmeno" => $sjmeno,
            "prijmeni" => $sprijmeni,
            "birthdate" => $sbirthdate,
            "tel" => $stel,
            "email" => $semail
        ];
    }

    mysqli_stmt_close($stmt);
}

// NACTENI STUDENTU
$sql = "SELECT id, jmeno, prijmeni, birthdate, tel, email FROM students ORDER BY id ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seznam studentu</title>
    <link rel="stylesheet" href="../CSS/styles.css">
</head>
<body>
    <div class="container">
        <h1>Seznam studentů</h1>

        <table border="1" cellpadding="8">
            <tr>
                <th>ID</th>
                <th>Jméno</th>
                <th>říjmení</th>
                <th>Datum narození</th>
                <th>Telefon</th>
                <th>Email</th>
                <th>Upravit</th>
                <th>Smazat</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row["id"]); ?></td>
                    <td><?php echo htmlspecialchars($row["jmeno"]); ?></td>
                    <td><?php echo htmlspecialchars($row["prijmeni"]); ?></td>
                    <td><?php echo date("d.m.Y", strtotime($row["birthdate"])); ?></td>
                    <td><?php echo htmlspecialchars($row["tel"]); ?></td>
                    <td><?php echo htmlspecialchars($row["email"]); ?></td>
                    <td>
                        <a href="students.php?edit=<?php echo $row["id"]; ?>">Upravit</a>
                    </td>
                    <td>
                        <a href="students.php?delete=<?php echo $row["id"]; ?>" onclick="return confirm('Opravdu chcete smazat tohoto studenta?');">Smazat</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <?php if ($error != "") { ?>
            <p class="error"><?php echo $error; ?></p>
        <?php } ?>

        <?php if (!$editMode) { ?>
            <h2>Pridat noveho studenta</h2>
            <form method="post" action="">
                <input type="text" name="jmeno" placeholder="Jmeno">
                <input type="text" name="prijmeni" placeholder="Prijmeni">
                <input type="date" name="birthdate">
                <input type="text" name="tel" placeholder="Telefon">
                <input type="email" name="email" placeholder="Email">
                <button type="submit" name="add_student">Přidat studenta</button>
            </form>
        <?php } else { ?>
            <h2>Upravit studenta</h2>
            <form method="post" action="">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($editStudent["id"]); ?>">

                <input type="text" name="jmeno" placeholder="Jmeno" value="<?php echo htmlspecialchars($editStudent["jmeno"]); ?>">
                <input type="text" name="prijmeni" placeholder="Prijmeni" value="<?php echo htmlspecialchars($editStudent["prijmeni"]); ?>">
                <input type="date" name="birthdate" value="<?php echo htmlspecialchars($editStudent["birthdate"]); ?>">
                <input type="text" name="tel" placeholder="Telefon" value="<?php echo htmlspecialchars($editStudent["tel"]); ?>">
                <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($editStudent["email"]); ?>">

                <button type="submit" name="update_student">Uložit změny</button>
                   <div class="button-row button-column">
                       <button type="button" class="button1" onclick="window.location.href='students.php'">Zrušit úpravu</button>
                   </div>
            </form>

        <?php } ?>
    </div>

    <br>
    <button onclick="window.location.href='admin.php'">Zpět na admin</button>
</body>
</html>