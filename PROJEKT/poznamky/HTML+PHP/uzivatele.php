<?php
session_start();
include 'db.php';
if (
    !isset($_SESSION['username']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] != 'admin'
) {
    header("Location: index.php");
    exit();
}
$error = "";
$success = "";
$editMode = false;
$editUser = [
    "id" => "",
    "username" => "",
    "role" => ""
];

if (isset($_GET["delete"])) {
    $id = (int)$_GET["delete"];
    $stmt = mysqli_prepare($conn, "SELECT username FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $deleteUsername);
    if (mysqli_stmt_fetch($stmt)) {
        mysqli_stmt_close($stmt);
        if ($deleteUsername == $_SESSION["username"]) {
            $error = "Nemuzes smazat sam sebe.";
        } else {
            $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            header("Location: " . $_SERVER["PHP_SELF"]);
            exit();
        }
    } else {
        mysqli_stmt_close($stmt);
        $error = "Uzivatel nebyl nalezen.";
    }
}

if (isset($_GET["edit"])) {
    $id = (int)$_GET["edit"];
    $stmt = mysqli_prepare($conn, "SELECT id, username, role FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $uid, $uusername, $urole);
    if (mysqli_stmt_fetch($stmt)) {
        $editMode = true;
        $editUser = [
            "id" => $uid,
            "username" => $uusername,
            "role" => $urole
        ];
    }
    mysqli_stmt_close($stmt);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $role = trim($_POST["role"]);

    if (isset($_POST["update_user"])) {
        $id = (int)$_POST["id"];
        if ($username != "" && $role != "") {
            $stmt = mysqli_prepare($conn, "UPDATE users SET username = ?, role = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "ssi", $username, $role, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            header("Location: " . $_SERVER["PHP_SELF"]);
            exit();
        } else {
            $error = "Vypln vsechna pole.";
        }
    } else {
        $password = isset($_POST["password"]) ? trim($_POST["password"]) : "";
        if ($username != "" && $password != "" && $role != "") {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $username, $hashedPassword, $role);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            header("Location: " . $_SERVER["PHP_SELF"]);
            exit();
        } else {
            $error = "Vypln vsechna pole.";
        }
    }
}

$sql = "SELECT id, username, role FROM users ORDER BY id ASC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Správa uživatelů</title>
    <link rel="stylesheet" href="../CSS/styles.css">
</head>
<body>
<div class="container">
    <h1>Správa uživatelů</h1>
    <p>
        Vítejte,
        <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>.
    </p>

    <?php if ($error != "") { ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php } ?>
    <?php if ($success != "") { ?>
        <p class="success"><?php echo htmlspecialchars($success); ?></p>
    <?php } ?>

    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Uživatel</th>
            <th>Role</th>
            <th>Upravit</th>
            <th>Smazat</th>
        </tr>

        <?php if ($result && mysqli_num_rows($result) > 0) { ?>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row["id"]); ?></td>
                    <td><?php echo htmlspecialchars($row["username"]); ?></td>
                    <td><?php echo htmlspecialchars($row["role"]); ?></td>
                    <td>
                        <a href="uzivatele.php?edit=<?php echo $row["id"]; ?>">Upravit</a>
                    </td>
                    <td>
                        <a href="uzivatele.php?delete=<?php echo $row["id"]; ?>"
                           onclick="return confirm('Opravdu chcete smazat tohoto uzivatele?');">Smazat</a>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="5">Žádní uživatelé nebyli nalezeni.</td>
            </tr>
        <?php } ?>
    </table>
    <br>

    <?php if ($error != "") { ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php } ?>

    <h2><?php echo $editMode ? "Upravit uživatele" : "Přidat uživatele"; ?></h2>
    <form method="post" action="">
        <?php if ($editMode) { ?>
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($editUser["id"]); ?>">
        <?php } ?>
        <input
            type="text"
            name="username"
            placeholder="Username"
            value="<?php echo htmlspecialchars($editUser["username"]); ?>"
            required
        >
        <?php if (!$editMode) { ?>
            <input type="password" name="password" placeholder="Password" required>
        <?php } ?>
        <select name="role" required>
            <option value="admin" <?php if ($editUser["role"] == "admin") echo "selected"; ?>>Admin</option>
            <option value="user"  <?php if ($editUser["role"] == "user")  echo "selected"; ?>>User</option>
        </select>
        <button type="submit" name="<?php echo $editMode ? "update_user" : "add_user"; ?>">
            <?php echo $editMode ? "Uložit změny" : "Přidat uživatele"; ?>
        </button>
        <?php if ($editMode) { ?>
            <div class="button-row button-column">
                <button type="button" class="button1" onclick="window.location.href='uzivatele.php'">Zrušit úpravu</button>
            </div>
        <?php } ?>
    </form>
    <br>
    <button type="button" onclick="window.location.href='admin.php'">Zpět na hl. stránku</button>
</div>
</body>
</html>