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

$editRide = [
    "id" => "",
    "student_id" => "",
    "instructor_id" => "",
    "vozidlo_id" => "",
    "datum" => "",
    "cas" => ""
];

if (isset($_GET["delete"]) && $isAdmin) {

    $id = (int)$_GET["delete"];

    $stmt = mysqli_prepare($conn, "DELETE FROM jizdy WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: jizdyadmin.php");
    exit();
}


if (isset($_GET["edit"]) && $isAdmin) {

    $id = (int)$_GET["edit"];

    $stmt = mysqli_prepare($conn, "
        SELECT id, student_id, instructor_id, vozidlo_id, datum, cas
        FROM jizdy
        WHERE id = ?
    ");

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    mysqli_stmt_bind_result(
        $stmt,
        $jid,
        $student_id,
        $instructor_id,
        $vozidlo_id,
        $datum,
        $cas
    );

    if (mysqli_stmt_fetch($stmt)) {

        $editMode = true;

        $editRide = [
            "id" => $jid,
            "student_id" => $student_id,
            "instructor_id" => $instructor_id,
            "vozidlo_id" => $vozidlo_id,
            "datum" => $datum,
            "cas" => $cas
        ];
    }

    mysqli_stmt_close($stmt);
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && $isAdmin) {

    $student_id = (int)$_POST["student_id"];
    $instructor_id = (int)$_POST["instructor_id"];
    $vozidlo_id = (int)$_POST["vozidlo_id"];
    $datum = $_POST["datum"];
    $cas = $_POST["cas"];

    if ($student_id > 0 && $instructor_id > 0 && $vozidlo_id > 0 && $datum != "" && $cas != "") {

        if (isset($_POST["update_jizda"])) {

            $id = (int)$_POST["id"];

            $stmt = mysqli_prepare($conn, "
                UPDATE jizdy
                SET student_id = ?, instructor_id = ?, vozidlo_id = ?, datum = ?, cas = ?
                WHERE id = ?
            ");

            mysqli_stmt_bind_param(
                $stmt,
                "iiissi",
                $student_id,
                $instructor_id,
                $vozidlo_id,
                $datum,
                $cas,
                $id
            );

        } else {

            $stmt = mysqli_prepare($conn, "
                INSERT INTO jizdy (student_id, instructor_id, vozidlo_id, datum, cas)
                VALUES (?, ?, ?, ?, ?)
            ");

            mysqli_stmt_bind_param(
                $stmt,
                "iiiss",
                $student_id,
                $instructor_id,
                $vozidlo_id,
                $datum,
                $cas
            );
        }

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: jizdyadmin.php");
        exit();

    } else {
        $error = "Vypln vsechna pole.";
    }
}


$students = mysqli_query($conn, "SELECT id, jmeno, prijmeni FROM students");
$instructors = mysqli_query($conn, "SELECT id, jmeno, prijmeni FROM instructors");
$vozidla = mysqli_query($conn, "SELECT id, znacka, model, spz FROM vozidla");


$jizdy = mysqli_query($conn, "
    SELECT
        j.id,
        j.datum,
        j.cas,
        s.jmeno AS student_jmeno,
        s.prijmeni AS student_prijmeni,
        i.jmeno AS instruktor_jmeno,
        i.prijmeni AS instruktor_prijmeni,
        v.znacka,
        v.model,
        v.spz
    FROM jizdy j
    INNER JOIN students s ON j.student_id = s.id
    INNER JOIN instructors i ON j.instructor_id = i.id
    INNER JOIN vozidla v ON j.vozidlo_id = v.id
    ORDER BY j.datum ASC, j.cas ASC
");
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Správa jízd</title>
    <link rel="stylesheet" href="../CSS/styles.css">
</head>
<body>

<div class="container">

<h1>Správa jízd</h1>

<?php if ($error != "") { ?>
    <p><?php echo htmlspecialchars($error); ?></p>
<?php } ?>

<h2>Seznam jízd</h2>

<table border="1" cellpadding="8">

<tr>
    <th>ID</th>
    <th>Student</th>
    <th>Instruktor</th>
    <th>Vozidlo</th>
    <th>Datum</th>
    <th>Čas</th>
    <?php if ($isAdmin) { ?>
        <th>Upravit</th>
        <th>Smazat</th>
    <?php } ?>
</tr>

<?php while ($j = mysqli_fetch_assoc($jizdy)) { ?>
<tr>
    <td><?php echo $j["id"]; ?></td>
    <td><?php echo $j["student_prijmeni"] . " " . $j["student_jmeno"]; ?></td>
    <td><?php echo $j["instruktor_prijmeni"] . " " . $j["instruktor_jmeno"]; ?></td>
    <td><?php echo $j["znacka"] . " " . $j["model"]; ?></td>
    <td><?php echo $j["datum"]; ?></td>
    <td><?php echo substr($j["cas"], 0, 5); ?></td>

    <?php if ($isAdmin) { ?>
        <td><a href="?edit=<?php echo $j["id"]; ?>">Upravit</a></td>
        <td><a href="?delete=<?php echo $j["id"]; ?>" onclick="return confirm('Smazat?');">Smazat</a></td>
    <?php } ?>
</tr>
<?php } ?>
</table>
<br>
<?php if ($isAdmin) { ?>

<h2><?php echo $editMode ? "Upravit jizdu" : "Přidat jízdu"; ?></h2>

<form method="post">

    <?php if ($editMode) { ?>
        <input type="hidden" name="id" value="<?php echo $editRide["id"]; ?>">
    <?php } ?>

    <select name="student_id">
        <option value="">Student</option>
        <?php while ($s = mysqli_fetch_assoc($students)) { ?>
            <option value="<?php echo $s["id"]; ?>"
                <?php if ($s["id"] == $editRide["student_id"]) echo "selected"; ?>>
                <?php echo $s["prijmeni"] . " " . $s["jmeno"]; ?>
            </option>
        <?php } ?>
    </select>

    <select name="instructor_id">
        <option value="">Instruktor</option>
        <?php while ($i = mysqli_fetch_assoc($instructors)) { ?>
            <option value="<?php echo $i["id"]; ?>"
                <?php if ($i["id"] == $editRide["instructor_id"]) echo "selected"; ?>>
                <?php echo $i["prijmeni"] . " " . $i["jmeno"]; ?>
            </option>
        <?php } ?>
    </select>

    <select name="vozidlo_id">
        <option value="">Vozidlo</option>
        <?php while ($v = mysqli_fetch_assoc($vozidla)) { ?>
            <option value="<?php echo $v["id"]; ?>"
                <?php if ($v["id"] == $editRide["vozidlo_id"]) echo "selected"; ?>>
                <?php echo $v["znacka"] . " " . $v["model"]; ?>
            </option>
        <?php } ?>
    </select>

    <input type="date" name="datum" value="<?php echo $editRide["datum"]; ?>">
    <input type="time" name="cas" value="<?php echo substr($editRide["cas"], 0, 5); ?>">

    <div class="button-row">
        <button type="submit" name="<?php echo $editMode ? "update_jizda" : "add_jizda"; ?>">
            <?php echo $editMode ? "Uložit" : "Přidat"; ?>
        </button>
        <?php if ($editMode) { ?>
            <button type="button" onclick="window.location.href='jizdyadmin.php'">Zrušit</button>
        <?php } ?>
    </div>

</form>

<?php } ?>

<br>
<button onclick="window.location.href='admin.php'">Zpět na hl. stránku</button>

</div>

</body>
</html>