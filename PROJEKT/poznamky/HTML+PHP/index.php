<?php
session_start();

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

if (isset($_SESSION['username'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin.php");
    } else {
        header("Location: vitejte.php");
    }
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db.php';

    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['user_id'] = $user['id'];

            if ($user['role'] == 'admin') {
                header("Location: admin.php");
                exit();
            } else {
                header("Location: vitejte.php");
                exit();
            }
        } else {
            $error = "Neplatne prihlasovaci udaje.";
        }
    } else {
        $error = "Neplatne prihlasovaci udaje.";
    }
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prihlaseni</title>
    <link rel="stylesheet" href="../CSS/styles.css">
</head>
<body>
    <div class="page-center">
        <div class="card login-card">
            <h1>Vitejte</h1>
            <p class="subtitle">System pro spravu autoskoly</p>

            <?php if ($error != "") { ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php } ?>

            <form action="index.php" method="post" class="form-vertical">
                <input type="text" name="username" placeholder="Zadejte sve jmeno" required>
                <input type="password" name="password" placeholder="Zadejte sve heslo" required>
                <button type="submit">Prihlasit se</button>
            </form>
        </div>
    </div>
</body>
</html>