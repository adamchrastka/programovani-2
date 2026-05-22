<?php
include 'db.php';
session_start();

if (!isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}
$isAdmin = ($_SESSION["role"] == "admin");
$error = "";
$editMode = false;
$editInstruktor = [
    "id" => "",
    "jmeno" => "",
    "prijmeni" => "",
    "tel" => "",
    "email" => ""
];

if (isset($_GET["delete"] ) && $isAdmin) {
    $id = (int)$_GET["delete"];

    $stmt = mysqli_prepare($conn, "DELETE FROM instructors WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: " . $_SERVER["PHP_SELF"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_instruktor"] ) && $isAdmin) {
    $jmeno = trim($_POST["jmeno"]);
    $prijmeni = trim($_POST["prijmeni"]);
    $tel = trim($_POST["tel"]);
    $email = trim($_POST["email"]);

    if ($jmeno != "" && $prijmeni != "" && $tel != "" && $email != "") {
        $stmt = mysqli_prepare($conn, "INSERT INTO instructors (jmeno, prijmeni, tel, email) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $jmeno, $prijmeni, $tel, $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: " . $_SERVER["PHP_SELF"]);
        exit();
    } else {
        $error = "Vypln vsechna pole.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_instruktor"] ) && $isAdmin) {
    $id = (int)$_POST["id"];
    $jmeno = trim($_POST["jmeno"]);
    $prijmeni = trim($_POST["prijmeni"]);
    $tel = trim($_POST["tel"]);
    $email = trim($_POST["email"]);

    if ($jmeno != "" && $prijmeni != "" && $tel != "" && $email != "") {
        $stmt = mysqli_prepare($conn, "UPDATE instructors SET jmeno = ?, prijmeni = ?, tel = ?, email = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssssi", $jmeno, $prijmeni, $tel, $email, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: " . $_SERVER["PHP_SELF"]);
        exit();
    } else {
        $error = "Vypln vsechna pole pro upravu.";
    }
}

if (isset($_GET["edit"])) {
    $id = (int)$_GET["edit"];

    $stmt = mysqli_prepare($conn, "SELECT id, jmeno, prijmeni, tel, email FROM instructors WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    mysqli_stmt_bind_result($stmt, $iid, $ijmeno, $iprijmeni, $itel, $iemail);

    if (mysqli_stmt_fetch($stmt)) {
        $editMode = true;
        $editInstruktor = [
            "id" => $iid,
            "jmeno" => $ijmeno,
            "prijmeni" => $iprijmeni,
            "tel" => $itel,
            "email" => $iemail
        ];
    }

    mysqli_stmt_close($stmt);
}

$sql = "SELECT id, jmeno, prijmeni, tel, email FROM instructors ORDER BY id ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seznam instruktorů</title>
    <link rel="stylesheet" href="../CSS/styles.css">
</head>
<body>
    <div class="container">
        <h1>Seznam instruktorů</h1>

        <table border="1" cellpadding="8">
            <tr>
                <th>ID</th>
                <th>Jméno</th>
                <th>Příjmení</th>
                <th>Telefon</th>
                <th>Email</th>
                <?php if ($isAdmin) { ?>
                    <th>Upravit</th>
                    <th>Smazat</th>
                <?php } ?>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row["id"]); ?></td>
                    <td><?php echo htmlspecialchars($row["jmeno"]); ?></td>
                    <td><?php echo htmlspecialchars($row["prijmeni"]); ?></td>
                    <td><?php echo htmlspecialchars($row["tel"]); ?></td>
                    <td><?php echo htmlspecialchars($row["email"]); ?></td>
                    <?php if ($isAdmin) { ?>
                        <td>
                            <a href="instruktori.php?edit=<?php echo $row["id"]; ?>">Upravit</a>
                        </td>
                        <td>
                            <a href="instruktori.php?delete=<?php echo $row["id"]; ?>" onclick="return confirm('Opravdu chcete smazat tohoto instruktora?');">Smazat</a>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </table>
        <br>
        <?php if ($error != "") { ?>
            <p><?php echo $error; ?></p>
        <?php } ?>

        <?php if ($isAdmin) { ?>
            <h2><?php echo $editMode ? "Upravit instruktora" : "Přidat nového instruktora"; ?></h2>
            <form method="post" action="">
                <?php if ($editMode) { ?>
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($editInstruktor["id"]); ?>">
                <?php } ?>
                <input type="text" name="jmeno" placeholder="Jméno" value="<?php echo htmlspecialchars($editInstruktor["jmeno"]); ?>">
                <input type="text" name="prijmeni" placeholder="Příjmení" value="<?php echo htmlspecialchars($editInstruktor["prijmeni"]); ?>">
                <input type="text" name="tel" placeholder="Telefon" value="<?php echo htmlspecialchars($editInstruktor["tel"]); ?>">
                <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($editInstruktor["email"]); ?>">
                <?php if ($editMode) { ?>
                    <button type="submit" name="update_instruktor">Uložit změny</button>
                    <button type="button" onclick="window.location.href='instruktori.php'">Zrušit úpravu</button>
                <?php } else { ?>
                    <button type="submit" name="add_instruktor">Přidat instruktora</button>
                <?php } ?>
            </form>
        <?php } ?>
        <button type="button" onclick="window.location.href='admin.php'">Zpět na hl. stránku</button>
    </div>
</body>
</html>