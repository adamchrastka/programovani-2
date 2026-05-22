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

$editVozidlo = [
    "id" => "",
    "znacka" => "",
    "model" => "",
    "spz" => "",
    "rok_vyroby" => "",
    "barva" => "",
    "palivo" => "",
    "prevodovka" => ""
];

if (isset($_GET["delete"]) && $isAdmin) {

    $id = (int)$_GET["delete"];

    $stmt = mysqli_prepare($conn, "DELETE FROM vozidla WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: vozidla.php");
    exit();
}



if (isset($_GET["edit"]) && $isAdmin) {

    $id = (int)$_GET["edit"];

    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, znacka, model, spz, rok_vyroby, barva, palivo, prevodovka
         FROM vozidla
         WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    mysqli_stmt_bind_result(
        $stmt,
        $vid,
        $vznacka,
        $vmodel,
        $vspz,
        $vrok_vyroby,
        $vbarva,
        $vpalivo,
        $vprevodovka
    );

    if (mysqli_stmt_fetch($stmt)) {
        $editMode = true;

        $editVozidlo = [
            "id" => $vid,
            "znacka" => $vznacka,
            "model" => $vmodel,
            "spz" => $vspz,
            "rok_vyroby" => $vrok_vyroby,
            "barva" => $vbarva,
            "palivo" => $vpalivo,
            "prevodovka" => $vprevodovka
        ];
    }

    mysqli_stmt_close($stmt);
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && $isAdmin) {

    $znacka = trim($_POST["znacka"]);
    $model = trim($_POST["model"]);
    $spz = trim($_POST["spz"]);
    $rok_vyroby = (int)$_POST["rok_vyroby"];
    $barva = trim($_POST["barva"]);
    $palivo = $_POST["palivo"];
    $prevodovka = $_POST["prevodovka"];

    if (
        $znacka != "" &&
        $model != "" &&
        $spz != "" &&
        $rok_vyroby > 0 &&
        $barva != "" &&
        $palivo != "" &&
        $prevodovka != ""
    ) {

        if (isset($_POST["update_vozidlo"])) {

            $id = (int)$_POST["id"];

            $stmt = mysqli_prepare(
                $conn,
                "UPDATE vozidla
                 SET znacka = ?, model = ?, spz = ?, rok_vyroby = ?, barva = ?, palivo = ?, prevodovka = ?
                 WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "sssisssi",
                $znacka,
                $model,
                $spz,
                $rok_vyroby,
                $barva,
                $palivo,
                $prevodovka,
                $id
            );

        } else {

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO vozidla
                 (znacka, model, spz, rok_vyroby, barva, palivo, prevodovka)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "sssisss",
                $znacka,
                $model,
                $spz,
                $rok_vyroby,
                $barva,
                $palivo,
                $prevodovka
            );
        }

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: vozidla.php");
        exit();

    } else {
        $error = "Vypln vsechna pole.";
    }
}


$result = mysqli_query(
    $conn,
    "SELECT id, znacka, model, spz, rok_vyroby, barva, palivo, prevodovka
     FROM vozidla
     ORDER BY id ASC"
);
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seznam vozidel</title>
    <link rel="stylesheet" href="../CSS/styles.css">
</head>

<body>

<div class="container">

<h1>Seznam vozidel</h1>

<table border="1" cellpadding="8">

<tr>
    <th>ID</th>
    <th>Značka</th>
    <th>Model</th>
    <th>SPZ</th>
    <th>Rok</th>
    <th>Barva</th>
    <th>Palivo</th>
    <th>Převodovka</th>

    <?php if ($isAdmin) { ?>
        <th>Upravit</th>
        <th>Smazat</th>
    <?php } ?>
</tr>


<?php while ($row = mysqli_fetch_assoc($result)) { ?>

<tr>

    <td><?php echo htmlspecialchars($row["id"]); ?></td>
    <td><?php echo htmlspecialchars($row["znacka"]); ?></td>
    <td><?php echo htmlspecialchars($row["model"]); ?></td>
    <td><?php echo htmlspecialchars($row["spz"]); ?></td>
    <td><?php echo htmlspecialchars($row["rok_vyroby"]); ?></td>
    <td><?php echo htmlspecialchars($row["barva"]); ?></td>
    <td><?php echo htmlspecialchars($row["palivo"]); ?></td>
    <td><?php echo htmlspecialchars($row["prevodovka"]); ?></td>

    <?php if ($isAdmin) { ?>

        <td>
            <a href="vozidla.php?edit=<?php echo $row["id"]; ?>">
                Upravit
            </a>
        </td>

        <td>
            <a href="vozidla.php?delete=<?php echo $row["id"]; ?>"
               onclick="return confirm('Opravdu smazat?');">
                Smazat
            </a>
        </td>

    <?php } ?>

</tr>

<?php } ?>

</table>
<br>

<?php if ($error != "") { ?>
    <p><?php echo htmlspecialchars($error); ?></p>
<?php } ?>


<?php if ($isAdmin) { ?>

<h2><?php echo $editMode ? "Upravit vozidlo" : "Přidat vozidlo"; ?></h2>

<form method="post">

    <?php if ($editMode) { ?>
        <input type="hidden" name="id" value="<?php echo $editVozidlo["id"]; ?>">
    <?php } ?>

    <input type="text" name="znacka" placeholder="Znacka"
        value="<?php echo $editVozidlo["znacka"]; ?>">

    <input type="text" name="model" placeholder="Model"
        value="<?php echo $editVozidlo["model"]; ?>">

    <input type="text" name="spz" placeholder="SPZ"
        value="<?php echo $editVozidlo["spz"]; ?>">

    <input type="number" name="rok_vyroby" placeholder="Rok"
        value="<?php echo $editVozidlo["rok_vyroby"]; ?>">

    <input type="text" name="barva" placeholder="Barva"
        value="<?php echo $editVozidlo["barva"]; ?>">

    <select name="palivo">
        <option value="">Palivo</option>
        <option value="benzin" <?php if ($editVozidlo["palivo"]=="benzin") echo "selected"; ?>>Benzin</option>
        <option value="nafta" <?php if ($editVozidlo["palivo"]=="nafta") echo "selected"; ?>>Nafta</option>
    </select>

    <select name="prevodovka">
        <option value="">Převodovka</option>
        <option value="manual 5rychlost" <?php if ($editVozidlo["prevodovka"]=="manual 5rychlost") echo "selected"; ?>>5</option>
        <option value="manual 6rychlost" <?php if ($editVozidlo["prevodovka"]=="manual 6rychlost") echo "selected"; ?>>6</option>
        <option value="automat" <?php if ($editVozidlo["prevodovka"]=="automat") echo "selected"; ?>>Auto</option>
    </select>

    <button type="submit" name="<?php echo $editMode ? "update_vozidlo" : "add_vozidlo"; ?>">
        <?php echo $editMode ? "Uložit" : "Pridat"; ?>
    </button>
        <?php if ($editMode) { ?>
            <button type="button" onclick="window.location.href='vozidla.php'">Zrušit úpravu</button>
        <?php } ?>

</form>

<?php } ?>

<br>
<button onclick="window.location.href='admin.php'">Zpět na hl. stránku</button>

</div>

</body>
</html>